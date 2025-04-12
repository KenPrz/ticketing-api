<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckTransferEmailRequest;
use App\Http\Requests\TransferTicketRequest;
use App\Models\TicketTransferHistory;
use App\Services\TransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class TransferController extends Controller
{
    /**
     * The transfer service instance.
     *
     * @var \App\Services\TransferService
     */
    protected $transferService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\TransferService  $transferService
     * @return void
     */
    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }

    /**
     * Check if the email is valid for transfer.
     *
     * @param  \App\Http\Requests\CheckTransferEmailRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkTransferEmailValidity(CheckTransferEmailRequest $request): JsonResponse
    {
        $result = $this->transferService->checkEmailValidity(
            $request->email,
            $request->user()
        );

        return response()->json(
            ['valid' => $result['valid'], 'message' => $result['message'] ?? null],
            $result['status']
        );
    }

    /**
     * Initiate a ticket transfer to another user.
     *
     * @param  \App\Http\Requests\TransferTicketRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function transferTicket(TransferTicketRequest $request): JsonResponse
    {
        $result = $this->transferService->transferTicket(
            $request->ticket_id,
            $request->email,
            $request->user()
        );

        return response()->json(
            ['message' => $result['message']],
            $result['status']
        );
    }
    
    /**
     * Process a ticket transfer acceptance from a signed URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $transferId
     * @return \Illuminate\Http\JsonResponse | \Illuminate\View\View
     */
    public function acceptTransfer(Request $request, int $transferId): JsonResponse | \Illuminate\View\View
    {
        if (!$request->hasValidSignature()) {
            return response()->json(
                ['message' => 'Invalid or expired signature'],
                403
            );
        }
        
        $result = $this->transferService->acceptTransfer($transferId);
        
        if ($request->expectsJson()) {
            return response()->json(
            ['message' => $result['message']],
            $result['status']
            );
        }

        return view('accepted');
    }
    
    /**
     * Process a ticket transfer rejection from a signed URL.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $transferId
     * @return \Illuminate\Http\JsonResponse | \Illuminate\View\View
     */
    public function rejectTransfer(Request $request, int $transferId): JsonResponse | \Illuminate\View\View
    {
        if (!$request->hasValidSignature()) {
            return response()->json(
                ['message' => 'Invalid or expired signature'],
                403
            );
        }
        
        $result = $this->transferService->rejectTransfer($transferId);
        
        if ($request->expectsJson()) {
            return response()->json(
                ['message' => $result['message']],
                $result['status']
            );
        }

        return view('rejected');
    }

    /**
     * Cancel a ticket transfer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $transferId
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelTicketTransfer(Request $request): JsonResponse
    {
        $transferId = $request->input('transfer_id');
        $user = $request->user();
        $result = $this->transferService->cancelTransfer(
            $transferId,
            $user,
        );

        return response()->json(
            ['message' => $result['message']],
            $result['status']
        );
    }
}
