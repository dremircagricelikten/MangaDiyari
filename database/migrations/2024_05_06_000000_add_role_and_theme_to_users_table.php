<?php

use App\Enums\UserRole;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default(UserRole::USER->value)->after('is_admin');
            $table->string('theme_preference')->default('light')->after('role');
        });

        DB::table('users')
            ->where('is_admin', true)
            ->update(['role' => UserRole::ADMIN->value]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'theme_preference']);
        });
    }
};
