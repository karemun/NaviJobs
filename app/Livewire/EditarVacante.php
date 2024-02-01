<?php

namespace App\Livewire;

use App\Models\Salario;
use Livewire\Component;
use App\Models\Categoria;
use App\Models\Vacante;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;

class EditarVacante extends Component
{
    public $vacante_id;
    public $titulo;
    public $salario;
    public $categoria;
    public $empresa;
    public $ultimo_dia;
    public $descripcion;
    public $imagen;
    public $imagen_nueva;

    use WithFileUploads;

    protected $rules = [
        'titulo' => 'required|string',
        'salario' => 'required',
        'categoria' => 'required',
        'empresa' => 'required',
        'ultimo_dia' => 'required',
        'descripcion' => 'required',
        'imagen_nueva' => 'nullable|image|max:1024' // Nueva imagen no obligatoria
    ];

    public function mount(Vacante $vacante) // Recibe vacante del view
    {
        $this->vacante_id = $vacante->id;
        $this->titulo = $vacante->titulo;
        $this->salario = $vacante->salario_id;
        $this->categoria = $vacante->categoria_id;
        $this->empresa = $vacante->empresa;
        $this->ultimo_dia = Carbon::parse($vacante->ultimo_dia)->format('Y-m-d'); // Formato fecha requerido
        $this->descripcion = $vacante->descripcion;
        $this->imagen = $vacante->imagen;
    }

    public function editarVacante()
    {
        $datos = $this->validate(); // Valida los rules

        // Si hay una imagen nueva
        if($this->imagen_nueva) {
            $imagen = $this->imagen_nueva->store('public/vacantes');    // Se guarda la imagen y la ubicacion
            $datos['imagen'] = str_replace('public/vacantes/', '', $imagen); // Obtiene solo el nombre
        }

        // Se encuentra la vacante a editar
        $vacante = Vacante::find($this->vacante_id);

        // Asignar los valores
        $vacante->titulo = $datos['titulo'];
        $vacante->salario_id = $datos['salario'];
        $vacante->categoria_id = $datos['categoria'];
        $vacante->empresa = $datos['empresa'];
        $vacante->ultimo_dia = $datos['ultimo_dia'];
        $vacante->descripcion = $datos['descripcion'];
        $vacante->imagen = $datos['imagen'] ?? $vacante->imagen; // Si hay nueva imagen se remplaza, si no, queda la misma

        // Se guarda la vacante
        $vacante->save();

        // Redireccionar al usuario
        session()->flash('mensaje', 'La vacante se actualizó correctamente.');
        return redirect()->route('vacantes.index');
    }

    public function render()
    {
        // Almacena los registros
        $salarios = Salario::all();
        $categorias = Categoria::all();

        return view('livewire.editar-vacante', [
            'salarios' => $salarios, 
            'categorias' => $categorias
        ]);
    }
}
