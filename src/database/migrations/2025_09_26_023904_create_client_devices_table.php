<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_devices', function (Blueprint $t) {
            $t->id();
            $t->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $t->string('city')->nullable()->index();
            $t->string('platform', 16)->nullable();     // ios/android/web
            $t->string('device_model', 64)->nullable();
            $t->string('os_version', 32)->nullable();
            $t->string('push_token', 191)->nullable()->index();
            $t->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('client_devices');
    }
};
