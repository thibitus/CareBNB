<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
    $this->call('BookingDateTimeTableSeeder');
    $this->command->info('Booking DateTimes seeded!');

    $this->call('AppointmentSeeder');
    $this->command->info('Appointments seeded!');

    $this->call('AdminSeeder');
    $this->command->info('Admins seeded!');

    $this->call('TimeIntervalTableSeeder');
    $this->command->info('Time intervals seeded!');

    $this->call('ConfigurationTableSeeder');
    $this->command->info('Configurations seeded!');

		$this->call('HulpverlenerSeeder');
		$this->command->info('hulpverleners seeded!');

    Eloquent::unguard();
	}

}
