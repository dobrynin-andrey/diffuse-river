<?php

namespace Andy\DiffuseRiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ParamValue
 *
 * @ORM\Table(name="param_value")
 * @ORM\Entity(repositoryClass="Andy\DiffuseRiverBundle\Repository\ParamValueRepository")
 */
class ParamValue
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="param_date_id", type="integer")
     *
     * @ORM\ManyToOne(targetEntity="Andy\DiffuseRiverBundle\Entity\ParamDate", inversedBy="id")
     * @ORM\JoinColumn(name="param_date_id", referencedColumnName="id")
     */
    private $paramDateId;

    /**
     * @var int
     *
     * @ORM\Column(name="parameter_id", type="integer")
     *
     * @ORM\ManyToOne(targetEntity="Andy\DiffuseRiverBundle\Entity\Parameter", inversedBy="id")
     * @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     */
    private $parameterId;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;


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
     * Set paramDateId
     *
     * @param integer $paramDateId
     *
     * @return ParamValue
     */
    public function setParamDateId($paramDateId)
    {
        $this->paramDateId = $paramDateId;

        return $this;
    }

    /**
     * Get paramDateId
     *
     * @return int
     */
    public function getParamDateId()
    {
        return $this->paramDateId;
    }

    /**
     * Set parameterId
     *
     * @param integer $parameterId
     *
     * @return ParamValue
     */
    public function setParameterId($parameterId)
    {
        $this->parameterId = $parameterId;

        return $this;
    }

    /**
     * Get parameterId
     *
     * @return int
     */
    public function getParameterId()
    {
        return $this->parameterId;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ParamValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}

