<?php

namespace Andy\DiffuseRiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Point
 *
 * @ORM\Table(name="point")
 * @ORM\Entity(repositoryClass="Andy\DiffuseRiverBundle\Repository\PointRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Point
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
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="4",
     *     max="255",
     *     minMessage="Название не должно быть менее 4 символов.",
     *     maxMessage="Название не должно быть более 255 символов."
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="coords", type="string", length=500)
     *
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min="4",
     *     max="500",
     *     minMessage="Координаты не должны быть менее 4 символов.",
     *     maxMessage="Координаты не должны быть более 500 символов."
     * )
     */
    private $coords;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     *
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
     * @return Point
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
     * Set coords
     *
     * @param string $coords
     *
     * @return Point
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;

        return $this;
    }

    /**
     * Get coords
     *
     * @return string
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     *
     * @return Point
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
     * @return Point
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
     * @return Point
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
}

