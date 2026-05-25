<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('zones', function (Blueprint $table) {
        $table->id();

        $table->string('name');

        $table->integer('population');

        $table->integer('traffic_level')->default(0);

        $table->decimal('latitude', 10, 7);

        $table->decimal('longitude', 10, 7);

        $table->timestamps();
    });
}
};
