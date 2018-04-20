<?php

namespace Andy\DiffuseRiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Result
 *
 * @ORM\Table(name="result")
 * @ORM\Entity(repositoryClass="Andy\DiffuseRiverBundle\Repository\ResultRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Result
{

    // В конструкторе вызываем функции по добавлению даты создания и обновления
    // Добавил @ORM\HasLifecycleCallbacks в описание класса, чтобы можно было менять дату при обновлении изменений
    public function __construct()
    {
        $this->setCreatedDate(new \DateTime());
        $this->setUpdatedDate(new \DateTime());
    }

    /**
     * @ORM\PreUpdate
     */
    // Функция работает вместе с @ORM\HasLifecycleCallbacks
    public function setUpdatedValue()
    {
        $this->setUpdatedDate(new \DateTime());
    }


    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text")
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    private $updatedDate;

    /**
     * @var int
     *
     * @ORM\Column(name="project_id", type="integer")
     * @ORM\ManyToOne(targetEntity="Andy\DiffuseRiverBundle\Entity\Project", inversedBy="id")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $projectId;

    /**
     * @var int
     *
     * @ORM\Column(name="point_id", type="integer")
     * @ORM\ManyToOne(targetEntity="Andy\DiffuseRiverBundle\Entity\Point", inversedBy="id")
     * @ORM\JoinColumn(name="point_id", referencedColumnName="id")
     *
     */
    private $pointId;

    /**
     * @var int
     *
     * @ORM\Column(name="parameter_id", type="integer")
     * @ORM\ManyToOne(targetEntity="Andy\DiffuseRiverBundle\Entity\Parameter", inversedBy="id")
     * @ORM\JoinColumn(name="parameter_id", referencedColumnName="id")
     */
    private $parameterId;


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
     * Set value
     *
     * @param string $value
     *
     * @return Result
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

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Result
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @param \DateTime $updatedDate
     *
     * @return Result
     */
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set projectId
     *
     * @param integer $projectId
     *
     * @return Result
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Set pointId
     *
     * @param integer $pointId
     *
     * @return Result
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
     * Set parameterId
     *
     * @param integer $parameterId
     *
     * @return Result
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
}

