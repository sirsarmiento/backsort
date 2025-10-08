<?php

namespace App\Repository\Sorteo;

use App\Entity\Sorteo\Factura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Entity\Empresa;
Use App\Entity\User;

/**
 * @method Factura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Factura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Factura[]    findAll()
 * @method Factura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Factura::class);
        $this->security = $security;
    }

    /**
     * Create factura.
     */
    public function post($data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Crear entidad principal - factura
            $entity = $helper->setParametersToEntity(new Factura(), $data);
                        
            // Convertir montos al formato correcto para la base de datos. Los montos mayores a 1000 me estaba guardando el primer numero ejemplo 1.500,00 me guardaba 1
            if (isset($data['monto'])) {
                // Asegurar que el monto sea un float/string numérico
                $monto = is_numeric($data['monto']) ? $data['monto'] : floatval(str_replace(',', '.', str_replace('.', '', $data['monto'])));
                $entity->setMonto((string) $monto);
            }
            
            if (isset($data['tasa'])) {
                $tasa = is_numeric($data['tasa']) ? $data['tasa'] : floatval(str_replace(',', '.', str_replace('.', '', $data['tasa'])));
                $entity->setTasa((string) $tasa);
            }

            if (isset($data['montoMin'])) {
                // Asegurar que el monto sea un float/string numérico
                $montoMin = is_numeric($data['montoMin']) ? $data['montoMin'] : floatval(str_replace(',', '.', str_replace('.', '', $data['montoMin'])));
                $entity->setMontoMin((string) $montoMin);
            }

            // Validar entidad principal
            $errors = $validator->validate($entity);
            if ($errors->count() > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                
                return new JsonResponse([
                    'msg' => 'Errores de validación',
                    'errors' => $errorMessages
                ], 422);
            }
            
            // Obtener usuario actual
            $currentUser = $entityManager->getRepository(User::class)
                ->find($this->security->getUser()->getId());
            
            if (!$currentUser) {
                return new JsonResponse(['msg' => 'Usuario no encontrado'], 404);
            }
            
            $entity->setCreateBy($currentUser->getUserName());

            // Persistir y flush
            $entityManager->persist($entity);
            $entityManager->flush();
            
            return new JsonResponse([
                'msg' => 'factura creado exitosamente',
                'id' => $entity->getId()
            ], 201);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'msg' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAll($urlPhotoCI): array 
    {
        try {
        $facturas = $this->findBy([], ['id' => 'DESC']);

        $result = [];

            foreach ($facturas as $factura) {
                $result[] = [
                    'id' => $factura->getId(),
                    'numero' => $factura->getNumero(),
                    'fecha' => $factura->getFecha()->format("Y-m-d"),
                    'hora' => $factura->getHora(),
                    'monto' => $factura->getMonto(),
                    'montoMin' => $factura->getMontoMin(),
                    'tasa' => $factura->getTasa(),
                    'print' => $factura->getPrint(),
                    'tickets' => $factura->getTickets(),
                    'cliente' => ($factura->getUser()!=null)?array(
                        "id"=>$factura->getUser()->getId(),
                        "tipoDocumentoIdentidad"=>$factura->getUser()->getTipoDocumentoIdentidad(),
                        "nroDocumentoIdentidad"=>$factura->getUser()->getNumeroDocumento(),
                        "nombreCompleto" => trim(
                                ($factura->getUser()->getPrimerNombre() ?? '') . ' ' .
                                ($factura->getUser()->getSegundoNombre() ?? '') . ' ' .
                                ($factura->getUser()->getPrimerApellido() ?? '') . ' ' .
                                ($factura->getUser()->getSegundoApellido() ?? '')
                        ),
                        "fotoCedula" => ($factura->getUser()->getFoto()) ? $urlPhotoCI . $factura->getUser()->getFoto() : null,
                        ):[],
                    'local' => ($factura->getLocal()!=null)?array(
                        "id"=>$factura->getLocal()->getId(),
                        "nombre"=>$factura->getLocal()->getNombre()
                        ):[],
                ];
            }

        return $result;
        
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error al obtener los facturaes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getTotalTicketsWithCount(): array
    {
        try {
            $query = $this->createQueryBuilder('f')
                ->select('SUM(f.tickets) as totalTickets', 'COUNT(f.id) as totalFacturas')
                ->getQuery();
            
            return $query->getSingleResult();
            
        } catch (\Exception $e) {
            return [
                'totalTickets' => 0,
                'totalFacturas' => 0
            ];
        }
    }

    /**
     * Update factura.
     */
    public function update(int $id, $data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Buscar el factura existente
            $factura = $this->find($id);
            
            if (!$factura) {
                return new JsonResponse(['msg' => 'factura no encontrada'], 404);
            }

            // Actualizar entidad principal
            $factura = $helper->setParametersToEntity($factura, $data);
            
            // Validar entidad principal
            $errors = $validator->validate($factura);
            if ($errors->count() > 0) {
                $errorMessages = [];
                foreach ($errors as $error) {
                    $errorMessages[$error->getPropertyPath()] = $error->getMessage();
                }
                
                return new JsonResponse([
                    'msg' => 'Errores de validación',
                    'errors' => $errorMessages
                ], 422);
            }

            // Obtener usuario actual para auditoría
            $currentUser = $entityManager->getRepository(User::class)
                ->find($this->security->getUser()->getId());

            if ($currentUser) {
                $factura->setUpdateBy($currentUser->getUserName());
                $factura->setUpdateAt(new \DateTime());
            }

            // Persistir y flush
            $entityManager->flush();
            
            return new JsonResponse([
                'msg' => 'Registro actualizado exitosamente',
                'id' => $factura->getId()
            ], 200);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'msg' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Print Factura.
     */
    public function putPrint($data,$id,$validator,$helper): JsonResponse  
    {
        $entityManager = $this->getEntityManager();
        $entity =$entityManager->getRepository(Factura::class)->find($id);

        if (!$entity) {
            return new JsonResponse(['msg'=>'No existen Registros con el id: '.$id],404);  
        }

        // Validar que print sea un número entero
        $printValue = $data['print'];
        if (!is_int($printValue) || $printValue < 0) {
            return new JsonResponse([
                'message' => 'El campo print debe ser un número entero positivo'
            ], 422);
        }

        // Actualizar solo la propiedad print
        $entity->setPrint($printValue);

        $entity=$helper->setParametersToEntity($entity,$data);
        $currentUser =$entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
        $entity->setUpdateBy($currentUser->getUserName());
        $entity->setUpdateAt(new \DateTime());

        $errors = $validator->validate($entity);
        if($errors->count() > 0){
            foreach ($errors as $violation) {
                $messages[$violation->getPropertyPath()][] = $violation->getMessage();
            }
            return new JsonResponse($messages,500);
        }else{
            $entityManager->persist($entity);
            $entityManager->flush();
            return new JsonResponse(['msg'=>'Registro Actualizado: '.$entity->getId()],200);
        }

    }
}
