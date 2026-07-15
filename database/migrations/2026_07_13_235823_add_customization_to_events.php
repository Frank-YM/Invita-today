<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string('color_primary', 20)->default('#d4a3b3');
            $table->string('color_secondary', 20)->default('#6e5a63');
            $table->string('color_accent', 20)->default('#e5c1cd');
            $table->string('rsvp_button_text', 60)->default('Confirmar asistencia');
            $table->string('share_message', 300)->nullable();
            $table->string('dress_code', 100)->nullable();
            $table->string('gift_info', 200)->nullable();
            $table->text('extra_info')->nullable();
            $table->boolean('show_countdown')->default(true);
            $table->boolean('show_messages')->default(true);
            $table->boolean('show_map')->default(true);
            $table->boolean('show_confirmed_count')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['color_primary','color_secondary','color_accent',
                'rsvp_button_text','share_message','dress_code','gift_info','extra_info',
                'show_countdown','show_messages','show_map','show_confirmed_count']);
        });
    }
};
