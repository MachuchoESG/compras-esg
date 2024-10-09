<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use App\Models\GastoFijo;
use App\Service\ProductoService;
use Illuminate\Http\Request;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class GastosFijosController extends Controller
{
    use LivewireAlert;

    public function index()
    {
        return view('catalogos.gastosfijos.index');
    }

    public function store(Request $request)
    {
        try {
            // Validar los datos de la solicitud
            $validatedData = $request->validate([
                'id_empresa' => 'required|integer',
                'id_sucursal' => 'required|integer',
                'id_producto' => 'required|integer|unique:gastos_fijos,id_producto',
                'producto_name' => 'required|string|max:255',
            ]);

            // Intentar crear el nuevo registro
            $gastoFijo = GastoFijo::create([
                'id_empresa' => $validatedData['id_empresa'],
                'id_sucursal' => $validatedData['id_sucursal'],
                'id_producto' => $validatedData['id_producto'],
                'producto_name' => $validatedData['producto_name'],
            ]);

            // Verificar si el registro fue creado exitosamente
            if ($gastoFijo) {
                session(['flag_success' => true]);
                return response()->json([
                    'success' => true,
                    'message' => 'Gasto fijo creado correctamente.',
                    'data' => $gastoFijo,
                ], 201); // 201 Created
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Establecer el flag de error en la sesión
            session(['flag_error' => true]);

            // Retornar los errores de validación con el código 422
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Manejar cualquier otro error que no sea de validación
            session(['flag_error' => true]);

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error inesperado.',
            ], 500); // 500 Internal Server Error
        }
    }

    public function destroy(string $id)
    {
        $gf = GastoFijo::find($id);
        $gf->delete();
    }

    public function gastosFijosEmpresas(Request $request)
    {
        $empresas = Empresa::with('sucursales')->get();

        return  $empresas;
    }

    public function gastosFijosOptProductos(Request $request, string $opt)
    {
        $productService = new ProductoService();
        $arr = explode('-', $opt);
        $productos = $productService->ListaProductos($arr[1]);
        return $productos;
    }

    public function guardarGastoFijo(Request $request)
    {
        $this->alert('success', 'Se agrego producto correctamente a Gastos Fijos.');
        return $request;
    }
}
