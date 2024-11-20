<?php

namespace App\Livewire\Requisicion;

use App\Models\Autorizacionhistorial;
use App\Models\DetalleRequisicion;
use App\Models\permisosrequisicion;
use App\Models\Requisicion;
use App\Models\Token;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CotizacionEspecial extends Component
{
    use LivewireAlert;

    public $requisicion;
    public $productosRequisicion = [];
    public $observacionEspecial = '';

    public function continuarRequisicion()
    {
        $userToken = Token::where('user_id', Auth::id())->latest()->first();
        $user = User::find(Auth::id());
        $permiso = permisosrequisicion::where('PuestoSolicitante_id', '=', $user->puesto->id)
            ->where('departamento_id', $user->departamento_id)
            ->first();
        $userAutorizador = User::where('puesto_id', '=', $permiso->PuestoAutorizador_id)->first();

        if ($this->observacionEspecial !== '' && strlen($this->observacionEspecial) > 1) {
            $updatedRequisicion = Requisicion::find($this->requisicion->id);
            $updatedRequisicion->observacion_especial = $this->observacionEspecial;
            $updatedRequisicion->estatus_id = 7;
            $updatedRequisicion->save();
            $this->alert('success', 'Se agrego correctamente la observacion especial.');

            $dataPost = [
                'cotizacion_especial' => false,
                'departamento_especial' => null,
                'departamento' => 2,
                'id_puesto_solicitante' => $user->puesto_id,
                'id_puesto_autorizador' => $permiso->PuestoAutorizador_id,
                'id_usuario_alertar' => null,
                'estatus' => $updatedRequisicion->estatus->name,
                'folio' => $updatedRequisicion->folio,
                'url_requisicion' => "/cotizacion" . "/" . $updatedRequisicion->id,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $userToken->token,
            ])->post(
                env('SERVICE_SOCKET_HOST', 'http://laravel_notifications') . ':' . env('SERVICE_SOCKET_PORT', '8888') . '/send/requisicion/departamento',
                $dataPost
            );

            $autorizacion = Autorizacionhistorial::create([
                'requisicion_id' => $this->requisicion->id,
                'user_id' => auth()->user()->puesto_id,
                'user_solicita' => auth()->user()->puesto_id,
                'departamento_id' => auth()->user()->departamento->id,
                'autorizado' => false,
                'visto' => false
            ]);
            return redirect()->route('requisicion.index');
        } else {
            $this->alert('error', 'La observacion especial es obligatoria.');
        }
    }

    public function mount()
    {
        //$vistoHistorial = Autorizacionhistorial::where('requisicion_id', '=', $this->requisicion->id)->get();
        //dd($vistoHistorial);


        //dd($this->requisicion);
        if ($this->requisicion->observacion_especial) {
            $this->observacionEspecial = $this->requisicion->observacion_especial;
        }
        $this->productosRequisicion = DetalleRequisicion::where('requisicion_id', '=', $this->requisicion->id)->get();
    }
    public function render()
    {
        return view('livewire.requisicion.cotizacionespecial');
    }
}
