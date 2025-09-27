<?php

namespace App\Controller\Sorteo;

use App\Entity\Sorteo\Cliente;
use App\Repository\Sorteo\ClienteRepository;
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

class ClienteController extends AbstractController
{
    /**
     * @Route("api/cliente", methods={"POST"})
     * @OA\Post(
     *     summary="Crear un nuevo cliente",
     *     description="Crea un nuevo cliente con sus datos personales",
     *     operationId="createCliente",
     *     tags={"Clientes"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del cliente",
     *         @OA\JsonContent(
     *             required={"tipoDocumentoIdentidad", "nroDocumentoIdentidad", "primerNombre", "primerApellido", "email", "codTelefono", "nroTelefono"},
     *             @OA\Property(property="tipoDocumentoIdentidad", type="string", example="V", description="Tipo de documento de identidad (V, E, J, etc.)"),
     *             @OA\Property(property="nroDocumentoIdentidad", type="string", example="12345678", description="Número de documento de identidad"),
     *             @OA\Property(property="primerNombre", type="string", example="Juan", description="Primer nombre del cliente"),
     *             @OA\Property(property="segundoNombre", type="string", example="Carlos", description="Segundo nombre del cliente (opcional)"),
     *             @OA\Property(property="primerApellido", type="string", example="Pérez", description="Primer apellido del cliente"),
     *             @OA\Property(property="segundoApellido", type="string", example="Gómez", description="Segundo apellido del cliente (opcional)"),
     *             @OA\Property(property="email", type="string", example="juan@example.com", description="Email del cliente"),
     *             @OA\Property(property="codTelefono", type="string", example="0412", description="Código de teléfono"),
     *             @OA\Property(property="nroTelefono", type="string", example="1234567", description="Número de teléfono"),
     *             @OA\Property(property="estado", type="integer", example=1, description="ID del estado (opcional)"),
     *             @OA\Property(property="ciudad", type="integer", example=1, description="ID de la ciudad (opcional)"),
     *             @OA\Property(property="direccion", type="string", example="Av. Principal #123", description="Dirección del cliente (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cliente creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cliente creado exitosamente"),
     *             @OA\Property(property="clienteId", type="integer", example=1)
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
    public function post(Request $request,ValidatorInterface $validator,Helper $helper,ClienteRepository $repository): JsonResponse
    {   
        try {
            $data = json_decode($request->getContent(),true);
            return $repository->post($data,$validator,$helper); 
        } catch (Exception $e) {
            return new JsonResponse(['msg'=>'Error del Servidor'],500);
        }
    }

    /**
     * @Route("api/clientes", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener todos los clientes",
     *     description="Retorna una lista de todos los clientes",
     *     operationId="getAllClientes",
     *     tags={"Clientes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Clientes obtenidos exitosamente"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="tipoDocumentoIdentidad", type="string", example="V"),
     *                     @OA\Property(property="nroDocumentoIdentidad", type="string", example="12345678"),
     *                     @OA\Property(property="primerNombre", type="string", example="Juan"),
     *                     @OA\Property(property="segundoNombre", type="string", example="Carlos"),
     *                     @OA\Property(property="primerApellido", type="string", example="Pérez"),
     *                     @OA\Property(property="segundoApellido", type="string", example="Gómez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com"),
     *                     @OA\Property(property="codTelefono", type="string", example="0412"),
     *                     @OA\Property(property="nroTelefono", type="string", example="1234567"),
     *                     @OA\Property(property="estado", type="string", example="Nombre Estado"),
     *                     @OA\Property(property="ciudad", type="string", example="Nombre Ciudad"),
     *                     @OA\Property(property="direccion", type="string", example="Av. Principal #123"),
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=5)
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function findAll(Request $request,ClienteRepository $repository): JsonResponse
    {

        $data = $repository->getAll();

        // Verifica qué datos estás obteniendo
        if (empty($data)) {
            return new JsonResponse([
                'message' => 'No se encontraron clientes',
                'data' => []
            ], 200);
        }
        
        return new JsonResponse([
            'message' => 'Clientes obtenidos exitosamente',
            'data' => $data,
            'count' => count($data)
        ], 200);
    }

    /**
     * @Route("/api/cliente/{id}", methods={"PUT"})
     * @OA\Put(
     *     summary="Actualizar un cliente existente",
     *     description="Actualiza los datos de un cliente",
     *     operationId="updateCliente",
     *     tags={"Clientes"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del Cliente a actualizar",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del cliente a actualizar",
     *         @OA\JsonContent(
     *             required={"tipoDocumentoIdentidad", "nroDocumentoIdentidad", "primerNombre", "primerApellido", "email", "codTelefono", "nroTelefono"},
     *             @OA\Property(property="tipoDocumentoIdentidad", type="string", example="V", description="Tipo de documento de identidad"),
     *             @OA\Property(property="nroDocumentoIdentidad", type="string", example="12345678", description="Número de documento de identidad"),
     *             @OA\Property(property="primerNombre", type="string", example="Juan", description="Primer nombre del cliente"),
     *             @OA\Property(property="segundoNombre", type="string", example="Carlos", description="Segundo nombre del cliente (opcional)"),
     *             @OA\Property(property="primerApellido", type="string", example="Pérez", description="Primer apellido del cliente"),
     *             @OA\Property(property="segundoApellido", type="string", example="Gómez", description="Segundo apellido del cliente (opcional)"),
     *             @OA\Property(property="email", type="string", example="juan@example.com", description="Email del cliente"),
     *             @OA\Property(property="codTelefono", type="string", example="0412", description="Código de teléfono"),
     *             @OA\Property(property="nroTelefono", type="string", example="1234567", description="Número de teléfono"),
     *             @OA\Property(property="estado", type="integer", example=1, description="ID del estado (opcional)"),
     *             @OA\Property(property="ciudad", type="integer", example=1, description="ID de la ciudad (opcional)"),
     *             @OA\Property(property="direccion", type="string", example="Av. Principal #123", description="Dirección del cliente (opcional)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cliente actualizado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Cliente actualizado exitosamente")
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
     *         description="Cliente no encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cliente no encontrado")
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
    public function put(int $id, Request $request, ValidatorInterface $validator, Helper $helper, ClienteRepository $repository): JsonResponse
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

    /**
     *  Get an cliente. 
     * @Route("/api/cliente/{ci}", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener cliente por CI",
     *     description="Retorna un cliente específico basado en su número de cédula de identidad",
     *     operationId="getClienteByCi",
     *     tags={"Clientes"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de clientes obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Clientes obtenidos exitosamente"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="tipoDocumentoIdentidad", type="string", example="V"),
     *                     @OA\Property(property="nroDocumentoIdentidad", type="string", example="12345678"),
     *                     @OA\Property(property="primerNombre", type="string", example="Juan"),
     *                     @OA\Property(property="segundoNombre", type="string", example="Carlos"),
     *                     @OA\Property(property="primerApellido", type="string", example="Pérez"),
     *                     @OA\Property(property="segundoApellido", type="string", example="Gómez"),
     *                     @OA\Property(property="email", type="string", example="juan@example.com"),
     *                     @OA\Property(property="codTelefono", type="string", example="0412"),
     *                     @OA\Property(property="nroTelefono", type="string", example="1234567"),
     *                     @OA\Property(property="estado", type="string", example="Nombre Estado"),
     *                     @OA\Property(property="ciudad", type="string", example="Nombre Ciudad"),
     *                     @OA\Property(property="direccion", type="string", example="Av. Principal #123"),
     *                 )
     *             ),
     *             @OA\Property(property="count", type="integer", example=5)
     *         )
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function findByCi($ci,ClienteRepository $repository): JsonResponse
    {
        $data = $repository
        ->findByCi($ci);
        if (!$data) {
            return new JsonResponse(['msg'=>'No existen Registros'],404);  
        }   
         return new JsonResponse($data,200);  
    }
}
