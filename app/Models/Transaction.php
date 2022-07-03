<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'amount', 'tag', 'type'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = ['type' => TransactionType::class];
}
