<?php

namespace App\Services;

use App\Models\{
    Purchase,
    Ticket,
    Seat,
    User,
};
use Illuminate\Database\Eloquent\{
    Collection,
    ModelNotFoundException,
};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseService 
{
    /**
     * The ticket model instance.
     *
     * @var Purchase
     */
    protected $purchase;

    /**
     * Construct the ticket service instance.
     *
     * @param Purchase $purchase The ticket model instance
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * Get the purchases of the authenticated user.
     *
     * @param \App\Models\User $user
     *
     * @return Collection<int, Purchase>
     */
    public function getMyPurchases(User $user): Collection
    {
        return $this->purchase->where('user_id', $user->id)
            ->get();
    }

    /**
     * Get the purchase of the authenticated user.
     *
     * @param \App\Models\User $user
     * @param int $purchaseId
     *
     * @return Purchase
     */
    public function getPurchase(User $user, int $purchaseId): Purchase
    {
        $purchase = $this->purchase->where('user_id', $user->id)
            ->where('id', $purchaseId)
            ->first();

        if (!$purchase) {
            throw new ModelNotFoundException('Purchase not found');
        }

        return $purchase;
    }

    /**
     * Create a new purchase.
     *
     * @param \App\Models\User $user
     * @param array $data
     *
     * @return Purchase
     */
    public function createPurchase(User $user, array $data): Purchase
    {
        $seatIds = array_values($data['seat_ids']);
        return DB::transaction(function () use ($user, $seatIds) {

            foreach ($seatIds as $seatId) {
                $seat = Seat::findOrFail($seatId);

                $purchase = $this->purchase->create([
                    'placeholder_for_transaction_handler' => $this->createMockTransactionId(),
                    'event_id' => $seat->event_id,
                    'purchased_by' => $user->id,
                ]);

                $ticket = Ticket::create([
                    'qr_code' => $this->generateQrDetails(),
                    'ticket_name' => "{$purchase->event->name} - {$user->name} - {$seat->section->value}",
                    'event_id' => $seat->event_id,
                    'owner_id' => $user->id,
                    'purchase_id' => $purchase->id,
                    'ticket_tier_id' => $seat->getTicketTierEvenIfNoTicket()->id,
                    'ticket_type' => $seat->section,
                    'ticket_desc' => "Purchased by {$user->name} on {$purchase->created_at}",
                    'is_used' => false,
                    'used_on' => null,
                ]);

                $seat->is_occupied = true;
                $seat->ticket_id = $ticket->id;
                $seat->save();
            }

            return $purchase;
        });
    }

    /**
     * Create a new seat for a ticket.
     *
     * @param Ticket $ticket
     * @param int $eventId
     *
     * @return Seat
     */
    private function createMockTransactionId(): string
    {
        return 'TXN_' . uniqid() . Str::random(32);
    }

    /**
     * Generate QR code details for a ticket.
     *
     * @return string A string containing the event name, ticket owner name, event date, and a unique code.
     */
    private function generateQrDetails(): string
    {
        return uniqid()."--".Str::random(32);
    }
}