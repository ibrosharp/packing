<?php

namespace Database\Factories;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'start_date' => Carbon::now()->toDateString(),
            'end_date' => Carbon::now()->addDays(3)->toDateString(),
            'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->email,
            'amount' => $this->faker->randomFloat(2, 10, 100),
            'parking_space_id' => 1,
            'status' => 'active',
        ];
    }
}
