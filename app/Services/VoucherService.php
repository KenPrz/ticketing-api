<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Voucher;

class VoucherService
{
    /**
     * Get all active vouchers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActiveVouchers()
    {
        return Voucher::where(function ($query) {
            $query->whereNull('start_date')
                ->orWhere('start_date', '<=', now());
        })->where(function ($query) {
            $query->whereNull('end_date')
                ->orWhere('end_date', '>=', now());
        })->get();
    }

    /**
     * Check if a voucher is usable using the given code and event ID.
     * Verifies that the voucher is valid and belongs to the same organizer as the event.
     * 
     * @param  string  $code
     * @param  int|null  $eventId
     *
     * @return array|null
     */
    public function isUsable(string $code, ?int $eventId = null): ?array
    {
        $voucherQuery = Voucher::where('code', $code)
            ->where(function ($query) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
        
        $voucher = $voucherQuery->first();
        
        if (!$voucher) {
            return null;
        }
        
        // Check if event_id is provided and verify the organizer match
        if ($eventId !== null) {
            // Get the event
            $event = Event::find($eventId);
            
            // If event doesn't exist or organizer_id doesn't match, voucher is invalid
            if (!$event || $event->organizer_id !== $voucher->organizer_id) {
                return null;
            }
        }
        
        return [
            'id' => $voucher->id,
            'code' => $voucher->code,
            'name' => $voucher->name,
            'discount' => $voucher->discount,
            'start_date' => $voucher->start_date,
            'end_date' => $voucher->end_date,
            'organizer_id' => $voucher->organizer_id
        ];
    }

    /**
     * Get all vouchers for a given organizer.
     *
     * @param  int  $organizerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVouchersByOrganizer(int $organizerId)
    {
        return Voucher::where('organizer_id', $organizerId)->get();
    }

    /**
     * Create a new voucher.
     *
     * @param  array  $data
     * @return \App\Models\Voucher
     */
    public function createVoucher(array $data)
    {
        return Voucher::create($data);
    }

    /**
     * Update an existing voucher.
     *
     * @param  \App\Models\Voucher  $voucher
     * @param  array  $data
     * @return \App\Models\Voucher
     */
    public function updateVoucher(Voucher $voucher, array $data)
    {
        $voucher->update($data);
        return $voucher;
    }

    /**
     * Delete a voucher.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return bool|null
     */
    public function deleteVoucher(Voucher $voucher)
    {
        return $voucher->delete();
    }
}