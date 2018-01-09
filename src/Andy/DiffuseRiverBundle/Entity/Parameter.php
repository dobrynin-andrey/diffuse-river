<?php

namespace Andy\DiffuseRiverBundle\Entity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * Parameter
 *
 * @ORM\Table(name="parameter")
 * @ORM\Entity(repositoryClass="Andy\DiffuseRiverBundle\Repository\ParameterRepository")
 * @UniqueEntity("code")
 */
class Parameter
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @ORM\OneToMany(targetEntity="Andy\DiffuseRiverBundle\Entity\ParamValue", mappedBy="parameterId")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Название не должно быть пустым."
     * )
     * @Assert\Length(
     *     min="3",
     *     max="255",
     *     minMessage="Название не должно быть менее 3 символов.",
     *     maxMessage="Название не должно быть более 255 символов."
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     *
     * @Assert\NotBlank(
     *     message="Поле код не должно быть пустым."
     * )
     * @Assert\Length(
     *     min="2",
     *     max="255",
     *     minMessage="Поле код не должно быть менее 2 символов.",
     *     maxMessage="Поле код не должно быть более 255 символов."
     * )
     *
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="ed_izm", type="string", length=255)
     *
     * @Assert\NotBlank(
     *     message="Поле единицы измерения не должно быть пустым."
     * )
     * @Assert\Length(
     *     min="2",
     *     max="255",
     *     minMessage="Поле единицы измерения не должно быть менее 2 символов.",
     *     maxMessage="Поле единицы измерения не должно быть более 255 символов."
     * )
     *
     */
    private $edIzm;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Parameter
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Parameter
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set edIzm
     *
     * @param string $edIzm
     *
     * @return Parameter
     */
    public function setEdIzm($edIzm)
    {
        $this->edIzm = $edIzm;

        return $this;
    }

    /**
     * Get edIzm
     *
     * @return string
     */
    public function getEdIzm()
    {
        return $this->edIzm;
    }
}

