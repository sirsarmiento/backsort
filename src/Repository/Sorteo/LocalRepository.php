<?php

namespace App\Repository\Sorteo;

use App\Entity\Sorteo\Local;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Entity\Empresa;
Use App\Entity\User;


/**
 * @method Local|null find($id, $lockMode = null, $lockVersion = null)
 * @method Local|null findOneBy(array $criteria, array $orderBy = null)
 * @method Local[]    findAll()
 * @method Local[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LocalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, Local::class);
        $this->security = $security;
    }

    /**
     * Create local.
     */
    public function post($data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Crear entidad principal - local
            $entity = $helper->setParametersToEntity(new local(), $data);
            
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
                'msg' => 'Local creado exitosamente',
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
        $locales = $this->findBy([], ['nombre' => 'ASC']);

        $result = [];

            foreach ($locales as $local) {
                $result[] = [
                    'id' => $local->getId(),
                    'nombre' => $local->getNombre(),
                    'monto' => $local->getMonto(),
                ];
            }

        return $result;
        
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error al obtener los locales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Local.
     */
    public function update(int $id, $data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Buscar el Local existente
            $Local = $this->find($id);
            
            if (!$Local) {
                return new JsonResponse(['msg' => 'Local no encontrado'], 404);
            }

            // Actualizar entidad principal
            $Local = $helper->setParametersToEntity($Local, $data);
            
            // Validar entidad principal
            $errors = $validator->validate($Local);
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
                $Local->setUpdateBy($currentUser->getUserName());
                $Local->setUpdateAt(new \DateTime());
            }

            // Persistir y flush
            $entityManager->flush();
            
            return new JsonResponse([
                'msg' => 'Registro actualizado exitosamente',
                'id' => $Local->getId()
            ], 200);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'msg' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
