<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Exceptions\GemNotAddedException;
use App\Exceptions\GemNotSpentException;
use App\Exceptions\NotEnoughGems;
use App\Models\Gem;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class GemService
{

    /**
     * Add Gem to User
     *
     * @param integer $userId
     * @param integer $amount
     * @param string $tag
     * @return int $userTotalGems
     * @throws GemNotAddedException
     */
    public function add(int $userId, int $amount, string $tag): int
    {
        $userTotalGems = Gem::getQuantity($userId) + $amount;

        try {
            DB::transaction(function () use ($userId, $amount, $tag, $userTotalGems) {

                Transaction::create([
                    'tag' => $tag,
                    'user_id' => $userId,
                    'amount' => $amount,
                    'type' => TransactionType::Deposit,
                ]);

                Gem::updateOrCreate(
                    ['user_id' => $userId],
                    ['quantity' => $userTotalGems]
                );

            });
        } catch (\Exception $ex) {
            throw new GemNotAddedException("Cannot add gems to User: {$ex->getMessage()}");
        }

        return $userTotalGems;
    }


    /**
     * Spend User's Gems
     *
     * @param integer $userId
     * @param integer $amount
     * @param string $tag
     * @return int $userTotalGemsAfterSpend
     * @throws NotEnoughGems|GemNotSpentException
     */
    public function spend(int $userId, int $amount, string $tag): int
    {
        $userTotalGems = Gem::getQuantity($userId);

        if ($userTotalGems < $amount)
            throw new NotEnoughGems("User does not have enough gems.");

        $userTotalGemsAfterSpend = $userTotalGems - $amount;

        try {
            DB::transaction(function () use ($userId, $amount, $tag, $userTotalGemsAfterSpend) {
                Transaction::create([
                    'tag' => $tag,
                    'user_id' => $userId,
                    'amount' => $amount,
                    'type' => TransactionType::Withdraw,
                ]);

                Gem::findByUserId($userId)->update([
                    'quantity' => $userTotalGemsAfterSpend
                ]);
            });
        } catch (\Exception $ex) {
            throw new GemNotSpentException("Cannot spend User's gems: {$ex->getMessage()}");
        }

        return $userTotalGemsAfterSpend;
    }

}
