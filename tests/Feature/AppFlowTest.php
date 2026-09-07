<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
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

        $response = $this->get(route('drivers.show', ['driver' => $driver]));

        $response->assertStatus(200);
        $response->assertSee($driver->name);
        $response->assertSee('Vehicle Details');
    }

    public function test_visitor_can_submit_review(): void
    {
        $driver = Driver::first();

        $response = $this->post(route('drivers.reviews.store', ['driver' => $driver]), [
            'user_name' => 'Test Reviewer',
            'rating' => 5,
            'comment' => 'Great ride!',
            'g-recaptcha-response' => 'dummy-token',
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
            route('dashboard.taxis.create') => 'Add Taxi',
            route('dashboard.drivers') => 'Drivers',
            route('dashboard.drivers.create') => 'Add Driver',
            route('dashboard.reviews') => 'Reviews',
            route('dashboard.settings') => 'Settings',
        ];

        foreach ($pages as $url => $heading) {
            $this->actingAs($user)->get($url)
                ->assertStatus(200)
                ->assertSee($heading);
        }

        $this->actingAs($user)->get(route('dashboard.drivers.edit', Driver::first()))
            ->assertStatus(200)
            ->assertSee('Edit Driver');

        $this->actingAs($user)->get(route('dashboard.taxis.edit', Driver::first()))
            ->assertStatus(200)
            ->assertSee('Edit Taxi');
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
        $upload = $this->actingAs($user)->post('/dashboard/uploads/avatar', ['file' => $avatar]);
        $upload->assertOk()->assertJsonStructure(['path', 'url']);

        $this->actingAs($user)->post('/dashboard/drivers', [
            'name' => 'Driver With Photo',
            'avatar' => $upload->json('path'),
        ])->assertRedirect();

        $driver = Driver::where('name', 'Driver With Photo')->firstOrFail();
        $this->assertNotNull($driver->avatar);
    }

    public function test_admin_can_save_driver_working_hours(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();

        $this->actingAs($user)->post('/dashboard/drivers', [
            'name' => 'Scheduled Driver',
            'years_experience' => 2,
            'work_start' => '09:00',
            'work_end' => '18:00',
            'work_days' => [1, 2, 3, 4, 5],
        ])->assertRedirect();

        $this->assertDatabaseHas('drivers', [
            'name' => 'Scheduled Driver',
            'work_start' => '09:00',
            'work_end' => '18:00',
        ]);
    }

    public function test_working_hours_appear_on_home_and_profile(): void
    {
        $driver = Driver::first();
        $driver->update(['work_start' => '09:00', 'work_end' => '18:00', 'work_days' => [1, 2, 3, 4, 5]]);

        $this->get('/')->assertSee($driver->working_hours);
        $this->get(route('drivers.show', ['driver' => $driver]))->assertSee($driver->working_hours);
    }

    public function test_working_hours_respect_days_and_time(): void
    {
        $driver = Driver::first();
        $driver->update([
            'work_start' => '01:00',
            'work_end' => '23:30',
            'work_days' => [1, 2, 3],
        ]);

        $monday = Carbon::parse('next monday 12:00');
        $sunday = Carbon::parse('next sunday 12:00');
        $mondayNight = Carbon::parse('next monday 23:45');
        $mondayEarly = Carbon::parse('next monday 00:30');

        $this->assertTrue($driver->isWorkingNow($monday));
        $this->assertFalse($driver->isWorkingNow($sunday));
        $this->assertFalse($driver->isWorkingNow($mondayNight));
        $this->assertFalse($driver->isWorkingNow($mondayEarly));
    }

    public function test_admin_can_add_taxi_with_multiple_images(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();

        $gallery = $this->actingAs($user)->post('/dashboard/uploads/taxi-gallery', [
            'images' => [
                UploadedFile::fake()->image('car1.png'),
                UploadedFile::fake()->image('car2.png'),
                UploadedFile::fake()->image('car3.png'),
                UploadedFile::fake()->image('car4.png'),
            ],
        ]);
        $gallery->assertOk()->assertJsonStructure(['paths', 'urls']);

        $this->actingAs($user)->post('/dashboard/taxis', [
            'driver_id' => $driver->id,
            'name' => 'Mercedes E200',
            'type' => 'luxury',
            'images' => $gallery->json('paths'),
        ])->assertRedirect();

        $taxi = $driver->fresh()->taxi;
        $this->assertNotNull($taxi);
        $this->assertCount(4, $taxi->getImages());
        $this->assertDatabaseHas('taxis', ['driver_id' => $driver->id, 'name' => 'Mercedes E200']);
    }

    public function test_admin_cannot_add_taxi_with_less_than_four_images(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();

        $response = $this->actingAs($user)->post('/dashboard/taxis', [
            'driver_id' => $driver->id,
            'name' => 'Mercedes C300',
            'type' => 'luxury',
            'images' => ['/uploads/taxis/car1.jpg', '/uploads/taxis/car2.jpg'],
        ]);

        $response->assertSessionHasErrors('images');
        $this->assertNotSame('Mercedes C300', $driver->fresh()->taxi?->name);
    }

    public function test_driver_page_renders_vehicle_gallery(): void
    {
        $driver = Driver::first();
        $taxi = $driver->taxi;
        $taxi->update(['image_gallery' => ['/asstes/images/taxi2.jpg', '/asstes/images/taxi3.jpg']]);

        $response = $this->get(route('drivers.show', ['driver' => $driver]));

        $response->assertStatus(200);
        $response->assertSee('vehicle-main-image', false);
        $response->assertSee('vehicle-thumbnails', false);
    }

    public function test_admin_can_toggle_taxi_availability(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $taxi = Driver::first()->taxi;

        $this->assertTrue($taxi->is_active);

        $this->actingAs($user)->put(route('dashboard.taxis.availability', $taxi))->assertRedirect();

        $this->assertFalse($taxi->fresh()->is_active);
    }

    public function test_home_page_hides_driver_with_unavailable_taxi(): void
    {
        $driver = Driver::first();
        $driver->taxi->update(['is_active' => false]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee($driver->name);
    }

    public function test_admin_can_delete_taxi(): void
    {
        $user = User::where('email', 'admin@taxigo.com')->firstOrFail();
        $driver = Driver::first();

        $driver->taxi()->create(['name' => 'Temp Taxi', 'type' => 'comfort']);

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

    public function test_sitemap_lists_home_and_drivers(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<loc>', false);
        $response->assertSee(route('drivers.show', ['driver' => Driver::first()]), false);
    }

    public function test_home_page_contains_seo_schema_and_meta(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('TaxiService', false);
        $response->assertSee('WebSite', false);
        $response->assertSee('name="description"', false);
        $response->assertSee('rel="canonical"', false);
        $response->assertSee('property="og:title"', false);
    }

    public function test_driver_page_contains_rating_schema(): void
    {
        $driver = Driver::first();

        $response = $this->get(route('drivers.show', ['driver' => $driver]));

        $response->assertStatus(200);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('AggregateRating', false);
        $response->assertSee('BreadcrumbList', false);
        $response->assertSee('"reviewCount"', false);
    }

    public function test_driver_page_shows_no_reviews_yet_when_no_reviews(): void
    {
        $driver = Driver::first();
        $driver->reviews()->delete();
        $driver->update(['rating' => 0, 'reviews_count' => 0]);

        $response = $this->get(route('drivers.show', ['driver' => $driver]));

        $response->assertStatus(200);
        $response->assertSee('No reviews yet');
        $response->assertDontSee('(0 reviews)');
    }

    public function test_home_page_ranks_drivers_by_rating(): void
    {
        Driver::query()->update(['rating' => 3.0]);
        $top = Driver::first();
        $top->update(['rating' => 5.0, 'name' => 'Top Rated Driver']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('data-delay="0"', false);
        $response->assertSee('Top Rated Driver');
        $this->assertEquals(5.0, (float) $top->fresh()->rating);
    }

    public function test_french_home_page_is_translated(): void
    {
        $response = $this->get('/fr');

        $response->assertStatus(200);
        $response->assertSee('Taxis Disponibles');
        $response->assertSee('Réserver un Taxi');
        $response->assertSee('Accueil');
        $response->assertSee('Nos Services');
    }

    public function test_english_home_page_shows_english(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Available Taxis');
        $response->assertSee('Book a Taxi');
        $response->assertSee('Our Services');
        $response->assertDontSee('Taxis Disponibles');
    }

    public function test_french_driver_page_is_translated(): void
    {
        $driver = Driver::first();

        $response = $this->get(route('drivers.show.fr', ['driver' => $driver]));

        $response->assertStatus(200);
        $response->assertSee('Détails du Véhicule');
        $response->assertSee('Avis');
        $response->assertSee('Ajouter un Avis');
    }

    public function test_french_login_page_is_translated(): void
    {
        $response = $this->get('/fr/login');

        $response->assertStatus(200);
        $response->assertSee('Bon retour');
        $response->assertSee('Se Connecter');
        $response->assertSee('Mot de passe');
    }

    public function test_invalid_locale_does_not_match_localized_routes(): void
    {
        $this->get('/de')->assertNotFound();
        $this->get('/de/drivers/1')->assertNotFound();
    }

    public function test_dashboard_routes_do_not_gain_locale_query_param(): void
    {
        $this->assertSame('/dashboard/drivers', route('dashboard.drivers', [], false));
        $this->assertStringEndsWith('/sitemap.xml', route('sitemap'));
        $this->assertStringNotContainsString('locale=', route('dashboard.taxis', [], false));
    }
}
