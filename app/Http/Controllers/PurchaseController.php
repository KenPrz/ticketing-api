<?php

namespace App\Http\Controllers;

use App\Http\Resources\PurchaseTicketResource;
use App\Services\{
    EventService,
    PurchaseService,
    SeatService,
};
use App\Services\TicketTierService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * @var PurchaseService $purchaseService An instance of the PurchaseService used to handle ticket-related operations.
     */
    protected $purchaseService;

    /**
     * @var EventService $eventService An instance of the EventService used to handle event-related operations.
     */
    protected $eventService;

    /**
     * @var SeatService $seatService An instance of the EventService used to handle event-related operations.
     */
    protected $seatService;

    /**
     * @var TicketTierService $ticketTierService An instance of the TicketTierService used to handle ticket tier-related operations.
     */
    protected $ticketTierService;

    /**
     * TicketController constructor.
     *
     * @param PurchaseService $purchaseService The service used to handle ticket-related operations.
     * @param EventService $eventService The service used to handle event-related operations.
     * @param SeatService $seatService The service used to handle seat-related operations.
     * @param TicketTierService $ticketTierService The service used to handle ticket tier-related operations.
     */
    public function __construct(
        PurchaseService $purchaseService,
        EventService $eventService,
        SeatService $seatService,
        TicketTierService $ticketTierService
    ) {
        $this->purchaseService = $purchaseService;
        $this->eventService = $eventService;
        $this->seatService = $seatService;
        $this->ticketTierService = $ticketTierService;
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
     * Show the Purchase screen for a specific event.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $eventTicketTierId
     *
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function showPurchaseScreen(
        Request $request,
        int $eventTicketTierId
    ) {
        try {
            $seats = $this->ticketTierService->fetchSeatsByTicketTierID($eventTicketTierId);

            $data = PurchaseTicketResource::make($seats)
                ->response()
                ->getData(true);

            return response()
                ->json(
                    $data,
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
