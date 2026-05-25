<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('movements', function (Blueprint $table) {
        $table->id();

        $table->foreignId('zone_from');

        $table->foreignId('zone_to');

        $table->string('transport_type');

        $table->integer('people_count');

        $table->timestamp('movement_time');

        $table->timestamps();
    });
}
};
