<?php

namespace App\Entity;

use App\Entity\Empresa;
use App\Entity\Riesgo\ProyectoResponsables;
use App\Entity\Sorteo\Factura;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Your E-Mail adress has already been registered"
 * )
 * @ORM\HasLifecycleCallbacks() 
 */

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $tipoDocumentoIdentidad;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $primerNombre;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $segundoNombre;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $primerApellido;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $segundoApellido;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaNacimiento;

    /**
     * @Assert\Email(
     *     message = "El email '{{ value }}' no es valido.",
     * )
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class)
     */
    private $idStatus;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $updateBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $createBy;

    /**
     * @ORM\OneToMany(targetEntity=Telefono::class, mappedBy="idUser")
     */
    private $telefonos;

    /**
     * @ORM\Column(type="string", length=2000)
     */
    private $roles;
    

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $foto;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $numeroDocumento;

    /**
     * @ORM\ManyToOne(targetEntity=Cargo::class)
     */
    private $idCargo;

    /**
     * @ORM\ManyToOne(targetEntity=Pais::class, inversedBy="users")
     */
    private $pais;

    /**
     * @ORM\ManyToOne(targetEntity=Estado::class, inversedBy="users")
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=Ciudad::class, inversedBy="users")
     */
    private $ciudad;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $sexo;

    /**
     * @ORM\Column(type="string", length=2000, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\OneToMany(targetEntity=Redes::class, mappedBy="id_user_id", orphanRemoval=true)
     */
    private $iduser_redes;


    /**
     * @ORM\OneToMany(targetEntity=CuentaEmail::class, mappedBy="user")
     */
    private $cuentaEmails;

    /**
     * @ORM\ManyToOne(targetEntity=Empresa::class, inversedBy="iduser")
     */
    private $idempresa;

    /**
     * @ORM\OneToMany(targetEntity=Factura::class, mappedBy="user")
     */
    private $facturas;


    public function __construct()
    {
        $this->telefonos = new ArrayCollection();
        $this->iduser_redes = new ArrayCollection();
        $this->iduserbloqueoarchivo = new ArrayCollection();
        $this->iduserhistbloqueo = new ArrayCollection();
        $this->cuentaEmails = new ArrayCollection();
        $this->histMovmtItemsIdUser = new ArrayCollection();
        $this->facturas = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getTipoDocumentoIdentidad(): ?string
    {
        return $this->tipoDocumentoIdentidad;
    }

    public function setTipoDocumentoIdentidad(string $tipoDocumentoIdentidad): self
    {
        $this->tipoDocumentoIdentidad = $tipoDocumentoIdentidad;

        return $this;
    }

    public function getPrimerNombre(): ?string
    {
        return $this->primerNombre;
    }

    public function setPrimerNombre(string $primerNombre): self
    {
        $this->primerNombre = $primerNombre;

        return $this;
    }

    public function getSegundoNombre(): ?string
    {
        return $this->segundoNombre;
    }

    public function setSegundoNombre(?string $segundoNombre): self
    {
        $this->segundoNombre = $segundoNombre;

        return $this;
    }

    public function getPrimerApellido(): ?string
    {
        return $this->primerApellido;
    }

    public function setPrimerApellido(string $primerApellido): self
    {
        $this->primerApellido = $primerApellido;

        return $this;
    }

    public function getSegundoApellido(): ?string
    {
        return $this->segundoApellido;
    }

    public function setSegundoApellido(?string $segundoApellido): self
    {
        $this->segundoApellido = $segundoApellido;

        return $this;
    }

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(?\DateTimeInterface $fechaNacimiento): self
    {
        $this->fechaNacimiento = $fechaNacimiento;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIdStatus(): ?Status
    {
        return $this->idStatus;
    }

    public function setIdStatus(?Status $idStatus): self
    {
        $this->idStatus = $idStatus;

        return $this;
    }

    public function getUpdateBy(): ?string
    {
        return $this->updateBy;
    }

    public function setUpdateBy(?string $updateBy): self
    {
        $this->updateBy = $updateBy;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    
    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;


        return $this;
    }

    public function getCreateBy(): ?string
    {
        return $this->createBy;
    }

    public function setCreateBy(?string $createBy): self
    {
        $this->createBy = $createBy;

        return $this;
    }

    /**
     * @return Collection|Telefono[]
     */
    public function getTelefonos(): Collection
    {
        return $this->telefonos;
    }

    public function addTelefono(Telefono $telefono): self
    {
        if (!$this->telefonos->contains($telefono)) {
            $this->telefonos[] = $telefono;
            $telefono->setIdUser($this);
        }

        return $this;
    }

    public function removeTelefono(Telefono $telefono): self
    {
        if ($this->telefonos->removeElement($telefono)) {
            // set the owning side to null (unless already changed)
            if ($telefono->getIdUser() === $this) {
                $telefono->setIdUser(null);
            }
        }

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $arr=json_decode($roles,TRUE); 
        return $arr;
    }

    public function setRoles(?string $roles): self
    {
        $this->roles = stripslashes(trim($roles,'"'));

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getApiToken(): string
    {
        return (string) $this->apiToken;
    }

    public function setApiToken(string $apiToken): self
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }


    public function getPropertyNames() {
        return get_object_vars($this);
    }    


    /**
    * @ORM\PrePersist
    */
    public function setCreatedAtValue()
    {
        $this->createdAt = new \DateTime();
        
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(string $foto): self
    {
        $this->foto = $foto;

        return $this;
    }

    public function getNumeroDocumento(): ?string
    {
        return $this->numeroDocumento;
    }

    public function setNumeroDocumento(?string $numeroDocumento): self
    {
        $this->numeroDocumento = $numeroDocumento;

        return $this;
    }

    public function getIdCargo(): ?Cargo
    {
        return $this->idCargo;
    }

    public function setIdCargo(?Cargo $idCargo): self
    {
        $this->idCargo = $idCargo;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPais(): ?Pais
    {
        return $this->pais;
    }

    public function setPais(?Pais $pais): self
    {
        $this->pais = $pais;

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getCiudad(): ?Ciudad
    {
        return $this->ciudad;
    }

    public function setCiudad(?Ciudad $ciudad): self
    {
        $this->ciudad = $ciudad;

        return $this;
    }

    public function getSexo(): ?string
    {
        return $this->sexo;
    }

    public function setSexo(?string $sexo): self
    {
        $this->sexo = $sexo;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * @return Collection|Redes[]
     */
    public function getIduserRedes(): Collection
    {
        return $this->iduser_redes;
    }

    public function addIduserRede(Redes $iduserRede): self
    {
        if (!$this->iduser_redes->contains($iduserRede)) {
            $this->iduser_redes[] = $iduserRede;
            $iduserRede->setIdUserId($this);
        }

        return $this;
    }

    public function removeIduserRede(Redes $iduserRede): self
    {
        if ($this->iduser_redes->removeElement($iduserRede)) {
            // set the owning side to null (unless already changed)
            if ($iduserRede->getIdUserId() === $this) {
                $iduserRede->setIdUserId(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|CuentaEmail[]
     */
    public function getCuentaEmails(): Collection
    {
        return $this->cuentaEmails;
    }

    public function addCuentaEmail(CuentaEmail $cuentaEmail): self
    {
        if (!$this->cuentaEmails->contains($cuentaEmail)) {
            $this->cuentaEmails[] = $cuentaEmail;
            $cuentaEmail->setUser($this);
        }

        return $this;
    }

    public function removeCuentaEmail(CuentaEmail $cuentaEmail): self
    {
        if ($this->cuentaEmails->removeElement($cuentaEmail)) {
            // set the owning side to null (unless already changed)
            if ($cuentaEmail->getUser() === $this) {
                $cuentaEmail->setUser(null);
            }
        }

        return $this;
    }

    public function getIdempresa(): ?Empresa
    {
        return $this->idempresa;
    }

    public function setIdempresa(?Empresa $idempresa): self
    {
        $this->idempresa = $idempresa;

        return $this;
    }

    /**
     * @return Collection|Factura[]
     */
    public function getFacturas(): Collection
    {
        return $this->facturas;
    }

    public function addFactura(Factura $factura): self
    {
        if (!$this->facturas->contains($factura)) {
            $this->facturas[] = $factura;
            $factura->setUser($this);
        }

        return $this;
    }

    public function removeFactura(Factura $factura): self
    {
        if ($this->facturas->removeElement($factura)) {
            // set the owning side to null (unless already changed)
            if ($factura->getUser() === $this) {
                $factura->setUser(null);
            }
        }

        return $this;
    }

}
