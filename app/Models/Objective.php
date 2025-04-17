<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'date',
        'initial_amount',
        'target_amount',
        'icon',
        'color',
        'status',
    ];

    protected $hidden = [
        'user_id',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'current_amount',
        'remaining_amount',
        'frequency',
        'percentage_remaining_amount',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
        ];
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'objective_id');
    }

    public function totalDeposits(): float
    {
        return $this->deposits()->sum('amount');
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function currentAmount(): Attribute
    {
        return Attribute::get(function (): float {
            return $this->initial_amount + $this->totalDeposits();

        });
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function remainingAmount(): Attribute
    {
        return Attribute::get(function (): float {
            return max($this->target_amount - $this->current_amount, 0);
        });
    }

    public function percentageRemainingAmount(): Attribute
    {
        return Attribute::get(function (): float {
            return round(100 - ($this->remaining_amount / $this->target_amount) * 100, 2);
        });
    }

    public function frequency(): Attribute
    {
        return Attribute::get(function (): array {
            $remainingAmount = $this->remaining_amount;
            $today           = now();
            $endDate         = Carbon::parse($this->date);

            $daysRemaining = $today->diffInDays($endDate, false);

            if ($daysRemaining <= 1) {
                return [
                    'amount'  => $remainingAmount,
                    'message' => 'Você precisa poupar hoje',
                ];
            }

            $weeksRemaining  = $today->diffInWeeks($endDate);
            $monthsRemaining = $today->diffInMonths($endDate);

            if ($daysRemaining > 1 && $daysRemaining <= 7) {
                $dayContribution = $remainingAmount / floor($daysRemaining);
                return [
                    'amount'  => $dayContribution,
                    'message' => 'Você precisa poupar a cada dia',
                ];
            }

            if ($daysRemaining < 90) {
                $weeklyContribution = $remainingAmount / floor($weeksRemaining);
                return [
                    'amount'  => round($weeklyContribution, 2),
                    'message' => 'Você precisa poupar a cada semana',
                ];
            }

            $monthlyContribution = $remainingAmount / floor($monthsRemaining);
            return [
                'amount'  => round($monthlyContribution, 2),
                'message' => 'Você precisa poupar a cada mês',
            ];

        });
    }
}
