<?php
namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountService
{

    /**
     * Validate account ownership by checking if the current user owns the account.
     *
     * @param string $accountId The ID of the account to validate.
     * @param Request $request The HTTP request object containing user information.
     * @return JsonResponse|Account Returns the account if validation is successful, otherwise throws an exception.
     * @throws HttpResponseException If the account is not found or the user does not own the account.
     */
    public function validateOwnership(string $accountId, Request $request): JsonResponse | Account
    {
        try {
            $account = Account::findOrFail($accountId);

            if ($account->user_id !== $request->user()->id) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => __('You do not have permission to access this resource.'),
                    ], 403)
                );
            }
            return $account;
        } catch (ModelNotFoundException $e) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('You do not have permission to access this resource.'),
                ], 403)
            );
        }
    }

    /**
     * Create a new account for the current user.
     *
     * @param Request $request The HTTP request object containing account information.
     * @return Account The newly created account.
     */
    public function create(Request $request): Account
    {
        return Account::create(array_merge($request->all(), [
            'user_id' => $request->user()->id,
        ]));
    }

    /**
     * Updates an existing account based on the given request.
     *
     * @param Request $request The HTTP request object containing account details.
     * @param string $accountId The ID of the account to update.
     * @return bool True if the update was successful, false otherwise.
     */

    public function update(Request $request, string $accountId): bool
    {
        return $this->validateOwnership($accountId, $request)->update($request->all());
    }

    /**
     * Deletes an existing account based on the given request.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param string $accountId The ID of the account to delete.
     * @return bool True if the deletion was successful, false otherwise.
     * @throws HttpResponseException If the account is not archived, or if the account is not found, or if the user does not own the account.
     */

    public function delete(Request $request, string $accountId): bool
    {
        $account = $this->validateOwnership($accountId, $request);

        if (! $account->is_archived) {
            throw new HttpResponseException(
                response()->json([
                    'message' => __('To delete the account, it must be archived first.'),
                ], 403)
            );

        }

        return $account->delete();
    }

    /**
     * Retrieves all accounts for the current user, including the current balance and
     * the predicted or final balance for the given year and month.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param int $year The year to retrieve the predicted or final balance for.
     * @param int $month The month to retrieve the predicted or final balance for.
     * @param bool $isArchived If true, only retrieve archived accounts.
     * @return Collection A collection of account instances, each with the current balance and the predicted or final balance.
     */
    public function getAll(Request $request, ?int $year = null, ?int $month = null, bool $isArchived = false): Collection
    {
        $now                  = Carbon::now();
        $predictedOrFinalDate = Carbon::create($year, $month, 1)->endOfMonth();

        $accounts = Account::where([
            'user_id'     => $request->user()->id,
            'is_archived' => $isArchived,
        ])
            ->with(['institution' => function ($query) {
                $query->select(['id', 'name', 'logo_path']);
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        $accounts->transform(function ($account) use ($now, $predictedOrFinalDate) {
            $account->current_balance            = app(BalanceService::class)->calculateBalance($now, true, $account->id, null);
            $account->predicted_or_final_balance = app(BalanceService::class)->calculateBalance($predictedOrFinalDate, null, $account->id, null);
            return $account;
        });

        return $accounts;
    }

    /**
     * Retrieve a single account by its ID, validating that the account belongs to the current user.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param string $accountId The ID of the account to retrieve.
     * @return Account The account instance if validation is successful, otherwise throws an exception.
     * @throws HttpResponseException If the account is not found or the user does not own the account.
     */
    public function get(Request $request, $accountId): Account
    {
        return $this->validateOwnership($accountId, $request);
    }

    /**
     * Archive or unarchive an account.
     *
     * @param Request $request The HTTP request object containing user information.
     * @param string $accountId The ID of the account to archive or unarchive.
     * @return bool True if the account was successfully archived or unarchived, false otherwise.
     * @throws HttpResponseException If the account is not found or the user does not own the account.
     */
    public function archive(Request $request, $accountId): bool
    {
        $status = filter_var($request->input('status', false), FILTER_VALIDATE_BOOLEAN);

        if ($status === null) {
            return response()->json([
                'message' => __('An error occurred while archiving the account, incorrect parameters'),
            ], 422);
        }

        return $this->validateOwnership($accountId, $request)->update(['is_archived' => $status]);
    }
}
