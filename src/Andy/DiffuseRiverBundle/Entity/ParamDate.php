<?php

namespace Andy\DiffuseRiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * ParamDate
 *
 * @ORM\Table(name="param_date")
 * @ORM\Entity(repositoryClass="Andy\DiffuseRiverBundle\Repository\ParamDateRepository")
 */
class ParamDate
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @ORM\OneToMany(targetEntity="Andy\DiffuseRiverBundle\Entity\ParamValue", mappedBy="paramDateId")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="point_id", type="integer")
     *
     * @ORM\ManyToOne(targetEntity="Andy\DiffuseRiverBundle\Entity\Point", inversedBy="id")
     * @ORM\JoinColumn(name="point_id", referencedColumnName="id")
     */
    private $pointId;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * Set pointId
     *
     * @param integer $pointId
     *
     * @return ParamDate
     */
    public function setPointId($pointId)
    {
        $this->pointId = $pointId;

        return $this;
    }

    /**
     * Get pointId
     *
     * @return int
     */
    public function getPointId()
    {
        return $this->pointId;
    }


    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ParamDate
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}

