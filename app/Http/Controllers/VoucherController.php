<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVoucherRequest;
use App\Http\Requests\UpdateVoucherRequest;
use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * The voucher service instance.
     *
     * @var \App\Services\VoucherService
     */
    protected $voucherService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\VoucherService $voucherService
     *
     * @return void
     */
    public function __construct(VoucherService $voucherService)
    {
        $this->voucherService = $voucherService;
    }

    /**
     * Get the list of vouchers.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isOrganizer()) {
            $vouchers = $this->voucherService->getVouchersByOrganizer($user->id);
        } elseif ($user->isClient()) {
            $vouchers = $this->voucherService->getActiveVouchers();
        } else {
            $vouchers = Voucher::all();
        }

        return response()->json(['data' => $vouchers]);
    }

    /**
     * Store a newly created voucher in storage.
     *
     * @param \App\Http\Requests\StoreVoucherRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreVoucherRequest $request)
    {
        $organizer = $request->user();
        $voucher = $this->voucherService->createVoucher($request->validated());

        return response()->json([
            'message' => 'Voucher created successfully',
            'data' => $voucher
        ], 201);
    }

    /**
     * Display the specified voucher.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $voucher = Voucher::findOrFail($id);

        return response()->json(['data' => $voucher]);
    }

    /**
     * Update the specified voucher in storage.
     *
     * @param \App\Http\Requests\UpdateVoucherRequest $request
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateVoucherRequest $request, string $id)
    {
        $voucher = Voucher::findOrFail($id);
        $updatedVoucher = $this->voucherService->updateVoucher($voucher, $request->validated());

        return response()->json([
            'message' => 'Voucher updated successfully',
            'data' => $updatedVoucher
        ]);
    }

    /**
     * Remove the specified voucher from storage.
     *
     * @param string $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $voucher = Voucher::findOrFail($id);
        $this->voucherService->deleteVoucher($voucher);

        return response()->json([
            'message' => 'Voucher deleted successfully'
        ], 200);
    }

    /**
     * Check if a voucher is valid by code and optionally for a specific event.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $code
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkVoucher(Request $request, string $code)
    {
        $eventId = $request->input('event_id');
        
        $voucherDetails = $this->voucherService->isUsable($code, $eventId ? (int)$eventId : null);
        $isUsable = $voucherDetails !== null;

        $response = [
            'valid' => $isUsable,
            'message' => $isUsable
                ? 'Voucher is valid and can be used for this event'
                : ($eventId
                    ? 'Voucher is invalid or cannot be used for this event' 
                    : 'Voucher is invalid or expired'),
            'discount' => $isUsable ? $voucherDetails['discount'] : null,
            'name' => $isUsable ? $voucherDetails['name'] : null,
            'voucher' => $isUsable ? $voucherDetails : null
        ];
        
        if ($eventId) {
            $response['event_id'] = $eventId;
        }

        return response()->json($response);
    }
}