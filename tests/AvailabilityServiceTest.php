<?php

namespace Tests\Unit\Services;

use App\Models\Booking;
use App\Models\PackingSpace;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Database\Factories\BookingFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @var AvailabilityService */
    private $availabilityService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->availabilityService = new AvailabilityService();
    }

    /** @test */
    public function it_can_get_available_spaces()
    {
        
        $booking = BookingFactory::new()->create([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 'active',
        ]);

        
        $startDate = now()->addDays(2);
        $endDate = now()->addDays(3);

        $availableSpaces = $this->availabilityService->getAvailableSpaces($startDate, $endDate);

        
        $this->assertNotContains($booking->parking_space_id, $availableSpaces->pluck('id'));
    }

    /** @test */
    public function it_can_check_availability()
    {
       
        $booking = BookingFactory::new()->create([
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay(),
            'status' => 'active',
        ]);

     
        $startDate = now();
        $endDate = now()->addDays(2);

        
        $isAvailable = $this->availabilityService->checkAvailability($booking->parking_space_id, $startDate, $endDate);

        $this->assertFalse($isAvailable);
    }
}
