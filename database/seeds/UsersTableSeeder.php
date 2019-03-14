<?php

use Illuminate\Database\Seeder;
use App\Coin;
use App\User;
use App\Wallet;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        factory(User::class, 10000)->create();
        foreach (Coin::all() as $coin)
        {
            foreach (User::all() as $user)
            {
                Wallet::create(["coin_id"=>$coin->id,"user_id"=>$user->id]);
            }
        }
    }
}
