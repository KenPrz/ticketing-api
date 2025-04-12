<?php

namespace App\Http\Controllers;

use App\Helpers\DateFormatterHelper;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // Import JsonResponse for type hinting
use Illuminate\Support\Facades\DB; // Keep if checkBarCode is used elsewhere, otherwise removable

class QrVerifyController extends Controller
{
    /**
     * Verify the QR code and return ticket details.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyQr(Request $request): JsonResponse
    {
        $user = $request->user();
        // Validate the incoming request
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Extract the QR code from the request
        $qrCode = $request->input('qr_code');

        // Find the ticket by the full QR code
        $ticket = Ticket::with(['seat', 'event', 'event.banner'])
            ->where('qr_code', $qrCode)
            ->first(); // Use first() to get the model instance or null

        if ($ticket) {
            $isUserOrganizer = $user->isOrganizer();
            if ($isUserOrganizer) {
                $isAllowedToMarkAsUsed = $ticket->event->organizer_id === $request->user()->id;
            }
            // Use a helper method to format the response
            return $this->formatTicketResponse(
                $ticket,
                'QR code is valid.',
                $isAllowedToMarkAsUsed ?? false,
            );
        } else {
            return response()->json(['message' => 'Invalid QR code.'], 404);
        }
    }

    /**
     * Verify the Barcode code and return ticket details.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyBarcode(Request $request): JsonResponse
    {
        $user = $request->user();
        // Validate the incoming request
        $request->validate([
            'barcode' => 'required|string',
        ]);

        // Extract the Barcode code input from the request
        $barcodeInput = $request->input('barcode');

        // IMPORTANT ASSUMPTION: The 'barcode' input *might* be the full string ('barcode--uuid')
        // or just the barcode part. We will primarily use the part before '--' for lookup,
        // consistent with the original checkBarCode logic.
        $parts = explode('--', $barcodeInput, 2);
        $barcodePart = $parts[0]; // The part before '--' is assumed to be the barcode

        // Find the ticket where the first part of qr_code matches the barcode part
        $ticket = Ticket::with(['seat', 'event', 'event.banner'])
            ->whereRaw("SUBSTRING_INDEX(qr_code, '--', 1) = ?", [$barcodePart])
            ->first(); // Use first() to get the model instance or null

        if ($ticket) {
            $isUserOrganizer = $user->isOrganizer();
            if ($isUserOrganizer) {
                $isAllowedToMarkAsUsed = $ticket->event->organizer_id === $request->user()->id;
            }
             // Use a helper method to format the response
            return $this->formatTicketResponse(
                $ticket,
                'Barcode is valid.',
                $isAllowedToMarkAsUsed ?? false,
            );
        } else {
             // Optional: As a fallback, check if the input *was* the full QR code string
             $ticketFallback = Ticket::with(['seat', 'event', 'event.banner'])
                ->where('qr_code', $barcodeInput)
                ->first();

             if ($ticketFallback) {
                 return $this->formatTicketResponse($ticketFallback, 'Barcode input matched full QR code.');
             }

            // If neither lookup worked, the barcode is invalid
            return response()->json(['message' => 'Invalid Barcode or QR code.'], 404);
        }
    }

    /**
     * Formats the ticket data into the standard JSON response.
     *
     * @param Ticket $ticket The Eloquent Ticket model instance.
     * @param string $message The success message to include.
     * @return JsonResponse
     */
    private function formatTicketResponse(
        Ticket $ticket,
        string $message,
        bool $isAllowedToMarkAsUsed = false,
    ): JsonResponse {
        $isTicketUsed = $ticket->is_used;

        if ($isTicketUsed) {
            $message = 'This ticket has already been used.';
        }

        // Convert the ticket and its loaded relations to an array
        $data = $ticket->toArray();

        // Extract the actual barcode part from the stored qr_code for the response
        $barCodeParts = explode('--', $data['qr_code'], 2);
        $actualBarcode = $barCodeParts[0];

        return response()->json([
            'message' => $message,
            'is_allowed_to_mark_as_used' => $isTicketUsed 
                ? false
                : $isAllowedToMarkAsUsed,
            'is_ticket_used' => $isTicketUsed,
            'ticket_data' => [
                'id' => $data['id'],
                'account_code' => $data['owner_id'],
                'ticket_code' => $actualBarcode, // Use the barcode part from the stored data
                'date' => DateFormatterHelper::dayFull($data['event']['date']),
                'time' => $data['event']['time'],
                'event' => [
                    // Use null coalescing operator for safety if banner relationship might be missing
                    'banner' => $data['event']['banner']['image_url'] ?? null,
                    'name' => $data['event']['name'],
                    'date' => DateFormatterHelper::dayFull($data['event']['date']),
                    'venue' => $data['event']['venue'],
                ],
                'seat' => [
                    'section' => $data['seat']['section'] ?? null,
                    'row_and_seat' => $data['seat']['row'] . '-' . $data['seat']['number'],
                ],
            ],
        ], 200);
    }

    /**
     * Verify the Bar code against the database (checks existence only).
     *
     * @param string $code the Bar code to verify
     *
     * @return bool if bar_code exists
     */
    public function checkBarCode(string $code): bool
    {
        // Ensure DB facade is imported: use Illuminate\Support\Facades\DB;
        return DB::table('tickets')
            ->whereRaw("SUBSTRING_INDEX(qr_code, '--', 1) = ?", [$code])
            ->exists();
    }

    /**
     * Verify the QR code against the database (checks existence only).
     *
     * @param string $qrCode the QR code to verify
     *
     * @return bool if qr_code exists
     */
    private function verifyQrCode($qrCode): bool
    {
        return Ticket::where('qr_code', $qrCode)->exists();
    }
}