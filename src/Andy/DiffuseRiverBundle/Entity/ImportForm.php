<?php

namespace Andy\DiffuseRiverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImportForm
 *
 */
class ImportForm
{
    /**
     * @Assert\NotBlank(
     *     message="Файл не выбран!"
     * )
     *
     */

    protected $file;

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

}

