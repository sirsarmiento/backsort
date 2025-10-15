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
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use  App\Service\Correo;

class FacturaController extends AbstractController
{
    private $params;

    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

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
     *             required={"user", "local", "numero", "fecha", "hora", "monto", "tasa"},
     *             @OA\Property(property="user", type="integer", example=1, description="ID del usuario"),
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
     *                     @OA\Property(property="user", type="string", example=1, description="Nombre del usuario"),
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
        $data = $repository->getAll($this->params->get('urlapi'));
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
     * @Route("api/facturas/tickets/total", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener el total de tickets",
     *     description="Retorna la suma total de todos los tickets generados",
     *     operationId="getTotalTickets",
     *     tags={"Facturas"},
     *     @OA\Response(
     *         response=200,
     *         description="Total de tickets obtenido exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Total de tickets obtenido exitosamente"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="totalTickets", type="integer", example=12345, description="Suma total de todos los tickets"),
     *                 @OA\Property(property="totalFacturas", type="integer", example=100, description="Cantidad total de facturas procesadas")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error interno del servidor"
     *     )
     * )
     * @Security(name="Bearer")
     */
    public function TotalTickets(FacturaRepository $repository): JsonResponse
    {
        try {

            $ticketData = $repository->getTotalTicketsWithCount();
            
            return new JsonResponse([
                'success' => true,
                'message' => 'Total de tickets obtenido exitosamente',
                'data' => [
                    'totalTickets' => (int) $ticketData['totalTickets'],
                    'totalFacturas' => (int) $ticketData['totalFacturas']
                ]
            ], 200);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error al obtener el total de tickets: ' . $e->getMessage(),
                'data' => [
                    'totalTickets' => 0,
                    'totalFacturas' => 0
                ]
            ], 500);
        }
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
     *             required={"user", "local", "numero", "fecha", "hora", "monto", "tasa"},
     *             @OA\Property(property="user", type="integer", example=1, description="ID del usuario"),
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

    /**
     * @Route("/api/factura/{id}/print", methods={"PUT"})
     * @OA\Put(
     *     summary="Actualizar impresión de factura",
     *     description="Actualiza el contador de impresión de una factura",
     *     operationId="updateFacturaPrint",
     *     tags={"Facturas"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID de la factura",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos para actualizar la impresión",
     *         @OA\JsonContent(
     *             required={"print"},
     *             @OA\Property(property="print", type="integer", example="1", description="Contador de impresiones")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Factura actualizada exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Factura actualizada exitosamente"),
     *             @OA\Property(property="factura", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Factura no encontrada",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Factura no encontrada")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Datos de entrada inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error del servidor",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error del servidor")
     *         )
     *     )
     * )
     */
    public function updatePrint($id, Request $request, ValidatorInterface $validator, Helper $helper, FacturaRepository $repository): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(),true);
            $em =$this->getDoctrine()->getManager();
            $repository = $this->getDoctrine()->getRepository(Factura::class);
            return $repository->putPrint($data,$id,$validator,$helper); 
        } catch (Exception $e) {
            return new JsonResponse(['msg'=>'Error del Servidor'],500);
        }
    }

    /**
    * @Route("/api/validate/email", methods={"POST"})
    * @OA\Post(
        * summary="Validate Email",
        * description="Validate Email",
        * operationId="ValidateEmail",
        * tags={"Facturas"},
        * @OA\RequestBody(
        *    required=true,
        *    description="email",
        *    @OA\JsonContent(
        *       required={"email"},
        *       @OA\Property(property="email", type="string", format="string", example="baezgregoric@gmail.com"),
        *    ),
        * ),
        * @OA\Response(
        *    response=422,
        *    description="Wrong credentials response",
        *    @OA\JsonContent(
        *       @OA\Property(property="message", type="string", example="Sorry, wrong email address or password. Please try again")
        *        )
        *     )
        * )
    */
   public function ValidateEmail(Request $request, Correo $correo): JsonResponse
    {   
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!$data || !isset($data['email']) || empty(trim($data['email']))) {
                return new JsonResponse(['msg' => 'Email es requerido'], 400);
            }
            
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                return new JsonResponse(['msg' => 'Email inválido'], 400);
            }
            
            $urlFront = $this->params->get('urlfrom');
            $correo->validateEmail($data, $urlFront);
            
            return new JsonResponse(['success' => true, 'msg' => 'Email enviado correctamente']);
            
        } catch (HttpException $e) {
            return new JsonResponse(['msg' => 'Error del Servidor'], 500);
        } catch (\Exception $e) {
            return new JsonResponse(['msg' => 'Error interno del servidor'], 500);
        }
    }

    /**
     *  Get bills by email
     * @Route("/api/factura/{email}", methods={"GET"})
     * @OA\Get(
     *     summary="Obtener todos las facturas por email",
     *     description="Retorna una lista de todos las facturas por un cliente",
     *     operationId="getBillsByEmail",
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
     *                     @OA\Property(property="user", type="string", example=1, description="Nombre del usuario"),
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
    public function findBillsByEmail($email,FacturaRepository $repository): JsonResponse
    {
        $data = $repository
        ->findBillsByEmail($email);
        if (!$data) {
            return new JsonResponse(['msg'=>'No existen Registros'],404);  
        }   
         return new JsonResponse($data,200);  
    }
}
