<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PackingSpace;
use App\Models\PackingSpacePricing;
use App\Services\AvailabilityService;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PackingController extends Controller
{

    private $availabilityService;
    private $bookingService;

    public function __construct(AvailabilityService $availabilityService, BookingService $bookingService)
    {
        $this->availabilityService = $availabilityService;
        $this->bookingService = $bookingService;
    }

    public function getAvailability(Request $request): JsonResponse
    {
        $validatedData = $this->validateDateRange($request);

        $availableSpaces = $this->availabilityService->getAvailableSpaces($validatedData['start_date'], $validatedData['end_date']);

        return response()->json(['available_spaces' => $availableSpaces]);
    }

    public function createBooking(Request $request): JsonResponse
    {
        $validatedData = $this->validateBookingRequest($request);

        if(!$this->availabilityService->checkAvailability(
            $validatedData["parking_space_id"],
            $validatedData["start_date"],
            $validatedData["end_date"]
        )) {
            return response()->json(['message' => 'Space is occupied'], 400);
        }

        $booking = $this->bookingService->createBooking($validatedData);

        return response()->json(['message' => 'Booking created successfully']);
    }

    public function cancelBooking($bookingId): JsonResponse
    {
        $this->bookingService->cancelBooking($bookingId);

        return response()->json(['message' => 'Booking cancelled successfully']);
    }

    public function amendBooking(Request $request, $bookingId): JsonResponse
    {   
      
        $validatedData = $this->validateBookingRequest($request);


        if(!$this->availabilityService->checkAvailability(
            $validatedData["parking_space_id"],
            $validatedData["start_date"],
            $validatedData["end_date"],
            $bookingId
        )) {
            return response()->json(['message' => 'Failed to amend the booking. New dates are not available'], 400);
        }

        if ($this->bookingService->amendBooking($bookingId, $validatedData)) {
            return response()->json(['message' => 'Booking amended successfully']);
        }

        return response()->json(['error' => 'Failed to amend the booking. New dates are not available'], 400);
    }
   
   

    private function validateDateRange(Request $request): array
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        if (!$startDate->isFuture()) {
            throw new \InvalidArgumentException('Start date must be in the future');
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];
    }

    private function validateBookingRequest(Request $request): array
    {
        $this->validate($request, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'customer_name' => 'required',
            'customer_email' => 'required|email',
            'parking_space_id' => 'required',
        ]);

        return [
            'start_date' => Carbon::parse($request->start_date),
            'end_date' => Carbon::parse($request->end_date),
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'parking_space_id' => $request->parking_space_id,
        ];
    }

   

    public function getPriceSummary(Request $request) {
        $this->validate($request, [
            'start_date' => 'required',
            'end_date' => 'required',
            'parking_space_id' => 'required'
        ]);


        try {
            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid date format'], 400);
        }
       
        
        if(!$startDate->isFuture()) {
            return response()->json(['error' => 'Start date in past'], 400);

        }

        if ($startDate->greaterThan($endDate)) {
            return response()->json(['error' => 'Invalid date range'], 400);

        }

        $amount = $this->bookingService->calculateParkingPrice($request->parking_space_id,$request->start_date, $request->end_date);

        return response()->json(['amount' => $amount], 200);
        
    }


    public function getBookings() {
        $bookings = Booking::all();
        return response()->json(['bookings' => $bookings], 200);

    }

}

