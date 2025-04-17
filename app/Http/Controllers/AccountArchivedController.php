<?php
namespace App\Http\Controllers;

use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountArchivedController extends Controller
{

    protected $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $accountId)
    {
        $accountArchived = $this->accountService->archive($request, $accountId);

        if (! $accountArchived) {
            return response()->json([
                'message' => __('An error occurred while archiving the account'),
            ], 404);
        }

        return response()->json([
            'message' => __('Account archived successfully'),
        ]);
    }
}
