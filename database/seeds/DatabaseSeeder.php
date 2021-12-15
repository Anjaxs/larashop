<?php

use Database\Seeders\AddressesSeeder;
use Database\Seeders\AdminTablesSeeder;
use Database\Seeders\CouponCodesSeeder;
use Database\Seeders\OrdersSeeder;
use Database\Seeders\ProductsSeeder;
use Database\Seeders\UsersSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminTablesSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(AddressesSeeder::class);
        $this->call(ProductsSeeder::class);
        $this->call(CouponCodesSeeder::class);
        $this->call(OrdersSeeder::class);
    }
}
