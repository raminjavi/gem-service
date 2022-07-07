<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'quantity'];


    /**
     * Get User's total gems
     *
     * @param int $userId
     * @return int $totalUserGems
     */
    public static function getQuantity(int $userId): int
    {
        $gem = self::where('user_id', $userId)->first();
        return $gem ? $gem->quantity : 0;
    }

    /**
     * @param int $userId
     * @return Model $gem
     */
    public static function findByUserId(int $userId): Model
    {
        return self::where('user_id', $userId)->first();
    }
}
