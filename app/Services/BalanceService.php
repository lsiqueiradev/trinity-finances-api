<?php
namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BalanceService
{

    public function update(Request $request, string $accountId)
    {
        $account = app(AccountService::class)->validateOwnership($accountId, $request);

        $currentBalance       = $this->calculateBalance(null, true, $accountId, null);
        $currentBalanceUpdate = $request->current_balance;

        if ($request->type === 'transaction') {
            $this->createBalanceAdjustmentTransaction($request, $accountId, $currentBalance, $currentBalanceUpdate);
        } else {
            $this->adjustInitialBalance($account, $currentBalance, $currentBalanceUpdate);
        }
    }

    public function calculateBalance(
        ?Carbon $date = null,
        ?bool $isConfirmed = null,
        ?string $accountId = null,
        ?bool $isInitialScreen = null,
        ?bool $isArchived = null
    ): float {
        $query = Transaction::query()->where('user_id', Auth::id())
            ->where(function ($query) use ($accountId, $isInitialScreen, $isArchived) {
                $query->whereHas('account', function ($accountQuery) use ($accountId, $isInitialScreen, $isArchived) {
                    if ($isArchived !== null) {
                        $accountQuery->where('is_archived', $isArchived);
                    }
                    if ($accountId !== null) {
                        $accountQuery->where('id', $accountId);
                    }

                    if ($isInitialScreen !== null) {
                        $accountQuery->where('is_initial_screen', $isInitialScreen);
                    }
                });
            });

        if ($isConfirmed !== null) {
            $query->where('is_confirmed', $isConfirmed);
        }

        if ($date) {
            $query->where('date', '<=', $date);
        }

        $transactions = $query->get();

        $initialBalance = $this->getInitialBalance($accountId, $isInitialScreen, $isArchived);
        $incomes        = $transactions->where('type', 'income')->sum('amount');
        $expenses       = $transactions->where('type', 'expense')->sum('amount');

        return round($initialBalance + $incomes - $expenses, 2);
    }

    public function calculateBalanceMonthly(int $year, int $month): array
    {
        $transactions = Transaction::where([
            'user_id' => Auth::id(),
        ])
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $incomes  = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');
        $balance  = round($incomes - $expenses, 2);

        return [
            'total_incomes'   => $incomes,
            'total_expenses'  => $expenses,
            'monthly_balance' => $balance,
        ];
    }

    private function createBalanceAdjustmentTransaction(Request $request, string $accountId, float $currentBalance, float $currentBalanceUpdate): void
    {
        $isIncome   = $currentBalanceUpdate > $currentBalance;
        $type       = $isIncome ? 'income' : 'expense';
        $amountDiff = abs($currentBalanceUpdate - $currentBalance);

        $category = Category::where([
            'type'      => $type,
            'is_system' => true,
            'code'      => 'adjustment_balance',
        ])->first();

        $request->merge([
            'account_id'   => $accountId,
            'category_id'  => $category->id,
            'type'         => $type,
            'date'         => Carbon::now(),
            'description'  => $request->description ?? __('Balance adjustment') . '*',
            'amount'       => $amountDiff,
            'is_confirmed' => true,
        ]);

        app(TransactionService::class)->create($request);
    }

    /**
     * Returns a string describing whether the given date is in the past, future
     * or current month relative to the given $now date.
     *
     * @param Carbon $selectedDate
     * @param Carbon $now
     * @return string 'past', 'current', or 'future'
     */
    public function getMonthStatus(Carbon $selectedDate, Carbon $now): string
    {
        if ($selectedDate->month === $now->month && $selectedDate->year === $now->year) {
            return 'current';
        }

        return $selectedDate->lt($now->startOfMonth()) ? 'past' : 'future';
    }

    /**
     * Retrieve the initial balance of an account or accounts for the current user.
     *
     * @param string|null $accountId Optional account ID to filter the balance query.
     * @param bool|null $isInitialScreen Optional flag to filter accounts marked as initial screen.
     * @return float The sum of initial balances.
     */

    private function getInitialBalance(
        ?string $accountId = null,
        ?bool $isInitialScreen = null,
        ?bool $isArchived = null
    ): float {
        $query = Account::query()->where('user_id', Auth::id());

        if ($isArchived !== null) {
            $query->where('is_archived', $isArchived);
        }

        if ($accountId !== null) {
            $query->where('id', $accountId);
        }

        if ($isInitialScreen !== null) {
            $query->where('is_initial_screen', $isInitialScreen);
        }

        return $query->sum('initial_balance');

    }

    private function adjustInitialBalance(Account $account, float $currentBalance, float $currentBalanceUpdate): void
    {
        $balanceDiff = round($currentBalanceUpdate - $currentBalance, 2);
        $account->initial_balance += $balanceDiff;
        $account->save();
    }

}
