<?php

use Illuminate\Database\Seeder;

use App\Helpers\DbHelper;

class UpsertMakesAndModels extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DbHelper::updateMakesAndModelsTables();
    }
}
