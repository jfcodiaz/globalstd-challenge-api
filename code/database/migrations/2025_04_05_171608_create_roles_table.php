<?php

use App\Repositories\RoleRepository;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        app(RoleRepository::class)->createBaseRoles();
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
