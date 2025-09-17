<?php
namespace App\Http\Controllers;

use App\Http\Requests\Account\AccountCreateUpdateRequest;
use App\Http\Requests\Transaction\TransactionCreateUpdateRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Services\BalanceService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
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
    public function index(Request $request): JsonResponse
    {
        $month = (int) $request->input('month', Carbon::now()->month);
        $year  = (int) $request->input('year', Carbon::now()->year);

        $transactions = $this->transactionService->getAll($request, $year, $month);

        if (app()->environment('local')) {
            usleep(2000000);
        }

        return response()->json($transactions);

    }

    /**
     * Store a newly created transaction in storage.
     *
     * @param  TransactionCreateUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */

    public function store(TransactionCreateUpdateRequest $request): JsonResponse
    {
        $this->transactionService->create($request);

        return response()->json([
            'message' => __('Transaction created successfully'),
        ], 201);
    }

    /**
     * Retrieve a specific transaction by its ID.
     *
     * @param string $transactionId The ID of the transaction to retrieve.
     * @return JsonResponse The transaction instance if found, otherwise a HTTP 404 response.
     * @throws ModelNotFoundException If the transaction is not found.
     */
    public function show(Request $request, $transactionId): JsonResponse
    {

        $transaction = Transaction::where([
            'user_id' => $request->user()->id,
            'id'      => $transactionId,

        ])
            ->with([
                'category'            => function ($query) {
                    $query->select(['id', 'name', 'color', 'icon']);
                },
                'account'             => function ($query): void {
                    $query->select(['id', 'description', 'color', 'institution_id']);
                },
                'account.institution' => function ($query): void {
                    $query->select(['id', 'name', 'type']);
                },
            ])
            ->get();

        if (! $transaction) {
            return response()->json([
                'message' => __('Transaction not found'),
            ], 404);

        }

        return response()->json($transaction);

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
