<?php

namespace Database\Seeders;

use App\Models\User\Address;
use App\Models\User\User;
use Illuminate\Database\Seeder;

class AddressesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->each(function (User $user) {
            Address::factory()->count(random_int(1, 3))->create(['user_id' => $user->id]);
        });
    }
}
