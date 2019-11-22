<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run() {
        $year = date('Y');

        // User
        $user = \App\User::create([
            'name' => 'KOLAY BUTCE',
            'email' => 'test@kolaybutce.com',
            'password' => '$2y$10$T0pGj.Cc/dI3DxrH2STYWuiJhrQD5bgf4s/fQErvMaPPnHTTfy6IK' // test1234
        ]);

        // Space
        $space = \App\Space::create([
            'currency_id' => 1,
            'name' => 'KOLAY BUTCE TEST\'s Space'
        ]);

        $user->spaces()->attach($space);

        // Tags
        $tagBills = \App\Tag::create(['space_id' => $space->id, 'name' => 'Fatura']);
        $tagFood = \App\Tag::create(['space_id' => $space->id, 'name' => 'Yemek']);
        $tagTransport = \App\Tag::create(['space_id' => $space->id, 'name' => 'Ulasim']);

        // CREATE Provider
        $provider = \App\Provider::create([
            'id' => 0,
            'name' => 'default_provider',
            'alias' => 'default',
            'icon' => '-',
            'client_id' => '-',
            'client_secret' => '-',
            'login_url' => '-'
        ]);


        // CREATE Account
        $account = \App\Account::create([
            'space_id' => $space->id,
            'name' => 'account_RANDOMNUM',
            'color' => '#CCCCCC',
            'description' => 'description_RANDOM',
            'provider_id' => $provider->id,
            'currency_id' => 1,
            'sync_url' => '---',
            'last_sync' => new DateTime(),
        ]);
        $account->id=0;
        $account->update();

        // Add Earnings & Spendings for 1 year
        for ($i = 1; $i < 12; $i ++) {
            // Income
            \App\Earning::create([
                'space_id' => $space->id,
                'happened_on' => $year . '-' . $i . '-24',
                'description' => 'Maas',
                'amount' => 25000
            ]);

            // Bills
            \App\Spending::create([
                'space_id' => $space->id,
                'tag_id' => $tagBills->id,
                'happened_on' => $year . '-' . $i . '-01',
                'description' => 'Telefon',
                'amount' => 2500,
                'account_id' => $account->id
            ]);

            \App\Spending::create([
                'space_id' => $space->id,
                'tag_id' => $tagBills->id,
                'happened_on' => $year . '-' . $i . '-01',
                'description' => 'Arac Sigortasi',
                'amount' => 4500,
                'account_id' => $account->id
            ]);

            // Food
            for ($j = 0; $j < rand(1, 10); $j ++) {
                \App\Spending::create([
                    'space_id' => $space->id,
                    'tag_id' => $tagFood->id,
                    'happened_on' => $year . '-' . $i . '-' . rand(1, 28),
                    'description' => '-',
                    'amount' => 250 * rand(1, 5),
                    'account_id' => $account->id
                ]);
            }

            // Transport
            for ($j = 0; $j < rand(1, 3); $j ++) {
                \App\Spending::create([
                    'space_id' => $space->id,
                    'tag_id' => $tagTransport->id,
                    'happened_on' => $year . '-' . $i . '-' . rand(1, 28),
                    'description' => '-',
                    'amount' => 1000 * rand(1, 5),
                    'account_id' => $account->id
                ]);
            }
        }
    }
}
