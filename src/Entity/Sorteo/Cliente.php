<?php

namespace App\Entity\Sorteo;

use App\Entity\Ciudad;
use App\Entity\Estado;
use App\Repository\Sorteo\ClienteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ClienteRepository::class)
 */
class Cliente
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=1)
     */
    private $tipoDocumentoIdentidad;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $nroDocumentoIdentidad;

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
     * @ORM\Column(type="string", length=100)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=4)
     */
    private $codTelefono;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nroTelefono;

    /**
     * @ORM\ManyToOne(targetEntity=Estado::class, inversedBy="clientes")
     */
    private $estado;

    /**
     * @ORM\ManyToOne(targetEntity=Ciudad::class, inversedBy="clientes")
     */
    private $ciudad;

    /**
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $direccion;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $createBy;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $updateBy;

    public function __construct()
    {
        $this->createAt = new \DateTime();
        $this->createBy = 'system'; // Default creator, can be changed later
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getNroDocumentoIdentidad(): ?string
    {
        return $this->nroDocumentoIdentidad;
    }

    public function setNroDocumentoIdentidad(string $nroDocumentoIdentidad): self
    {
        $this->nroDocumentoIdentidad = $nroDocumentoIdentidad;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getCreateBy(): ?string
    {
        return $this->createBy;
    }

    public function setCreateBy(string $createBy): self
    {
        $this->createBy = $createBy;

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

    public function getUpdateBy(): ?string
    {
        return $this->updateBy;
    }

    public function setUpdateBy(?string $updateBy): self
    {
        $this->updateBy = $updateBy;

        return $this;
    }

    public function getCodTelefono(): ?string
    {
        return $this->codTelefono;
    }

    public function setCodTelefono(string $codTelefono): self
    {
        $this->codTelefono = $codTelefono;

        return $this;
    }

    public function getNroTelefono(): ?string
    {
        return $this->nroTelefono;
    }

    public function setNroTelefono(string $nroTelefono): self
    {
        $this->nroTelefono = $nroTelefono;

        return $this;
    }
}
