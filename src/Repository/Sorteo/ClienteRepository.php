<?php

namespace App\Repository\Sorteo;

use App\Entity\Sorteo\Cliente;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;
use App\Entity\Empresa;
Use App\Entity\User;

/**
 * @method Cliente|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cliente|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cliente[]    findAll()
 * @method Cliente[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClienteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, Security $security)
    {
        parent::__construct($registry, cliente::class);
        $this->security = $security;
    }

    /**
     * Create cliente.
     */
    public function post($data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Crear entidad principal - cliente
            $entity = $helper->setParametersToEntity(new Cliente(), $data);
            
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
                'msg' => 'cliente creado exitosamente',
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
        $clientes = $this->findBy([], ['nombre' => 'ASC']);

        $result = [];

            foreach ($clientes as $cliente) {
                $result[] = [
                    'id' => $cliente->getId(),
                    'numeroDocumento' => $cliente->getNroDocumentoIdentidad(),
                    'tipoDocumentoIdentidad' => $cliente->getTipoDocumentoIdentidad(),
                    'primerNombre' => $cliente->getPrimerNombre(),
                    'segundoNombre' => $cliente->getSegundoNombre(),
                    'primerApellido' => $cliente->getPrimerApellido(),
                    'segundoApellido' => $cliente->getSegundoApellido(),
                    'fechaNacimiento' =>  $cliente->getFechaNacimiento() == null ? '' : $cliente->getFechaNacimiento()->format("Y-m-d"),
                    'email' => $cliente->getEmail(),
                    'estado' => ($cliente->getEstado()!=null)?array("id"=>$cliente->getEstado()->getId(),"Nombre"=>$cliente->getEstado()->getNombre()):[],
                    'ciudad' => ($cliente->getCiudad()!=null)?array("id"=>$cliente->getCiudad()->getId(),"Nombre"=>$cliente->getCiudad()->getNombre()):[],
                    'direccion' => $cliente->getDireccion(),
                ];
            }

        return $result;
        
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => 'Error al obtener los clientees: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cliente.
     */
    public function update(int $id, $data, $validator, $helper): JsonResponse
    {
        $entityManager = $this->getEntityManager();

        try {
            // Buscar el cliente existente
            $cliente = $this->find($id);
            
            if (!$cliente) {
                return new JsonResponse(['msg' => 'cliente no encontrado'], 404);
            }

            // Actualizar entidad principal
            $cliente = $helper->setParametersToEntity($cliente, $data);
            
            // Validar entidad principal
            $errors = $validator->validate($cliente);
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
                $cliente->setUpdateBy($currentUser->getUserName());
                $cliente->setUpdateAt(new \DateTime());
            }

            // Persistir y flush
            $entityManager->flush();
            
            return new JsonResponse([
                'msg' => 'Registro actualizado exitosamente',
                'id' => $cliente->getId()
            ], 200);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'msg' => 'Error interno del servidor',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function findByCi($ci){
        $entityManager = $this->getEntityManager();
        $clienteData= $this->createQueryBuilder('a')
            ->andWhere('a.nroDocumentoIdentidad='.$ci)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult();
        $dataUser=array();

        foreach($clienteData as $cliente){
            $dataUser=array(
                'id'=>$cliente->getId(),
                'numeroDocumento'=>$cliente->getNroDocumentoIdentidad(),
                'tipoDocumentoIdentidad'=>$cliente->getTipoDocumentoIdentidad(),
                'primerNombre'=>$cliente->getPrimerNombre(),
                'segundoNombre'=>$cliente->getSegundoNombre(),
                'primerApellido'=>$cliente->getPrimerApellido(),
                'segundoApellido'=>$cliente->getSegundoApellido(),
                'email'=>$cliente->getEmail(),
                'codTelefono'=>$cliente->getCodTelefono(),
                'nroTelefono'=>$cliente->getNroTelefono(),
                'estado'=>($cliente->getEstado()!=null)?array("id"=>$cliente->getEstado()->getId(),"Nombre"=>$cliente->getEstado()->getNombre()):[],
                'ciudad'=>($cliente->getCiudad()!=null)?array("id"=>$cliente->getCiudad()->getId(),"Nombre"=>$cliente->getCiudad()->getNombre()):[],
                'direccion'=>$cliente->getDireccion(),
            );
        }
        
        return $dataUser;
 
    }
}
