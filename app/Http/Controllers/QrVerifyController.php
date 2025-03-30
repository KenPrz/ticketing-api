<?php

namespace App\Http\Controllers;

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
            return response()->json(['message' => 'QR code is valid.'], 200);
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
