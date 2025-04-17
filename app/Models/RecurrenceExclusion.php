<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RecurrenceExclusion extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id',
        'recurrence_id',
        'date',
    ];

    protected $hidden = [
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
