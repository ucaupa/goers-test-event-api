<?php

namespace Database\Seeders;

use App\Models\EventCategory;
use Illuminate\Database\Seeder;

class EventCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EventCategory::query()->create(['name' => 'Art']);
        EventCategory::query()->create(['name' => 'Education']);
        EventCategory::query()->create(['name' => 'Festival']);
        EventCategory::query()->create(['name' => 'Workshop']);
    }
}
