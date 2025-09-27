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

    public function getAll(): array 
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
                    'tasa' => $factura->getTasa(),
                    'cliente' => ($factura->getCliente()!=null)?array(
                        "id"=>$factura->getCliente()->getId(),
                        "tipoDocumentoIdentidad"=>$factura->getCliente()->getTipoDocumentoIdentidad(),
                        "nroDocumentoIdentidad"=>$factura->getCliente()->getNroDocumentoIdentidad(),
                        "primerNombre"=>$factura->getCliente()->getPrimerNombre(),
                        "primerApellido"=>$factura->getCliente()->getPrimerApellido(),
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
}
