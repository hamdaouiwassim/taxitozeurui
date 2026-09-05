<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('taxis', function (Blueprint $table) {
            $table->json('image_gallery')->nullable()->after('image');
        });

        Schema::table('taxis', function (Blueprint $table) {
            DB::table('taxis')
                ->whereNotNull('image')
                ->get(['id', 'image'])
                ->each(fn ($taxi) => DB::table('taxis')
                    ->where('id', $taxi->id)
                    ->update(['image_gallery' => json_encode([$taxi->image])]));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxis', function (Blueprint $table) {
            $table->dropColumn('image_gallery');
        });
    }
};
