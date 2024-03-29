<?php

namespace App\Livewire;

use App\Models\Vacante;
use App\Notifications\NuevoCandidato;
use Livewire\Component;
use Livewire\WithFileUploads;

class PostularVacante extends Component
{
    use WithFileUploads; // Subir archivo

    public $cv;
    public $vacante;

    protected $rules = [
        'cv' => 'required|mimes:pdf'
    ];

    public function mount(Vacante $vacante)
    {
        $this->vacante = $vacante; // Obtiene la informacion de la vacante
    }

    public function postularme()
    {
        $datos = $this->validate();

        // validar que el usuario no haya postulado a la vacante
        if($this->vacante->candidatos()->where('user_id', auth()->user()->id)->count() > 0) {
            session()->flash('mensaje', 'Ya postulaste a esta vacante anteriormente');
        } else {
            // Almacenar el CV
            $cv = $this->cv->store('public/cv'); // Se guarda la imagen y la ubicacion
            $datos['cv'] = str_replace('public/cv/', '', $cv); // Obtiene solo el nombre

            // Crear candidato a la vacante
            $this->vacante->candidatos()->create([
                'user_id' => auth()->user()->id,
                // 'vacante_id' se almacena en automatico por la relacion en el modelo
                'cv' => $datos['cv'],
            ]);
    
            // Crear notificación y enviar el email
            $this->vacante->reclutador->notify(new NuevoCandidato($this->vacante->id, $this->vacante->titulo, auth()->user()->id ));
    
            // Mostrar el usuario un mensaje de ok
            session()->flash('mensaje', 'Se envió correctamente tu información, mucha suerte');
    
            // Redireccionar al usuario
            return redirect()->back();
        }
    }

    public function render()
    {
        return view('livewire.postular-vacante');
    }
}
