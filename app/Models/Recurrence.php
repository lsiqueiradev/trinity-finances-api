<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Recurrence extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'category_id',
        'account_id',
        'date',
        'amount',
        'description',
        'observation',
        'type',
        'frequency',
        'is_active',
    ];

    protected $hidden = [
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'date'      => 'date',
        'amount'    => 'float',
        'is_active' => 'boolean',
    ];
}
