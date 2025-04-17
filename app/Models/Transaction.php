<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'description',
        'observation',
        'amount',
        'type',
        'date',
        'frequency',
        'recurrence_id',
        'total_installments',
        'current_installments',
        'installments_id',
        'is_recurring',
        'is_installments',
        'is_confirmed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'account_id',
        'user_id',
        'category_id',
        'recurrence_id',
        'installments_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_confirmed'    => 'boolean',
            'is_recurring'    => 'boolean',
            'is_installments' => 'boolean',
            'date'            => 'datetime',
            'amount'          => 'float',
        ];
    }

    /**
     * Get the account that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    /**
     * Get the category that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user that owns the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
