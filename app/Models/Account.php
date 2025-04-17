<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'user_id',
        'description',
        'initial_balance',
        'institution_id',
        'is_initial_screen',
        'is_archived',
    ];

    protected $hidden = [
        'institution_id',
        'user_id',
    ];

    protected $casts = [
        'initial_balance'   => 'float',
        'is_initial_screen' => 'boolean',
        'is_archived'       => 'boolean',
    ];

    protected $appends = [];

    /**
     * Get the user that owns the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the banking institution that owns the Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    /**
     * Get the transactions for the account.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }
}
