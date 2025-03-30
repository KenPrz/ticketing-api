<?php

namespace App\Http\Controllers;

use App\Helpers\DateFormatterHelper;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QrVerifyController extends Controller
{
    /**
     * Verify the QR code.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyQr(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'qr_code' => 'required|string',
        ]);

        // Extract the QR code from the request
        $qrCode = $request->input('qr_code');

        // Check if the QR code is valid
        $isValid = $this->verifyQrCode($qrCode);

        if ($isValid) {
            $data = Ticket::with(['seat','event','event.banner'])
                ->where('qr_code', $qrCode)
                ->first()
                ->toArray();

            $barCode = explode('--', $data['qr_code'], 2);

            return response()->json([
                'message' => 'QR code is valid.',
                'ticket_data' => [
                    'account_code' => $data['owner_id'],
                    'ticket_code' => $barCode[0],
                    'date' => DateFormatterHelper::dayFull($data['event']['date']),
                    'time' => $data['event']['time'],
                    'event' => [
                        'banner' => $data['event']['banner']['image_url'],
                        'name' => $data['event']['name'],
                        'date' => DateFormatterHelper::dayFull($data['event']['date']),
                        'venue' => $data['event']['venue'],
                    ],
                    'seat' => [
                        'section' => $data['seat']['section'],
                        'row_and_seat' => $data['seat']['row'].'-'.$data['seat']['number'],
                    ],
                ],
                
            ], 200);
        } else {
            return response()->json(['message' => 'Invalid QR code.'], 404);
        }
    }

    /**
     * Verify the Barcode code.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyBarcode(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'barcode' => 'required|string',
        ]);

        // Extract the Barcode code from the request
        $string = $request->input('barcode');
        $parts = explode('--', $string, 2);
        // Check if the Barcode code is valid
        $isValid = $this->checkBarCode($parts[0]);

        if ($isValid) {
            return response()->json(['message' => 'Barcode code is valid.'], 200);
        } else {
            return response()->json(['message' => 'Invalid Barcode code.'], 404);
        }
    }

    /**
     * Verify the Bar code against the database.
     *
     * @param string $code the Bar code to verify
     *
     * @return bool if bar_code exists
     */
    public function checkBarCode(string $code)
    {
        return DB::table('tickets')
            ->whereRaw("SUBSTRING_INDEX(qr_code, '--', 1) = ?", [$code])
            ->exists();
    }

    /**
     * Verify the QR code against the database.
     *
     * @param string $qrCode the QR code to verify
     *
     * @return bool if qr_code exists
     */
    private function verifyQrCode($qrCode)
    {
        return Ticket::where('qr_code', $qrCode)->exists();
    }
}
