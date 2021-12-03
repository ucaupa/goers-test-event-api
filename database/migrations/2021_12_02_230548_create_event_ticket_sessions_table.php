<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventTicketSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_ticket_sessions', function (Blueprint $table) {
            $table->id();
            $table->integer('event_ticket_id')->unsigned()->index();
            $table->integer('event_schedule_id')->unsigned()->index();

            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('event_ticket_id')->references('id')->on('event_tickets')->cascadeOnDelete();
            $table->foreign('event_schedule_id')->references('id')->on('event_schedules')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_ticket_sessions');
    }
}
