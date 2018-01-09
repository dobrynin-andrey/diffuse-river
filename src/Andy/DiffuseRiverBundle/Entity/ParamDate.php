<?php

namespace Andy\DiffuseRiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @var int
     *
     * @ORM\Column(name="year", type="integer")
     */
    private $year;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=10)
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
     * Set year
     *
     * @param integer $year
     *
     * @return ParamDate
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set date
     *
     * @param string $date
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
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }
}

