<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * В этой таблице храним json объект, который хранит в себе очки пользователя за каждый день месяца.
     * В 00:00 нового месяца таблицу обнуляем(например крон скриптом), чтобы не использовать данные за прошлый месяц при построении отчетов.
     */
    public function up(): void
    {
        Schema::create('user_scores_by_day', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->json('scores_data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_scores_by_day');
    }
};
