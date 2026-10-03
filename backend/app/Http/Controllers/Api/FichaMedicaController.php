<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GuardarFichaMedicaRequest;
use App\Services\FichaMedicaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FichaMedicaController extends Controller
{
    public function __construct(
        protected FichaMedicaService $fichaService
    ) {}

    /**
     * Registro público de ficha médica por el alumno desde su celular.
     */
    public function storePublic(GuardarFichaMedicaRequest $request): JsonResponse
    {
        try {
            $resultado = $this->fichaService->registrarFichaInicial($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Ficha médica registrada con éxito. Presenta tu número de folio en el consultorio de enfermería.',
                'data' => $resultado,
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error al registrar la ficha médica.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Consultar ficha médica por ID.
     */
    public function show(int $id): JsonResponse
    {
        $ficha = $this->fichaService->obtenerFicha($id);

        if (! $ficha) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ficha médica no encontrada.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $ficha,
        ]);
    }

    /**
     * Listar fichas médicas (para enfermería).
     */
    public function index(Request $request): JsonResponse
    {
        $estatus = $request->query('estatus');
        $fichas = $this->fichaService->listarFichas($estatus);

        return response()->json([
            'status' => 'success',
            'data' => $fichas,
        ]);
    }
}
