<?php

namespace App\Livewire\Requisicion;

use App\Models\Comentarios;
use App\Models\Requisicion;
use App\Service\ApiUrl;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;


class Show extends Component
{

    use LivewireAlert;

    public $urlApi;
    public $requisicion;

    public $openIncompleta = false;
    public $comentario;
    public function mount($requisicion)
    {
        // Requisicion::with('detalleRequisiciones', 'cotizaciones.detalleCotizaciones')->find($requisicion->id);

        $this->urlApi = ApiUrl::urlApi();
        $this->requisicion = $requisicion;
    }

    
    public function incompleta(){
        $this->validate([
            'comentario' => 'required',
        ], [], [
            'comentario' => 'Comentario',
        ]);

         // Crear el comentario
            $comentario = Comentarios::create([
                'requisicion_id' => $this->requisicion->id,
                'user_id' => Auth::id(),
                'comentario' => $this->comentario,
            ]);

            if ($comentario) {
                // Actualizar el estatus de la requisición si es necesario
                $this->requisicion->estatus_id = 7;
                $this->requisicion->save();

                // Mostrar un mensaje de éxito
                $this->alert('success', 'Comentario agregado con éxito');
                
                // Reiniciar el campo de comentario
                $this->comentario = '';


                return redirect()->route('requisicion.index');

                // Redirigir o hacer lo que necesites después de agregar el comentario
            } else {
                // Manejar el caso de error si es necesario
                // Por ejemplo, mostrar un mensaje de error
                $this->alert('error', 'Error al agregar el comentario');
            }
    }

    public function delete()
    {

        $this->authorize('delete', $this->requisicion);
        $this->alert('warning', 'Estas seguro de eliminar la requisición?', [
            'position' => 'center',
            'timer' => 6000,
            'toast' => true,
            'showCancelButton' => true,
            'cancelButtonText' => 'Cancelar',
            'showDenyButton' => true,
            'denyButtonText' => 'Eliminar',
            'onDenied' => 'confirmed'
        ]);
    }
    protected $listeners = [
        'confirmed'
    ];
    public function confirmed()
    {




        $requisicionEliminar = Requisicion::find($this->requisicion->id);

        if ($requisicionEliminar) {
            $requisicionEliminar->estatus_id = 9;
            $requisicionEliminar->fechacancelacion=now();

            $requisicionEliminar->save();

            $this->alert('success', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Eliminacion correctamente',
            ]);
        } else {
            $this->alert('info', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Ocurrio un error al eliminar la requisicion',
            ]);
        }




        return redirect()->route('requisicion.index');
    }



    public function autorizarCotizacion(){

        if( $this->requisicion){
            $this->requisicion->estatus_id=2;
            $this->requisicion->save();

            
            $this->alert('success', 'Requisicion', [
                'position' => 'top-end',
                'timer' => '4000',
                'toast' => true,
                'text' => 'Requisicion Autorizada',
            ]);

            return redirect()->route('requisicion.index');
        }
       
    }
    public function render()
    {
        return view('livewire.requisicion.show');
    }
}
