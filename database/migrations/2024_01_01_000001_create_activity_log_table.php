<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event')->nullable();
            $table->string('batch_uuid')->nullable();
            $table->morphs('subject', 'subject');
            $table->morphs('causer', 'causer');
            $table->json('properties')->nullable();
            $table->text('description');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_log');
    }
}