<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@taxigo.com',
            'password' => 'admin123',
            'is_admin' => true,
        ]);

        $gallery = [
            '/asstes/images/taxi2.jpg',
            '/asstes/images/taxi3.jpg',
            '/asstes/images/images.jpeg',
            '/asstes/images/47f7a327-5fa8-47db-a183-d4f127272c07.png',
        ];

        $drivers = [
            [
                'name' => 'Alex Turner', 'location' => 'Downtown, Tozeur',
                'whatsapp' => '21600000000', 'phone' => '+216 00 000 000',
                'rating' => 4.9, 'reviews_count' => 212, 'trips' => 1240, 'years_experience' => 5,
                'work_start' => '07:00', 'work_end' => '22:00', 'work_days' => [1, 2, 3, 4, 5, 6],
                'avatar_color' => '2563eb',
                'taxi' => ['name' => 'Toyota Prius', 'year' => 2022, 'color' => '#FFFFFF', 'capacity' => '4 Passengers', 'luggage' => '3 Bags', 'type' => 'economy', 'image_gallery' => $gallery],
            ],
            [
                'name' => 'Lisa Anderson', 'location' => 'City Center, Tozeur',
                'whatsapp' => '21600000001', 'phone' => '+216 00 000 000',
                'rating' => 4.8, 'reviews_count' => 178, 'trips' => 980, 'years_experience' => 4,
                'work_start' => '08:00', 'work_end' => '20:00', 'work_days' => [1, 2, 3, 4, 5],
                'avatar_color' => '16a34a',
                'taxi' => ['name' => 'Mercedes C200', 'year' => 2021, 'color' => '#000000', 'capacity' => '4 Passengers', 'luggage' => '3 Bags', 'type' => 'comfort', 'image_gallery' => $gallery],
            ],
            [
                'name' => 'Robert Martinez', 'location' => 'Airport, Tozeur',
                'whatsapp' => '21600000002', 'phone' => '+216 00 000 000',
                'rating' => 4.7, 'reviews_count' => 145, 'trips' => 760, 'years_experience' => 7,
                'work_start' => '06:00', 'work_end' => '23:00', 'work_days' => [1, 2, 3, 4, 5, 6, 7],
                'avatar_color' => 'd97706',
                'taxi' => ['name' => 'Mercedes E200', 'year' => 2020, 'color' => '#C0C0C0', 'capacity' => '4 Passengers', 'luggage' => '3 Bags', 'type' => 'comfort', 'image_gallery' => $gallery],
            ],
            [
                'name' => 'Jessica White', 'location' => 'Palm Grove, Tozeur',
                'whatsapp' => '21600000003', 'phone' => '+216 00 000 000',
                'rating' => 4.9, 'reviews_count' => 198, 'trips' => 1120, 'years_experience' => 6,
                'work_start' => '10:00', 'work_end' => '18:00', 'work_days' => [2, 3, 4, 5, 6],
                'avatar_color' => 'db2777',
                'taxi' => ['name' => 'BMW 5 Series', 'year' => 2023, 'color' => '#000080', 'capacity' => '4 Passengers', 'luggage' => '3 Bags', 'type' => 'luxury', 'image_gallery' => $gallery],
            ],
        ];

        foreach ($drivers as $data) {
            $avatarColor = $data['avatar_color'];
            unset($data['avatar_color']);

            $taxiData = $data['taxi'];
            unset($data['taxi']);

            $driver = Driver::create([
                ...$data,
                'avatar' => 'https://ui-avatars.com/api/?name='.urlencode($data['name'])."&background={$avatarColor}&color=fff&size=128&rounded=true",
            ]);

            $driver->taxi()->create($taxiData);

            $this->seedReviews($driver, $avatarColor);
        }

        Booking::create([
            'customer_name' => 'Omar Haddad',
            'customer_phone' => '+216 51 234 567',
            'driver_id' => Driver::first()?->id,
            'pickup_location' => 'Tozeur Airport',
            'dropoff_location' => 'Dar Melaab Resort',
            'status' => 'confirmed',
            'fare' => 45.00,
        ]);

        Booking::create([
            'customer_name' => 'Nadia Bouaziz',
            'customer_phone' => '+216 98 765 432',
            'driver_id' => Driver::first()?->id,
            'pickup_location' => 'Hotel Sahara',
            'dropoff_location' => 'Chott El Jerid',
            'status' => 'completed',
            'fare' => 60.00,
        ]);

        Booking::create([
            'customer_name' => 'John Miller',
            'customer_phone' => '+216 55 111 222',
            'driver_id' => Driver::find(2)?->id,
            'pickup_location' => 'Palm Grove',
            'dropoff_location' => 'Tozeur City Center',
            'status' => 'pending',
            'fare' => 25.00,
        ]);
    }

    private function seedReviews(Driver $driver, string $color): void
    {
        $reviews = [
            ['Sara Ben', 5, 'Amazing ride! Very professional and the car was spotless. Highly recommend.'],
            ['Ahmed K', 4, 'Punctual and friendly. The ride was smooth and comfortable.'],
            ['Maria L', 5, 'Perfect service from start to finish. Great driver with excellent knowledge of the area.'],
            ['Youssef R', 5, 'On time, clean vehicle, and a very safe driver. Will book again.'],
            ['Emma S', 4, 'Comfortable journey and a courteous driver. Great value for money.'],
        ];

        foreach ($reviews as $r) {
            Review::create([
                'driver_id' => $driver->id,
                'user_name' => $r[0],
                'avatar_color' => $color,
                'rating' => $r[1],
                'comment' => $r[2],
            ]);
        }

        $driver->refresh();
        $count = $driver->reviews()->count();
        $sum = (float) $driver->reviews()->sum('rating');
        $driver->update([
            'reviews_count' => $count,
            'rating' => $count > 0 ? round($sum / $count, 1) : 0,
        ]);
    }
}
