<?php
namespace App\Http\Controllers;

use App\Http\Requests\Account\AccountCreateUpdateRequest;
use App\Models\Account;
use App\Services\AccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    /**
     * Retrieve a list of all accounts for the current user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $month    = (int) $request->input('month', Carbon::now()->month);
        $year     = (int) $request->input('year', Carbon::now()->year);
        $archived = filter_var($request->input('archived', false), FILTER_VALIDATE_BOOLEAN);

        $accounts = app(AccountService::class)->getAll($request, $year, $month, $archived);

        return response()->json($accounts);
    }

    /**
     * Create a new account in storage.
     *
     * @param  AccountCreateUpdateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountCreateUpdateRequest $request)
    {
        app(AccountService::class)->create($request);

        return response()->json([
            'message' => __('Account created successfully'),
        ], 201);
    }

    /**
     * Retrieve a single account by its ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $accountId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, string $accountId)
    {
        $account = app(AccountService::class)->get($request, $accountId);
        return response()->json($account);
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
        $accountUpdated = app(AccountService::class)->update($request, $accountId);

        if (! $accountUpdated) {
            return response()->json([
                'message' => __('An error occurred while updating the account'),
            ], 404);
        }

        return response()->json([
            'message' => __('Account updated successfully'),
        ]);

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
        $accountDeleted = app(AccountService::class)->delete($request, $accountId);

        if (! $accountDeleted) {
            return response()->json([
                'message' => __('An error occurred while deleting the account'),
            ], 404);
        }

        return response()->json([
            'message' => __('Account deleted successfully'),
        ]);

    }
}
