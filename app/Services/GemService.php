<?php

namespace App\Services;

use App\Models\Gem;
use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Exceptions\NotEnoughGems;
use App\Exceptions\UserNotFoundException;

class GemService
{

    /**
     * Add Gem to User
     *
     * @param integer $userId
     * @param integer $amount
     * @param string $tag
     * @return int $userTotalGems
     * @throws \Exception
     */
    public function add(int $userId, int $amount, string $tag): int
    {
        Transaction::create([
            'tag' => $tag,
            'user_id' => $userId,
            'amount' => $amount,
            'type' => TransactionType::Deposit,
        ]);

        $userTotalGems = $this->calculateTotalGems($userId);

        Gem::updateOrCreate(
            ['user_id' => $userId],
            ['quantity' => $userTotalGems]
        );

        return $userTotalGems;
    }


    /**
     * Spend User's Gems
     *
     * @param integer $userId
     * @param integer $amount
     * @param string $tag
     * @return int $userTotalGemsAfterSpend
     * @throws \Exception|\App\Exceptions\NotEnoughGems|\App\Exceptions\UserNotFoundException
     */
    public function spend(int $userId, int $amount, string $tag): int
    {
        $userTotalGems = $this->calculateTotalGems($userId);

        if ($userTotalGems < $amount)
            throw new NotEnoughGems("User does not have enough gems.");

        Transaction::create([
            'tag' => $tag,
            'user_id' => $userId,
            'amount' => $amount,
            'type' => TransactionType::Withdraw,
        ]);

        $userTotalGemsAfterSpend = $userTotalGems - $amount;

        Gem::updateOrCreate(
            ['user_id' => $userId],
            ['quantity' => $userTotalGemsAfterSpend]
        );

        return $userTotalGemsAfterSpend;
    }


    /**
     * Calculate total User's Gems.
     *
     * @param integer $userId
     * @return integer $totalGems
     * @throws UserNotFoundException
     */
    public function calculateTotalGems(int $userId): int
    {
        $transactions = Transaction::where('user_id', $userId);

        if (!$transactions->exists())
            throw new UserNotFoundException("User not found.");

        return $transactions->selectRaw("
                SUM(CASE WHEN type='" . TransactionType::Deposit->value . "' THEN amount ELSE 0 END) - 
                SUM(CASE WHEN type='" . TransactionType::Withdraw->value . "' THEN amount ELSE 0 END) AS balance")
            ->pluck('balance')->first();
    }
}
