<?php
namespace App\Services;

use App\Models\Recurrence;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RecurrenceService
{

    public function create(Request $request)
    {
        return Recurrence::create(array_merge($request->all(), [
            'date'    => Carbon::parse($request->date)->setTimezone('UTC'),
            'user_id' => $request->user()->id,
        ]));
    }
}
