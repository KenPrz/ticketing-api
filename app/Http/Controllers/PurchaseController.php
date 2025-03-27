<?php

namespace App\Http\Controllers;

use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * @var PurchaseService $purchaseService An instance of the PurchaseService used to handle ticket-related operations.
     */
    protected $purchaseService;

    /**
     * TicketController constructor.
     *
     * @param PurchaseService $purchaseService The service used to handle ticket-related operations.
     */
    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    /**
     * Get the Purchase of the authenticated user.
     * 
     * @param Request $request The request object
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $purchases = $this->purchaseService->getMyPurchases($request->user());

            return response()
                ->json(
                    ['purchases' => $purchases],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Show the Purchase of the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        try {
            $purchase = $this->purchaseService->getPurchase($request->user(), $id);

            return response()
                ->json(
                    ['purchase' => $purchase],
                    200
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param Request $request The request object
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {
            $data = $request->validated();

            $purchase = $this->purchaseService->createPurchase(
                $request->user(),
                $data
            );

            return response()
                ->json(
                    ['purchase' => $purchase],
                    201
                );
        } catch (\Exception $e) {
            return response()
                ->json(
                    ['message' => $e->getMessage()],
                    400
                );
        }
    }
}
