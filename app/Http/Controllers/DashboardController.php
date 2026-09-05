<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Review;
use App\Models\Taxi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $drivers = Driver::with('taxi')->get();
        $reviews = Review::with('driver')->latest()->take(10)->get();

        $stats = [
            'drivers' => $drivers->count(),
            'active_taxis' => Taxi::where('is_active', true)->count(),
            'reviews' => Review::count(),
        ];

        return view('dashboard.index', compact('drivers', 'reviews', 'stats'));
    }

    public function taxis(): View
    {
        $drivers = Driver::with('taxi')->get();

        return view('dashboard.taxis', compact('drivers'));
    }

    public function drivers(): View
    {
        $drivers = Driver::with('taxi')->get();

        return view('dashboard.drivers', compact('drivers'));
    }

    public function createDriver(): View
    {
        return view('dashboard.drivers-create');
    }

    public function editDriver(Driver $driver): View
    {
        return view('dashboard.drivers-edit', compact('driver'));
    }

    public function createTaxi(): View
    {
        $drivers = Driver::with('taxi')->get();

        return view('dashboard.taxis-create', compact('drivers'));
    }

    public function editTaxi(Driver $driver): View
    {
        $driver->load('taxi');

        return view('dashboard.taxis-edit', compact('driver'));
    }

    public function reviews(): View
    {
        $reviews = Review::with('driver')->latest()->get();

        return view('dashboard.reviews', compact('reviews'));
    }

    public function settings(): View
    {
        $settings = cache()->get('settings', []);

        return view('dashboard.settings', compact('settings'));
    }

    public function storeDriver(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:150',
            'years_experience' => 'nullable|integer|min:0',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'work_days' => 'nullable|array',
            'work_days.*' => 'integer|between:1,7',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        Driver::create($data);

        return redirect()->route('dashboard.drivers')->with('success', 'Driver added successfully!');
    }

    public function updateDriver(Request $request, Driver $driver): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'whatsapp' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:150',
            'years_experience' => 'nullable|integer|min:0',
            'work_start' => 'nullable|date_format:H:i',
            'work_end' => 'nullable|date_format:H:i',
            'work_days' => 'nullable|array',
            'work_days.*' => 'integer|between:1,7',
            'is_active' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');
        $driver->update($data);

        return redirect()->route('dashboard.drivers')->with('success', 'Driver updated successfully!');
    }

    public function destroyDriver(Driver $driver): RedirectResponse
    {
        $driver->delete();

        return back()->with('success', 'Driver deleted successfully!');
    }

    public function storeTaxi(Request $request): RedirectResponse
    {
        $isNew = $request->isMethod('POST');

        $data = $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'name' => 'required|string|max:100',
            'type' => 'nullable|string|max:30',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images' => $isNew ? 'required|array|min:4' : 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'plate_number' => 'nullable|string|max:30',
            'year' => 'nullable|integer',
            'color' => 'nullable|string|max:30',
            'capacity' => 'nullable|string|max:50',
            'luggage' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('taxis', 'public');
        }

        if ($request->hasFile('images')) {
            $data['image_gallery'] = array_map(
                fn ($image) => $image->store('taxis', 'public'),
                $request->file('images'),
            );
        }

        $driver = Driver::findOrFail($data['driver_id']);
        unset($data['driver_id']);
        $data['is_active'] = $request->boolean('is_active', true);
        $driver->taxi()->updateOrCreate(['driver_id' => $driver->id], $data);

        return redirect()->route('dashboard.taxis')->with('success', 'Taxi saved successfully!');
    }

    public function destroyTaxi(Driver $driver): RedirectResponse
    {
        if ($driver->taxi) {
            $driver->taxi()->delete();
        }

        return back()->with('success', 'Taxi deleted successfully!');
    }

    public function toggleTaxiAvailability(Taxi $taxi): RedirectResponse
    {
        $taxi->update(['is_active' => ! $taxi->is_active]);

        return back()->with('success', 'Taxi availability updated successfully!');
    }

    public function toggleReviewVisibility(Review $review): RedirectResponse
    {
        $review->update(['is_visible' => ! $review->is_visible]);

        $this->recomputeDriverRating($review);

        return back()->with('success', 'Review updated successfully!');
    }

    public function destroyReview(Review $review): RedirectResponse
    {
        $review->delete();

        $this->recomputeDriverRating($review);

        return back()->with('success', 'Review deleted successfully!');
    }

    private function recomputeDriverRating(Review $review): void
    {
        $driver = $review->driver;

        if ($driver) {
            $count = $driver->reviews()->where('is_visible', true)->count();
            $sum = (float) $driver->reviews()->where('is_visible', true)->sum('rating');
            $driver->update([
                'reviews_count' => $count,
                'rating' => $count > 0 ? round($sum / $count, 1) : 0,
            ]);
        }
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => 'nullable|string|max:100',
            'whatsapp_number' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
        ]);

        // Settings are stored in the application cache for simplicity.
        cache()->forever('settings', $data);

        return back()->with('success', 'Settings saved successfully!');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,'.Auth::id(),
            'password' => 'nullable|string|min:6',
        ]);

        $user = Auth::user();
        $user->name = $data['name'];
        $user->email = $data['email'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully!');
    }
}
