<?php
namespace App\Http\Controllers;

use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountBalanceController extends Controller
{
    protected $balanceService;

    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    public function update(Request $request, string $accountId): JsonResponse
    {
        $this->balanceService->update($request, $accountId);
        return response()->json(['message' => __('Account balance updated successfully.')]);
    }
}
