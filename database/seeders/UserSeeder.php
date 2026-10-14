<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Administrador principal
        // User::create([
        //     'firstname_usr' => 'Jair',
        //     'lastname_usr' => 'Cussy',
        //     'email_usr' => 'jair@twiiti.com',
        //     'email' => 'jair@twiiti.com',
        //     'password_usr' => bcrypt('12345678')
        // ])->assignRole('Super admin');

        // Usuarios del proyecto (extraídos de la imagen)[cite: 1]
        $users = [
            [
                'first_name' => 'Luis Omar',
                'last_name' => 'Suarez Perez',
                'email' => 'construccion-pry@chiquitano-group.com',
            ],
            [
                'first_name' => 'Cristian',
                'last_name' => 'Pocube Taceo',
                'email' => 'gir-cr@chiquitano-group.com',
            ],
            [
                'first_name' => 'Genaro',
                'last_name' => 'Montenegro Jimenez',
                'email' => 'ctec-cr@chiquitano-group.com',
            ],
            [
                'first_name' => 'Mauricio',
                'last_name' => 'Quintana Zambrana',
                'email' => 'diseno-cr@chiquitano-group.com',
            ],
            [
                'first_name' => 'Jose Luis',
                'last_name' => 'Flores Soto',
                'email' => 'fiscal-1-cr@chiquitano-group.com',
            ],
            [
                'first_name' => 'Emanuel Cristian',
                'last_name' => 'Castro',
                'email' => 'fiscal-2-cr@chiquitano-group.com',
            ],
        ];

        foreach ($users as $userData) {
            // Generar la contraseña según la regla: a12345 + 1ra Mayúscula + 2da minúscula + *
            $firstName = $userData['first_name'];
            $p1 = strtoupper(substr($firstName, 0, 1));
            $p2 = strtolower(substr($firstName, 1, 1));
            
            $password = 'a12345' . $p1 . $p2 . '*';

            $user = User::create([
                'firstname_usr' => $userData['first_name'],
                'lastname_usr' => $userData['last_name'],
                'email_usr' => $userData['email'],
                'email' => $userData['email'],
                'password_usr' => bcrypt($password)
            ]);

            $user->assignRole('User');
        }

        // Ejecutar el comando artisan solicitado
        Artisan::call('serebo:sync-roles');
    }
}