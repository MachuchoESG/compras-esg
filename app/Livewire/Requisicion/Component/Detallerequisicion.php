<?php

namespace App\Livewire\Requisicion\Component;

use App\Models\Autorizacionhistorial;
use App\Models\Cotizacion;
use App\Models\DetalleCotizacion;
use App\Models\Evidencia;
use App\Models\Requisicion;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use PhpParser\Node\Stmt\TryCatch;

class Detallerequisicion extends Component
{


    public $requisicion;
    public $requisicionid;
    public $cotizaciones;
    public $autorizacionHistorial = [];
    public $archivo;
    public $productosRequisicion = [];

    use LivewireAlert;
    use WithFileUploads;

    public $openComentarios = false;


    public function mount()
    {
        $this->requisicion =  Requisicion::with('cotizaciones', 'historialesAutorizacion')->find($this->requisicionid);
        $this->autorizacionHistorial = $this->requisicion->historialesAutorizacion;
        $this->productosRequisicion = DetalleCotizacion::where('requisicion_id', '=', $this->requisicion->id);
        $this->cotizaciones = $this->requisicion->cotizaciones;
    }



    public function estatus()
    {
        $primerRegistro = Autorizacionhistorial::where('requisicion_id', $this->requisicionid)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($this->requisicion->estatus_id == 1) {



            if ($primerRegistro) {
                $usuario = User::where('puesto_id', $primerRegistro->user_id)->first(); // O puedes usar ->get() si esperas múltiples resultados
                return 'Pendiente de aprobar por ' . $usuario->name;
            }
        }
        if ($this->requisicion->estatus_id == 2) {
            if ($primerRegistro) {
                $usuario = User::where('puesto_id', $primerRegistro->user_id)->first(); // O puedes usar ->get() si esperas múltiples resultados
                return 'Pendiente de autorizar por ' . $usuario->name;
            }
        }

        if ($this->requisicion->estatus_id == 3) {
            if ($primerRegistro) {
                $usuario = User::where('puesto_id', $primerRegistro->user_id)->first(); // O puedes usar ->get() si esperas múltiples resultados
                return 'Pendiente de autorizar por ' . $usuario->name;
            }
        }

        return '';
    }
    public function downloadevidencia($id)
    {

        $evidencia = Evidencia::findOrFail($id);

        if (!$evidencia) {
            $this->alert('warning', 'Evidencia', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'No se encontró el documento',
            ]);
        } else {

            try {
                $path = storage_path('app/' . $evidencia->url);
                return response()->download($path);
            } catch (\Throwable $th) {
                $this->alert('warning', 'Evidencia', [
                    'position' => 'top-end',
                    'timer' => '4000',
                    'toast' => true,
                    'text' => 'No se encontró el documento',
                ]);
            }
        }
    }

    public function download($id)
    {
        $archivo = Cotizacion::findOrFail($id);
        if (!$archivo) {
            $this->alert('warning', 'Cotización', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'No se encontró el documento',
            ]);
        } else {

            try {
                $path = storage_path('app/' . $archivo->url);
                return response()->download($path);
            } catch (\Exception $e) {
                $this->alert('warning', 'Cotización', [
                    'position' => 'top-end',
                    'timer' => '4000',
                    'toast' => true,
                    'text' => 'No se encontró el documento',
                ]);
            }
        }
    }
    public function render()
    {
        return view('livewire.requisicion.component.detallerequisicion');
    }
}
