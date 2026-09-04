<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AppFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_lists_drivers(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Alex Turner');
        $response->assertSee('Available Taxis');
    }

    public function test_driver_profile_shows_reviews(): void
    {
        $driver = Driver::first();

        $response = $this->get(route('drivers.show', $driver));

        $response->assertStatus(200);
        $response->assertSee($driver->name);
        $response->assertSee('Vehicle Details');
    }

    public function test_visitor_can_submit_review(): void
    {
        $driver = Driver::first();

        $response = $this->post(route('drivers.reviews.store', $driver), [
            'user_name' => 'Test Reviewer',
            'rating' => 5,
            'comment' => 'Great ride!',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'driver_id' => $driver->id,
            'user_name' => 'Test Reviewer',
        ]);

        $driver->refresh();
        $this->assertEquals($driver->reviews()->count(), $driver->reviews_count);
    }

    public function test_admin_can_hide_and_delete_review(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();
        $review = Review::create([
            'driver_id' => $driver->id,
            'user_name' => 'Hidden Review',
            'rating' => 4,
            'comment' => 'Temporary',
            'is_visible' => true,
        ]);

        $this->actingAs($user)->put(route('dashboard.reviews.toggle', $review))->assertRedirect();
        $this->assertFalse($review->fresh()->is_visible);

        $this->actingAs($user)->delete(route('dashboard.reviews.destroy', $review))->assertRedirect();
        $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
    }

    public function test_dashboard_redirects_guests_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_admin_can_access_all_management_pages(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();

        $pages = [
            route('dashboard.taxis') => 'Manage Taxis',
            route('dashboard.drivers') => 'Drivers',
            route('dashboard.reviews') => 'Reviews',
            route('dashboard.settings') => 'Settings',
        ];

        foreach ($pages as $url => $heading) {
            $this->actingAs($user)->get($url)
                ->assertStatus(200)
                ->assertSee($heading);
        }
    }

    public function test_non_admin_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/dashboard')->assertStatus(403);
    }

    public function test_admin_can_access_dashboard(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();

        $this->actingAs($user)->get('/dashboard')
            ->assertStatus(200)
            ->assertSee('Dashboard Overview');
    }

    public function test_login_allows_admin(): void
    {
        $this->post('/login', [
            'email' => 'admin@taxigo.com',
            'password' => 'admin123',
        ])->assertRedirect('/dashboard');
    }

    public function test_admin_can_add_driver(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();

        $this->actingAs($user)->post('/dashboard/drivers', [
            'name' => 'New Driver',
            'years_experience' => 3,
        ])->assertRedirect();

        $this->assertDatabaseHas('drivers', ['name' => 'New Driver']);
    }

    public function test_admin_can_add_driver_with_avatar(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();

        $avatar = UploadedFile::fake()->image('avatar.png');

        $this->actingAs($user)->post('/dashboard/drivers', [
            'name' => 'Driver With Photo',
            'avatar' => $avatar,
        ])->assertRedirect();

        $driver = Driver::where('name', 'Driver With Photo')->firstOrFail();
        $this->assertNotNull($driver->avatar);
    }

    public function test_admin_can_add_taxi_with_image(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();

        $car = UploadedFile::fake()->image('car.png');

        $this->actingAs($user)->post('/dashboard/taxis', [
            'driver_id' => $driver->id,
            'name' => 'Mercedes Vito',
            'type' => 'luxury',
            'image' => $car,
        ])->assertRedirect();

        $this->assertDatabaseHas('taxis', ['driver_id' => $driver->id, 'name' => 'Mercedes Vito']);
        $this->assertNotNull($driver->taxi->image);
    }

    public function test_admin_can_delete_taxi(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();

        $driver->taxi()->create(['name' => 'Temp Taxi', 'type' => 'van']);

        $this->actingAs($user)->delete("/dashboard/taxis/{$driver->id}")->assertRedirect();

        $this->assertNull($driver->fresh()->taxi);
    }

    public function test_admin_can_toggle_driver_active(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();
        $original = $driver->is_active;

        $this->actingAs($user)->put("/dashboard/drivers/{$driver->id}", [
            'name' => $driver->name,
            'is_active' => $original ? 0 : 1,
        ])->assertRedirect();

        $this->assertDatabaseHas('drivers', ['id' => $driver->id, 'is_active' => ! $original]);
    }

    public function test_admin_can_delete_driver(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();

        $this->actingAs($user)->delete("/dashboard/drivers/{$driver->id}")->assertRedirect();

        $this->assertDatabaseMissing('drivers', ['id' => $driver->id]);
    }

    public function test_admin_can_update_profile(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();

        $this->actingAs($user)->post('/dashboard/profile', [
            'name' => 'Admin Updated',
            'email' => $user->email,
            'password' => '',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Admin Updated']);
    }
}
