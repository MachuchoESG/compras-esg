<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT; // Usar la biblioteca JWT adecuada


class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validar las credenciales
        // Validar los datos de entrada

        //dd($request);
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Intentar iniciar sesión con las credenciales proporcionadas
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Si el usuario se autenticó correctamente
            $user = Auth::user();
            //dd($user);
            // Intentar generar el JWT
            $key = 'ESG';
            $payload = [
                'id_user' => $user->id,
                'id_departamento' => $user->departamento_id,
                'id_puesto' => $user->puesto_id,
                'iat' => time(),     // Hora de creación
                'exp' => time() + 60 * 60 * 24 // Expiración (1 hora)
            ];

            $jwt = JWT::encode($payload, $key, 'HS256');

            $jwtNuevo = Token::create([
                'user_id' => $user->id,
                'token' => $jwt
            ]);

            session(['tokenUser' => $jwt, 'id_departamento' => $user->departamento_id, 'id_puesto' => $user->puesto_id]);

            //return view('dashboard');
            return redirect()->route('dashboard');
        } else {
            // Si las credenciales son incorrectas
            //return view('login');
            return redirect()->back()->withErrors(['message' => 'Credenciales incorrectas']);
        }
    }
}
