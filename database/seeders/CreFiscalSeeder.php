<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CreFiscalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'Rolando',
                'last_name' => 'Delgadillo',
                'email' => 'rolando.delgadillo@mail.com',
            ],
            [
                'first_name' => 'Oswaldo',
                'last_name' => 'Ontiveros',
                'email' => 'oswaldo.ontiveros@mail.com',
            ],
            [
                'first_name' => 'Pablo',
                'last_name' => 'Mendoza',
                'email' => 'pablo.mendoza@mail.com',
            ],
            [
                'first_name' => 'Pablo',
                'last_name' => 'Vaca',
                'email' => 'pablo.vaca@mail.com',
            ],
            [
                'first_name' => 'Milton',
                'last_name' => 'Madrid',
                'email' => 'milton.madrid@mail.com',
            ],
            [
                'first_name' => 'Diego',
                'last_name' => 'Suarez',
                'email' => 'diego.suarez@mail.com',
            ],
            [
                'first_name' => 'Cruztel',
                'last_name' => 'Uno',
                'email' => 'cruztel1@mail.com',
            ],
            [
                'first_name' => 'Cruztel',
                'last_name' => 'Dos',
                'email' => 'cruztel2@mail.com',
            ],
            [
                'first_name' => 'Salvio',
                'last_name' => 'Cegarra',
                'email' => 'salvio.cegarra@mail.com',
            ],
            [
                'first_name' => 'Mauricio',
                'last_name' => 'Miranda',
                'email' => 'mauricio.miranda@mail.com',
            ],
            [
                'first_name' => 'Juan Carlos',
                'last_name' => 'Giles',
                'email' => 'juan.giles@mail.com',
            ],
            [
                'first_name' => 'Ernesto',
                'last_name' => 'Barrientos',
                'email' => 'ernesto.barrientos@mail.com',
            ],
            [
                'first_name' => 'Lorgio',
                'last_name' => 'Coca',
                'email' => 'lorgio.coca@mail.com',
            ],
            [
                'first_name' => 'Jhonny',
                'last_name' => 'Guzman',
                'email' => 'jhonny.guzman@mail.com',
            ],
            [
                'first_name' => 'Javier',
                'last_name' => 'Vidal',
                'email' => 'javier.vidal@mail.com',
            ],
            [
                'first_name' => 'Cristhian',
                'last_name' => 'Villavicencio',
                'email' => 'cristhian.villavicencio@mail.com',
            ],
            [
                'first_name' => 'Jose Ovidio',
                'last_name' => 'Salvatierra',
                'email' => 'jose.salvatierra@mail.com',
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

            // Asignar los roles correspondientes
            $user->assignRole(['User', 'Fiscal de CRE']);
        }

        // Ejecutar el comando artisan solicitado
        Artisan::call('serebo:sync-roles');
    }
}