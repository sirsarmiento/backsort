<?php

namespace App\Controller\Sorteo;

use App\Entity\Sorteo\Factura;
use App\Repository\Sorteo\FacturaRepository;

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

class FacturaController extends AbstractController
{
    /**
     * @Route("api/factura", methods={"POST"})
     * @OA\Post(
     *     summary="Crear una nueva factura",
     *     description="Crea una nueva factura con sus datos personales",
     *     operationId="createFactura",
     *     tags={"Facturas"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del factura",
     *         @OA\JsonContent(
     *             required={"cliente", "local", "numero", "fecha", "hora", "monto", "tasa"},
     *             @OA\Property(property="cliente", type="integer", example=1, description="ID del cliente"),
     *             @OA\Property(property="local", type="integer", example=1, description="ID del local"),
     *             @OA\Property(property="numero", type="string", example="01234567", description="Numero de la factura"),
     *             @OA\Property(property="fecha", type="date", example="2022-04-01", description="Fecha de la factura"),
     *             @OA\Property(property="hora", type="string", example="09:10", description="Hora de la factura"),
     *             @OA\Property(property="monto", type="number", example="1540.10", description="Monto de la factura"),
     *             @OA\Property(property="tasa", type="number", example="171.30", description="Tasa de la factura"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Factura creada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Factura creada exitosamente"),
     *             @OA\Property(property="facturaId", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos de entrada inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Datos incompletos o inválidos")
     *         )
     *     )
     * )
     */
    public function post(Request $request,ValidatorInterface $validator,Helper $helper,FacturaRepository $repository): JsonResponse
    {   
        try {
            $data = json_decode($request->getContent(),true);
            return $repository->post($data,$validator,$helper); 
        } catch (Exception $e) {
            return new JsonResponse(['msg'=>'Error del Servidor'],500);
        }
    }

    /**
     * @Route("api/facturas", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener todos los facturas",
     *     description="Retorna una lista de todos los facturas",
     *     operationId="getAllFacturas",
     *     tags={"Facturas"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de facturas obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Facturas obtenidos exitosamente"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="cliente", type="string", example=1, description="Nombre del cliente"),
     *                     @OA\Property(property="local", type="string", example=1, description="Nombre del local"),
     *                     @OA\Property(property="numero", type="string", example="01234567", description="Numero de la factura"),
     *                     @OA\Property(property="fecha", type="date", example="2022-04-01", description="Fecha de la factura"),
     *                     @OA\Property(property="hora", type="string", example="09:10", description="Hora de la factura"),
     *                     @OA\Property(property="monto", type="number", example="1540.10", description="Monto de la factura"),
     *                     @OA\Property(property="tasa", type="number", example="171.30", description="Tasa de la factura"),
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=5)
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function findAll(Request $request,FacturaRepository $repository): JsonResponse
    {
        $data = $repository->getAll();
        // Verifica qué datos estás obteniendo
        if (empty($data)) {
            return new JsonResponse([
                'message' => 'No se encontraron locales',
                'data' => []
            ], 200);
        }
        
        return new JsonResponse([
            'message' => 'Facturas obtenidos exitosamente',
            'data' => $data,
            'count' => count($data)
        ], 200);
    }

    /**
     * @Route("/api/factura/{id}", methods={"PUT"})
     * @OA\Put(
     *     summary="Actualizar una factura existente",
     *     description="Actualiza los datos de un factura",
     *     operationId="updateFactura",
     *     tags={"Facturas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la factura a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de la factura a actualizar",
     *         @OA\JsonContent(
     *             required={"cliente", "local", "numero", "fecha", "hora", "monto", "tasa"},
     *             @OA\Property(property="cliente", type="integer", example=1, description="ID del cliente"),
     *             @OA\Property(property="local", type="integer", example=1, description="ID del local"),
     *             @OA\Property(property="numero", type="string", example="01234567", description="Numero de la factura"),
     *             @OA\Property(property="fecha", type="date", example="2022-04-01", description="Fecha de la factura"),
     *             @OA\Property(property="hora", type="string", example="09:10", description="Hora de la factura"),
     *             @OA\Property(property="monto", type="number", example="2750.10", description="Monto de la factura"),
     *             @OA\Property(property="tasa", type="number", example="171.30", description="Tasa de la factura"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Factura actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Factura actualizada exitosamente")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos de entrada inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Datos incompletos o inválidos")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Factura no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Factura no encontrado")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Error de validación en los datos")
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
    public function put(int $id, Request $request, ValidatorInterface $validator, Helper $helper, FacturaRepository $repository): JsonResponse
    {   
        try {
            $data = json_decode($request->getContent(), true);
            return $repository->update($id, $data, $validator, $helper); 
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error del Servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
