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
        // 1. Agregar columnas a la tabla events
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('slug')->unique()->nullable();
        });

        // 2. Agregar columnas a la tabla guests
        Schema::table('guests', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->constrained()->onDelete('cascade');
        });

        // 3. Vincular datos existentes
        $firstUser = DB::table('users')->first();
        $firstEvent = DB::table('events')->first();

        if ($firstEvent) {
            if ($firstUser) {
                DB::table('events')->where('id', $firstEvent->id)->update([
                    'user_id' => $firstUser->id,
                    'slug' => 'mi-cumpleanos'
                ]);
            } else {
                DB::table('events')->where('id', $firstEvent->id)->update([
                    'slug' => 'mi-cumpleanos'
                ]);
            }

            // Asociar todos los invitados existentes a este primer evento
            DB::table('guests')->update(['event_id' => $firstEvent->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'slug']);
        });
    }
};
