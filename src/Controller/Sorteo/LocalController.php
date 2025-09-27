<?php

namespace App\Controller\Sorteo;

use App\Entity\Sorteo\Local;
use App\Repository\Sorteo\LocalRepository;
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

class LocalController extends AbstractController
{
    /**
     * @Route("api/local", methods={"POST"})
     * @OA\Post(
     *     summary="Crear un nuevo local",
     *     description="Crea un nuevo local con sus datos básicos",
     *     operationId="createLocal",
     *     tags={"Locales"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del local",
     *         @OA\JsonContent(
     *             required={"nombre", "monto", "empresa_id"},
     *             @OA\Property(property="nombre", type="string", example="Local Principal", description="Nombre del local"),
     *             @OA\Property(property="monto", type="integer", example=1000, description="Monto del local"),
     *             @OA\Property(property="empresa_id", type="integer", example=1, description="ID de la empresa a la que pertenece el local")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Local creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Local creado exitosamente"),
     *             @OA\Property(property="localId", type="integer", example=1)
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
    public function post(Request $request,ValidatorInterface $validator,Helper $helper,LocalRepository $repository): JsonResponse
    {   
        try {
            $data = json_decode($request->getContent(),true);
            return $repository->post($data,$validator,$helper); 
        } catch (Exception $e) {
            return new JsonResponse(['msg'=>'Error del Servidor'],500);
        }
    }

    /**
     * @Route("api/locales", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener todos los locales",
     *     description="Retorna una lista de todos los locales",
     *     operationId="getAllLocales",
     *     tags={"Locales"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de locales obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Locales obtenidos exitosamente"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="nombre", type="string", example="Local Principal"),
     *                     @OA\Property(property="monto", type="integer", example=1000),
     *                     @OA\Property(property="empresa", type="string", example="Nombre Empresa")
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=5)
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function findAll(Request $request,LocalRepository $repository): JsonResponse
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
            'message' => 'Locales obtenidos exitosamente',
            'data' => $data,
            'count' => count($data)
        ], 200);
    }

    /**
     * @Route("/api/local/{id}", methods={"PUT"})
     * @OA\Put(
     *     summary="Actualizar un local existente",
     *     description="Actualiza los datos de un local",
     *     operationId="updateLocal",
     *     tags={"Locales"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del Local a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del local a actualizar",
     *         @OA\JsonContent(
     *             required={"nombre", "monto", "empresa_id"},
     *             @OA\Property(property="nombre", type="string", example="Local Principal", description="Nombre del local"),
     *             @OA\Property(property="monto", type="integer", example=1000, description="Monto del local"),
     *             @OA\Property(property="empresa_id", type="integer", example=1, description="ID de la empresa a la que pertenece el local")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Local actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Local actualizado exitosamente")
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
     *         description="Local no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Local no encontrado")
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
    public function put(int $id, Request $request, ValidatorInterface $validator, Helper $helper, LocalRepository $repository): JsonResponse
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
