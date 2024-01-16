<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\PackingSpacePricing;
use Carbon\Carbon;

class BookingService
{
    public function createBooking(array $data): Booking
    {
    

        $amount = $this->calculateParkingPrice($data['parking_space_id'], $data['start_date'], $data['end_date']);

        return Booking::create([
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'amount' => $amount,
            'parking_space_id' => $data['parking_space_id']
        ]);
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = Booking::find($bookingId);

        if ($booking) {
            $booking->update(['status' => 'cancelled']);
        }
    }

    public function amendBooking(int $bookingId, array $data): bool
    {
        $booking = Booking::find($bookingId);

        if ($booking) {
            $newAmount = $this->calculateParkingPrice($booking->parking_space_id, $data['start_date'], $data['end_date']);

            $booking->update([
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'amount' => $newAmount,
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email']
            ]);

            return true;
        }

        return false;
    }



    public function calculateParkingPrice($packingSpaceId, $startDate, $endDate): float
    {
        $parking = PackingSpacePricing::where("parking_space_id", $packingSpaceId)->first();

        $startDate = Carbon::parse($startDate);
        $endDate = Carbon::parse($endDate);

        $weekdayPrice = $parking->weekday_price;
        $weekendPrice = $parking->weekend_price;
        $summerPriceMultiplier = $parking->summer_price_multiplier;

        $totalPrice = 0;

        // Loop through each day in the date range
        for ($currentDate = $startDate; $currentDate->lte($endDate); $currentDate->addDay()) {
            $isWeekend = in_array($currentDate->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY]);

            // Check if the current day is in summer (assuming summer is from June 1 to August 31)
            $isSummer = $currentDate->month >= 6 && $currentDate->month <= 8;

          
            if ($isWeekend) {
                $totalPrice += $weekendPrice;
            } else {
                $totalPrice += $weekdayPrice;
            }

            if ($isSummer) {
                $totalPrice *= $summerPriceMultiplier;
            }
        }

        return $totalPrice;
    }
}
