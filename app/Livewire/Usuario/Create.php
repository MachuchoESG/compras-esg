<?php

namespace App\Livewire\Usuario;

use App\Actions\Fortify\PasswordValidationRules;
use App\Mail\SendMailNewUserNoReplay;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\Puesto;
use App\Models\User;
use Error;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;


class Create extends Component
{
    use PasswordValidationRules;

    use LivewireAlert;

    public $open = false;
    public $departamentos = [];
    public $puestos = [];
    public $empresas = [];
    public $user = '';



    public $usuario = [
        'name' => '',
        'email' => '',
        'password=' > '',
        'departamento_id' => '',
        'puesto_id' => ''
    ];

    public function obtenerPuestos($departamentoId)
    {
        $this->puestos = Puesto::where('departamento_id', $departamentoId)->get();
    }

    public function save()
    {
        try {
            // Datos a validar
            $data = [
                'name' => $this->usuario['name'],
                'email' => $this->usuario['email'],
                'password' => $this->usuario['password'], // Asumiendo que este es el campo de contraseña sin cifrar
                'departamento_id' => $this->usuario['departamento_id'],
                'puesto_id' => $this->usuario['puesto_id']
            ];

            // Reglas de validación
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required'], // Asumiendo que $this->passwordRules() devuelve las reglas para la contraseña
                'departamento_id' => ['required'],
                'puesto_id' => ['required']
            ];

            // Crear un validador
            $validator = Validator::make($data, $rules);

            // Validar los datos
            $validator->validate();

            // Crear el usuario si pasa la validación
            $usuario = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'departamento_id' => $data['departamento_id'],
                'puesto_id' => $data['puesto_id']
            ]);

            $this->alert('success', "Usuario creado correctamente");
            $this->reset(['usuario']);
        } catch (ValidationException $e) {
            // Manejar errores de validación
            $this->alert('error', "Error al validar datos.");
            // ...
        } catch (QueryException $e) {
            $this->alert('error', "Error al guardar datos.");
            // Manejar errores de consulta
            // ...
        }

        $this->open = false;
        try{
            $this->sendMailNewUser($this->usuario['name'], $this->usuario['email'], $this->usuario['password']);
        } catch (Error $error){
            $this->alert('error', "Error al enviar correo de autenticación.");
        }
        
        return redirect()->route('usuario.index');
    }

    public function sendMailNewUser($nombre, $correo, $contraseña)
    {
        Mail::to($correo)->send(new SendMailNewUserNoReplay($nombre, $correo, $contraseña));
    }

    public function generarPasswordRandom()
    {
        $letras = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 3);
        $numeros = substr(str_shuffle('1234567890'), 0, 3);

        $this->usuario['password'] = $letras . $numeros;
    }


    public function mount()
    {
        $this->departamentos = Departamento::all();
        $dep1 = $this->departamentos[0];
        $this->puestos = Puesto::where('departamento_id', '=', $dep1->id)->get();
        //dd($dep1);
    }

    public function render()
    {
        return view('livewire.usuario.create');
    }
}
