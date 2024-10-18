<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCateringSubscriptionRequest;
use App\Http\Resources\Api\CateringSubscriptionApiResource;
use App\Models\CateringPackage;
use App\Models\CateringSubscription;
use App\Models\CateringTier;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CateringSubscriptionController extends Controller
{
    public function store(StoreCateringSubscriptionRequest $request) {
        $validateData       = $request->validated();
        $cateringPackage    = CateringPackage::find($validateData['catering_package_id']);
        if (!$cateringPackage) {
            return json_encode(['message' => 'Package not found'], 404);
        }
        
        $cateringTier   = CateringTier::find($validateData['catering_tier_id']);
        if (!$cateringTier) {
            return json_encode(['message' => 'Tier Package not found'], 404);
        }

        // Handle File Upload
        if ($request->hasFile('proof')) {
            $filePath               = $request->file('proof')->store('payment/proofs', 'public');
            $validateData['proof']  = $filePath;
        }

        // Calculate ended date
        $startedAt  = Carbon::parse($validateData['started_at']);
        $endedAt    = $startedAt->copy()->addDays($cateringTier->duration);

        $price      = $cateringTier->price;
        $tax        = 0.11;
        $totalTax   = $price * $tax;
        $grandTotal = $price + $totalTax;

        $validateData['price']            = $price;
        $validateData['total_tax_amount'] = round($totalTax,2);
        $validateData['total_amount']     = round($grandTotal,2);
        $validateData['quantity']         = $cateringTier->quantity;
        $validateData['duration']         = $cateringTier->duration;
        $validateData['city']             = $cateringPackage->city->name;
        $validateData['delivery_time']    = "Lunch time";
        $validateData['started_at']       = $startedAt->format('Y-m-d');
        $validateData['ended_at']         = $endedAt->format('Y-m-d');
        $validateData['is_paid']          = false;
        $validateData['booking_trx_id']   = CateringSubscription::generateUniqueTrxId();

        $bookingTransaction = CateringSubscription::create($validateData); 
        $bookingTransaction->load(['cateringPackage', 'cateringTier']);
        return new CateringSubscriptionApiResource($bookingTransaction);
    }

    public function booking_details(Request $request) {
        $request->validate([
            'phone'             => 'required|string',
            'booking_trx_id'    => 'required|string',
        ]);

        $booking    = CateringSubscription::where('phone', $request->phone)
        ->where('booking_trx_id', $request->booking_trx_id)
        ->with(['cateringPackage', 'cateringPackage.kitchen', 'cateringTier'])
        ->first();

        if (!$booking) {
            return json_encode(['message' => 'Booking not found'], 404);
        }
        return new CateringSubscriptionApiResource($booking);
    }
}
