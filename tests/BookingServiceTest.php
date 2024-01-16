<?php

namespace Tests\Unit\Services;

use App\Models\Booking;
use App\Models\PackingSpacePricing;
use App\Services\BookingService;
use Carbon\Carbon;
use Database\Factories\BookingFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_create_a_booking()
    {
        $bookingService = new BookingService();

        $booking = BookingFactory::new()->create();

        $data = [
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(3)->toDateString(),
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'parking_space_id' => 1,
        ];

        $booking = $bookingService->createBooking($data);

        $this->assertInstanceOf(Booking::class, $booking);
        $this->assertEquals($data['start_date'], $booking->start_date);
        $this->assertEquals($data['end_date'], $booking->end_date);
        $this->assertEquals($data['customer_name'], $booking->customer_name);
        $this->assertEquals($data['customer_email'], $booking->customer_email);
        $this->assertEquals($data['parking_space_id'], $booking->parking_space_id);
    }

    /** @test */
    public function it_can_cancel_a_booking()
    {
        $bookingService = new BookingService();

        $booking = BookingFactory::new()->create();

        $bookingService->cancelBooking($booking->id);

        $this->assertEquals('cancelled', $booking->fresh()->status);
    }

}
