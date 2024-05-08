<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facebook_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total_friends_amount')->nullable();
            $table->integer('photo_width')->nullable();
            $table->integer('photo_height')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('github_metrics', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('total_friends_amount')->nullable();
            $table->integer('photo_width')->nullable();
            $table->integer('photo_height')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('provider_metrics_request_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_provider', 50)->index();
            $table->string('endpoint', 255);
            $table->text('response_message')->nullable();
            $table->string('response_type', 100)->nullable();
            $table->string('response_trace_id', 100)->nullable();
            $table->tinyInteger('response_code')->nullable();
            $table->boolean('is_success')->default(true);
            $table->timestamps();
        });

        Schema::create('databox_metrics_request_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('metric_name', 100);
            $table->smallInteger('metrics_amount')->default(1);
            $table->text('response_message')->nullable();
            $table->string('response_id', 100)->nullable();
            $table->boolean('is_success')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facebook_metrics');
        Schema::dropIfExists('github_metrics');
        Schema::dropIfExists('provider_metrics_request_logs');
        Schema::dropIfExists('databox_metrics_request_logs');
    }
};
