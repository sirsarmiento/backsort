<?php

namespace App\Controller\Sorteo;

use App\Entity\Costo\Tasa;
use App\Repository\Sorteo\TasaRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\Helper;
use Symfony\Component\Validator\Constraints\Json;

class TasaController extends AbstractController
{
    /**
     * @Route("api/tasa", methods={"POST"})
     * @OA\Post(
     *     summary="Crear una nueva tasa",
     *     description="Crea una nueva tasa con sus datos básicos",
     *     operationId="createTasa",
     *     tags={"Tasas"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de la tasa",
     *         @OA\JsonContent(
     *             required={"monto"},
     *             @OA\Property(property="monto", type="number", format="float", example=15.50, description="Monto de la tasa")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tasa creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tasa creada exitosamente"),
     *             @OA\Property(property="tasaId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos de entrada inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Datos incompletos o inválidos"),
     *             @OA\Property(property="errors", type="object", example={"monto": "Este campo es requerido"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación en los datos"),
     *             @OA\Property(property="errors", type="string", example="monto: Este valor debe ser mayor que 0")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error interno del servidor")
     *         )
     *     )
     * )
     */
    public function post(Request $request,ValidatorInterface $validator,Helper $helper,TasaRepository $repository): JsonResponse
    {   
        try {
            $data = json_decode($request->getContent(),true);
            return $repository->post($data,$validator,$helper); 
        } catch (Exception $e) {
            return new JsonResponse(['msg'=>'Error del Servidor'],500);
        }
    }

    /**
     * @Route("api/tasas", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener todas las tasas",
     *     description="Retorna una lista de todas las tasas con solo el monto",
     *     operationId="getAllTasa",
     *     tags={"Tasas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tasas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Tasas obtenidas exitosamente"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="monto", type="number", format="float", example=0.05)
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=3)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token de acceso no válido")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error al obtener las tasas")
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function findAll(Request $request,TasaRepository $repository): JsonResponse
    {
        $data = $repository->getAll();
        // Verifica qué datos estás obteniendo
        if (empty($data)) {
            return new JsonResponse([
                'message' => 'No se encontraron tasas',
                'data' => []
            ], 200);
        }
        
        return new JsonResponse([
            'message' => 'Tasas obtenidas exitosamente',
            'data' => $data,
            'count' => count($data)
        ], 200);
    }
}
