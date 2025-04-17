<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\BalanceService;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class BalanceController extends Controller
{

    protected $balanceService;
    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;

    }

    /**
     * Retrieve a list of all accounts for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $balanceType)
    {

        $month    = (int) $request->input('month', Carbon::now()->month);
        $year     = (int) $request->input('year', Carbon::now()->year);
        $archived = filter_var($request->input('archived', false), FILTER_VALIDATE_BOOLEAN);

        switch ($balanceType) {
            case 'accounts':
                return $this->accounts($month, $year, $archived);
                break;
            case 'dashboard':
                return $this->dashboard($month, $year, $archived);
                break;
            case 'transactions':
                return $this->transactions($month, $year, $archived);
                break;
            default:
                throw new HttpResponseException(
                    response()->json([
                        'message' => __('You do not have permission to access this resource.'),
                    ], 403)
                );
                break;
        }
    }

    private function dashboard(int $month, int $year, $isArchived = false)
    {

        $now     = Carbon::now();
        $balance = 0;

        $monthStatus = $this->balanceService->getMonthStatus(Carbon::create($year, $month, 1), $now);
        if ($monthStatus === 'current') {
            $balance = $this->balanceService->calculateBalance($now, true, null, true, $isArchived);
        } else {
            $predictiveOrFinalDate = Carbon::create($year, $month, 1)->endOfMonth();
            $balance               = $this->balanceService->calculateBalance($predictiveOrFinalDate, null, null, true, $isArchived);
        }
        $monthlyBalancesTransactions = $this->balanceService->calculateBalanceMonthly($year, $month);

        return response()->json([
            'balance'        => $balance,
            'monthly_status' => $monthStatus,
            ...$monthlyBalancesTransactions,
        ]);

        return response()->json();
    }

    private function transactions(int $month, int $year, $isArchived = false)
    {
        $now     = Carbon::now();
        $balance = 0;

        $monthStatus = $this->balanceService->getMonthStatus(Carbon::create($year, $month, 1), $now);
        if ($monthStatus === 'current') {
            $balance = $this->balanceService->calculateBalance($now, true, null, null, $isArchived);
        } else {
            $predictiveOrFinalDate = Carbon::create($year, $month, 1)->endOfMonth();
            $balance               = $this->balanceService->calculateBalance($predictiveOrFinalDate, null, null, null, $isArchived);
        }
        $monthlyBalancesTransactions = $this->balanceService->calculateBalanceMonthly($year, $month);

        return response()->json([
            'balance'        => $balance,
            'monthly_status' => $monthStatus,
            ...$monthlyBalancesTransactions,
        ]);
    }

    private function accounts(int $month, int $year, $isArchived = false)
    {

        $now                   = Carbon::now();
        $predictiveOrFinalDate = Carbon::create($year, $month, 1)->endOfMonth();

        $currentBalance = $this->balanceService->calculateBalance($now, true, null, null, $isArchived);

        $predictedOrFinalBalance = $this->balanceService->calculateBalance($predictiveOrFinalDate, null, null, null, $isArchived);

        return response()->json([
            'current_balance'            => $currentBalance,
            'predicted_or_final_balance' => $predictedOrFinalBalance,
        ]);

    }
}
