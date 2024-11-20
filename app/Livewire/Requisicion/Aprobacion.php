<?php

namespace App\Livewire\Requisicion;

use App\Models\autorizacionhistorial;
use App\Models\permisosrequisicion;
use App\Models\Requisicion;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Aprobacion extends Component
{
    use LivewireAlert;

    public $requisicion;
    public function mount()
    {
        $this->visto();
    }
    public function visto()
    {


        if ($this->requisicion->visto != true) {
            $this->requisicion->visto = 1;
            $this->requisicion->save();
        }
    }

    public function aprobar()
    {
        $userToken = Token::where('user_id', Auth::id())->latest()->first();
        $user = $this->requisicion->solicitante;
        $permiso = permisosrequisicion::where('PuestoSolicitante_id', '=', $user->puesto_id)
            ->where('departamento_id', $user->departamento_id)
            ->first();
        if ($this->requisicion) {
            if ($this->requisicion->cotizacion_especial !== 1) {
                $this->requisicion->estatus_id = 7; // APROBADO
                $dataPost = [
                    'cotizacion_especial' => false,
                    'departamento_especial' => null,
                    'departamento' => 2,
                    'id_puesto_solicitante' => $user->puesto_id,
                    'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                    'id_usuario_alertar' => null,
                    'estatus' => $this->requisicion->estatus->name,
                    'folio' => $this->requisicion->folio,
                    'url_requisicion' => "/cotizacion" . "/" . $this->requisicion->id,
                ];
            } else {
                $this->requisicion->estatus_id = 13; // COTIZACION ESPECIAL
                $dataPost = [
                    'cotizacion_especial' => true,
                    'departamento_especial' => $this->requisicion->departamento_especial,
                    'departamento' => null,
                    'id_puesto_solicitante' => $user->puesto_id,
                    'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                    'id_usuario_alertar' => $user->id,
                    'estatus' => $this->requisicion->estatus->name,
                    'folio' => $this->requisicion->folio,
                    'url_requisicion' => "/requisicion" . "/" . $this->requisicion->id . "/especial",
                ];
            }

            $this->requisicion->aprobado = 1;
            $this->requisicion->fechaaprobacion = now();

            $this->requisicion->save();
            $this->alert('success', 'Se a aprobado el FOLIO: ' . $this->requisicion->folio);
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken->token,
            ])->post(
                env('SERVICE_SOCKET_HOST', 'localhost') . ':' . env('SERVICE_SOCKET_PORT', '8888') . '/send/requisicion/departamento',
                $dataPost
            );
        }
        return redirect()->route('requisicion.index');
    }
    public function noaprobar()
    {
        $userToken = Token::where('user_id', Auth::id())->latest()->first();
        if ($this->requisicion) {
            $this->requisicion->aprobado = 0;
            $this->requisicion->estatus_id = 4; // NO AUTORIZADO
            $this->requisicion->save();
            $user = $this->requisicion->solicitante;
            $permiso = permisosrequisicion::where('PuestoSolicitante_id', '=', $user->puesto_id)
                ->where('departamento_id', $user->departamento_id)
                ->first();
            $userAutorizador = User::where('puesto_id', '=', $permiso->PuestoAutorizador_id)->first();
            $dataPost = [
                'id_puesto_solicitante' => $user->puesto_id,
                'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                'id_usuario_alertar' => $user->id,
                'estatus' => $this->requisicion->estatus->name,
                'folio' => $this->requisicion->folio,
                'url_requisicion' => "/requisicion" . "/" . $this->requisicion->id,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken->token,
            ])->post(
                env('SERVICE_SOCKET_HOST', 'localhost') . ':' . env('SERVICE_SOCKET_PORT', '8888') . '/send/requisicion-actualizada',
                $dataPost
            );
        }
        return redirect()->route('requisicion.index');
    }

    public function render()
    {
        return view('livewire.requisicion.aprobacion');
    }
}
