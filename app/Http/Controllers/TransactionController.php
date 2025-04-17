<?php
namespace App\Http\Controllers;

use App\Http\Requests\Account\AccountCreateUpdateRequest;
use App\Http\Requests\Transaction\TransactionCreateUpdateRequest;
use App\Models\Account;
use App\Models\Category;
use App\Services\BalanceService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionController extends Controller
{

    protected $transactionService;
    protected $balanceService;

    public function __construct(TransactionService $transactionService, BalanceService $balanceService)
    {
        $this->transactionService = $transactionService;
        $this->balanceService     = $balanceService;
    }

    /**
     * Retrieve a list of all accounts for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year  = (int) $request->input('year', Carbon::now()->year);

        $transactions = $this->transactionService->getAll($request, $year, $month);

        return response()->json($transactions);

    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param  TransactionCreateUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(TransactionCreateUpdateRequest $request)
    {
        $this->transactionService->create($request);

        return response()->json([
            'message' => __('Transaction created successfully'),
        ], 201);
    }

    /**
     * Retrieve a single account by its ID.
     *
     * @param  string  $categoryId
     * @return \Illuminate\Http\Response
     */
    public function show(string $categoryId)
    {
        // try {
        //     return response()->json(Category::findOrFail($categoryId));
        // } catch (ModelNotFoundException $e) {
        //     return response()->json([
        //         'message' => __('You do not have permission to access this resource.'),
        //     ], 404);
        // }

    }

    /**
     * Update an existing account.
     *
     * @param  AccountCreateUpdateRequest  $request
     * @param  string  $accountId
     * @return \Illuminate\Http\Response
     */
    public function update(AccountCreateUpdateRequest $request, string $accountId)
    {
//         $accountUpdated = $this->accountService->updateAccount($request, $accountId);
//
//         if (!$accountUpdated) {
//             return response()->json([
//                 'message' => __('An error occurred while updating the account'),
//             ], 404);
//         }
//
//         return response()->json([
//             'message' => __('Account updated successfully'),
//         ]);

    }

    /**
     * Delete an existing account.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param string $accountId The ID of the account to delete.
     * @return \Illuminate\Http\Response
     */

    public function destroy(Request $request, string $accountId)
    {
//         $accountDeleted = $this->accountService->deleteAccount($request, $accountId);
//
//         if (!$accountDeleted) {
//             return response()->json([
//                 'message' => __('An error occurred while deleting the account'),
//             ], 404);
//         }
//
//         return response()->json([
//             'message' => __('Account deleted successfully'),
//         ]);

    }
}
