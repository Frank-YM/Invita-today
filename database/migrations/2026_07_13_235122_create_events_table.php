<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('¡Feliz Cumpleaños!');
            $table->string('subtitle')->default('Estás invitado a celebrar 🎉');
            $table->string('emoji')->default('🎂');
            $table->string('event_type', 30)->default('cumple');
            $table->dateTime('date')->nullable();
            $table->string('place')->default('Cusco, Perú');
            $table->decimal('lat', 10, 7)->default(-13.516799);
            $table->decimal('lng', 10, 7)->default(-71.978817);
            $table->timestamps();
        });

        // Fila única inicial editable
        DB::table('events')->insert([
            'title'    => '¡Feliz Cumpleaños Sofía!',
            'subtitle' => 'Estás invitado a celebrar sus 5 añitos 🎉',
            'emoji'    => '🎂',
            'event_type' => 'cumple',
            'date'     => '2026-08-15 16:00:00',
            'place'    => 'Plaza de Armas, Cusco, Perú',
            'lat'      => -13.516799,
            'lng'      => -71.978817,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
