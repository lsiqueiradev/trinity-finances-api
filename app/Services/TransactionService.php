<?php
namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TransactionService
{

    /**
     * Validate transaction ownership by checking if the current user owns the transaction.
     *
     * @param string $transactionId The ID of the transaction to validate.
     * @param Request $request The HTTP request object containing user information.
     * @return JsonResponse|Transaction Returns the transaction if validation is successful, otherwise throws an exception.
     * @throws HttpResponseException If the transaction is not found or the user does not own the transaction.
     */
    public function validateOwnership(string $transactionId, Request $request): JsonResponse | Transaction
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);

            if ($transaction->user_id !== $request->user()->id) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => __('You do not have permission to access this resource.'),
                    ], 403)
                );
            }
            return $transaction;
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('You do not have permission to access this resource.'),
                ], 403)
            );
        }
    }

    /**
     * Create a new transaction for the current user.
     *
     * @param Request $request The HTTP request object containing transaction information.
     * @return Transaction The newly created transaction.
     */

    public function create(Request $request): Transaction | bool
    {
        if ($request->is_recurring) {
            return $this->createRecurring($request);
        }

        if ($request->is_installments) {
            return $this->createInstallments($request);
        }

        return $this->createSingle($request);
    }

    /**
     * Create a new transaction for the current user.
     *
     * @param Request $request The HTTP request object containing transaction information.
     * @return Transaction The newly created transaction.
     */

    private function createSingle(Request $request): Transaction
    {
        return Transaction::create(array_merge($request->all(), [
            'date'    => Carbon::parse($request->date)->setTimezone('UTC'),
            'user_id' => $request->user()->id,
        ]));
    }

    private function createRecurring(Request $request)
    {
        $recurrence = app(RecurrenceService::class)->create($request);

        $dates = $this->createDates(
            Carbon::parse($request->date),
            $request->frequency,
            null);

        dd($dates);

        return $recurrence;
    }

    private function createInstallments(Request $request)
    {
        $dates = $this->createDates(
            Carbon::parse($request->date),
            $request->frequency,
            $request->total_installments);

        $originalDescription = $request['description'];
        foreach ($dates as $i => $date) {
            $request['description'] = "{$originalDescription} (" . ($i + 1) . "/" . $request['total_installments'] . ")";

            $request['date']         = $date;
            $request['is_confirmed'] = $i === 0 && $request['is_confirmed'];

            $this->createSingle($request);
        }

        return false;

    }

    private function createDates(
        Carbon $startDate,
        string $frequency,
        ?int $installments = null
    ): array {
        $date  = $startDate->copy();
        $dates = [];
        if ($installments) {
            for ($i = 0; $i < $installments; $i++) {
                $dates[] = $date->copy();

                $date = match ($frequency) {
                    'weekly'  => $date->addWeek(),
                    'monthly' => $date->setDay(1)->addMonth(),
                    'yearly'  => $date->setDay(1)->addYear(),
                    default   => throw new \InvalidArgumentException('Frequência inválida para recorrências.'),
                };

                if (in_array($frequency, ['monthly', 'yearly'])) {
                    $date = adjustEndOfMonth($startDate->day, $date->month, $date->year);
                }
            }
        } else {
            $currentMonthStart = Carbon::createFromDate($date->year, $date->month, 1);
            $currentMonthEnd   = $currentMonthStart->copy()->endOfMonth();
            while ($date->lte($currentMonthEnd)) {
                $dates[] = $date->copy();

                $date = match ($frequency) {
                    'weekly'  => $date->addWeek(),
                    'monthly' => $date->setDay(1)->addMonth(),
                    'yearly'  => $date->setDay(1)->addYear(),
                    default   => throw new \InvalidArgumentException('Frequência inválida para recorrências.'),
                };

                if (in_array($frequency, ['monthly', 'yearly'])) {
                    $date = adjustEndOfMonth($startDate->day, $date->month, $date->year);
                }
            }
        }
        return $dates;
    }

    /**
     * Retrieve a list of all transactions for the current user, grouped by date.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param int $year The year to retrieve the transactions for.
     * @param int $month The month to retrieve the transactions for.
     * @return Collection A collection of transaction instances, each containing the date, accumulated balance, and a collection of transactions.
     */
    public function getAll(Request $request, int $year, int $month)
    {
        $orderBy      = $request->input('order', 'desc');
        $isDescending = $orderBy === 'desc';

        $query = Transaction::where([
            'user_id' => $request->user()->id,
        ])
            ->with([
                'category:id,name,color,icon',
                'account:id,description,color,institution_id',
                'account.institution:id,name,type,logo_path',
            ])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $previousMonthEnd   = Carbon::create($year, $month, 1)->subMonth()->endOfMonth();
        $accumulatedBalance = app(BalanceService::class)->calculateBalance($previousMonthEnd);

        $transactionsGrouped = $query->groupBy(function ($transaction) {
            return $transaction->date ? Carbon::parse($transaction->date)->format('Y-m-d') : null;
        })->filter(function ($group, $key) {
            return ! is_null($key);
        });

        $runningBalance = $accumulatedBalance;
        $balancesByDate = [];

        foreach ($transactionsGrouped as $date => $dailyTransactions) {
            $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');

            if (isset($balancesByDate[$previousDate])) {
                $runningBalance = $balancesByDate[$previousDate];
            }

            $dailyIncome  = $dailyTransactions->where('type', 'income')->sum('amount');
            $dailyExpense = $dailyTransactions->where('type', 'expense')->sum('amount');
            $runningBalance += ($dailyIncome - $dailyExpense);

            $balancesByDate[$date] = round($runningBalance, 2);

            $dailyTransactions->prepend((object) [
                'amount'      => $balancesByDate[$date],
                'date'        => Carbon::parse($date)->endOfDay()->setTimezone('UTC'),
                'description' => __('Estimated end of day balance'),
                'created_at'  => Carbon::parse($date)->endOfDay()->setTimezone('UTC'),
            ]);
        }

        $dates        = collect(array_keys($balancesByDate));
        $orderedDates = $isDescending ? $dates->sortDesc() : $dates->sort();

        $finalResult = collect();

        foreach ($orderedDates as $date) {
            $dailyTransactions = $transactionsGrouped[$date];

            $sortedDailyTransactions = $dailyTransactions->sort(function ($a, $b) use ($isDescending) {
                $dateA = Carbon::parse($a->date)->timestamp;
                $dateB = Carbon::parse($b->date)->timestamp;

                if ($dateA === $dateB) {
                    $timeA = $a->created_at->timestamp;
                    $timeB = $b->created_at->timestamp;
                    return $isDescending ? ($timeB - $timeA) : ($timeA - $timeB);
                }

                return $isDescending ? ($dateB - $dateA) : ($dateA - $dateB);
            })->values();

            $finalResult->push(...$sortedDailyTransactions);

        }

        return $finalResult;
    }

}
