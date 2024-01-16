<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\PackingSpace;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    public function getAvailableSpaces(Carbon $startDate, Carbon $endDate): Collection
    {
        $bookings = $this->getBookingsInDateRange($startDate, $endDate);
        $takenSpaceIds = $bookings->pluck('parking_space_id')->toArray();

        return PackingSpace::whereNotIn('id', $takenSpaceIds)->get();
    }

    private function getBookingsInDateRange(Carbon $startDate, Carbon $endDate): Collection
    {
        return Booking::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })->where('status', 'active')->groupBy('parking_space_id')->select('parking_space_id')->get();
    }

    public function checkAvailability($packingSpaceId, $startDate, $endDate, $bookingId = null): bool
    {
        try {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } catch (\Exception $e) {
            return false;
        }

        if (!$startDate->isFuture() && !$startDate->lessThan($endDate)) {
            return false;
        }

        $overlappingBookings = Booking::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })->where(["parking_space_id" => $packingSpaceId, "status" => "active"])
        ->when($bookingId != null, function($query) use($bookingId) {
            $query->where("id","<>",$bookingId);
        })
        ->get();

        return count($overlappingBookings) == 0;
    }

}
