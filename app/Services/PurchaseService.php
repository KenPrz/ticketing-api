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

    public function createPurchase(User $user, array $data): Purchase
    {
        return DB::transaction(function () use ($user, $data) {
            $purchase = $this->purchase->create([
                'placeholder_for_transaction_handler' => 'transaction_id',
                'event_id' => $data['event_id'],
                'purchased_by' => $user->id,
            ]);

            collect($data['tickets'])->map(function ($ticket) use ($purchase) {
                return $purchase->tickets()->create([
                    'qr_code' => $ticket['qr_code'],
                    'ticket_name' => $ticket['ticket_name'],
                    'event_id' => $ticket['event_id'],
                    'owner_id' => $ticket['owner_id'],
                    'ticket_tier_id' => $ticket['ticket_tier_id'],
                    'ticket_type' => $ticket['ticket_type'],
                    'ticket_desc' => $ticket['ticket_desc'],
                    'is_used' => false,
                    'used_on' => null,
                ]);
            });

            return $purchase;
        });
    }
}