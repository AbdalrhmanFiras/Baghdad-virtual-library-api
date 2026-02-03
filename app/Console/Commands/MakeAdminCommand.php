<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user with Filament access';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email address');
        $password = $this->secret('Password');

        if (empty($name) || empty($email) || empty($password)) {
            $this->error('All fields are required.');

            return;
        }

        if (\App\Models\User::where('email', $email)->exists()) {
            $this->error('A user with this email already exists.');

            return;
        }

        $user = \App\Models\User::create([
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'role' => 'admin',
        ]);

        $this->info("Admin user [{$email}] created successfully.");
    }
}
