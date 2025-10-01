<?php

namespace App\Repository\Sorteo;

use App\Entity\Sorteo\Tasa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Entity\Empresa;
Use App\Entity\User;

/**
 * @method Tasa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tasa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tasa[]    findAll()
 * @method Tasa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TasaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Tasa::class);
        $this->security = $security;
    }

    /**
     * Create Tasa.
     */
    public function post($data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Crear entidad principal - Tasa
            $entity = $helper->setParametersToEntity(new Tasa(), $data);
            
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
                'msg' => 'Tasa creado exitosamente',
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
        $tasas = $this->findBy([], ['id' => 'DESC']);

        $result = [];

        foreach ($tasas as $tasa) {      
            $result[] = [
                'id' => $tasa->getId(),
                'monto' => $tasa->getMonto(),
                'fecha' => $tasa->getCreateAt()->format("Y-m-d")
            ];
        }

        return $result;
        
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error al obtener las tasas: ' . $e->getMessage()
            ], 500);
        }
    }

}
