<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasUuids;

    protected $fillable = [
        'objective_id',
        'amount',
        'date',
    ];

    protected $hidden = [
        'objective_id',
    ];

    protected function casts(): array
    {
        return [
            'date'   => 'datetime',
            'amount' => 'float',
        ];
    }

    public function objective()
    {
        return $this->belongsTo(Objective::class);
    }

}
