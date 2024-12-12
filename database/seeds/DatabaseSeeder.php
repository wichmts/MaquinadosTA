<?php

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
        $this->call(PermissionTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(MaquinaSeeder::class);

        // Eloquent::unguard();
        // $path = 'app/dev_files/countries.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('Country table seeded!');

        // $path = 'app/dev_files/states.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('State table seeded!');

        // $path = 'app/dev_files/cities_00.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_00');

        // $path = 'app/dev_files/cities_01.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_01');

        // $path = 'app/dev_files/cities_02.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_02');

        // $path = 'app/dev_files/cities_03.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_03');

        // $path = 'app/dev_files/cities_04.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_04');

        // $path = 'app/dev_files/cities_05.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_05');

        // $path = 'app/dev_files/cities_06.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_06');

        // $path = 'app/dev_files/cities_07.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_07');

        // $path = 'app/dev_files/cities_08.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_08');

        // $path = 'app/dev_files/cities_09.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_09');

        // $path = 'app/dev_files/cities_10.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_10');

        // $path = 'app/dev_files/cities_11.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_11');

        // $path = 'app/dev_files/cities_12.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_12');

        // $path = 'app/dev_files/cities_13.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_13');

        // $path = 'app/dev_files/cities_14.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_14');

        // $path = 'app/dev_files/cities_15.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_15');

        // $path = 'app/dev_files/cities_16.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_16');

        // $path = 'app/dev_files/cities_17.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_17');

        // $path = 'app/dev_files/cities_18.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_18');

        // $path = 'app/dev_files/cities_19.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_19');

        // $path = 'app/dev_files/cities_20.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_20');

        // $path = 'app/dev_files/cities_21.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_21');

        // $path = 'app/dev_files/cities_22.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_22');

        // $path = 'app/dev_files/cities_23.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_23');

        // $path = 'app/dev_files/cities_24.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_24');

        // $path = 'app/dev_files/cities_25.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_25');

        // $path = 'app/dev_files/cities_26.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_26');

        // $path = 'app/dev_files/cities_27.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_27');

        // $path = 'app/dev_files/cities_28.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_28');

        // $path = 'app/dev_files/cities_29.sql';
        // DB::unprepared(file_get_contents($path));
        // $this->command->info('cities_29');
    }
}
