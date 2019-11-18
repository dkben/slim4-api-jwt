<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Exception;


/**
 * @ORM\Entity(repositoryClass="App\Repository\FilmRepository")
 * @ORM\Table(name="film")
 * @HasLifecycleCallbacks
 */
class Film extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="string", name="film_describe", nullable=true)
     */
    protected $filmDescribe;

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setFilmDescribe($filmDescribe)
    {
        $this->filmDescribe = $filmDescribe;
    }

    public function getFilmDescribe()
    {
        return $this->filmDescribe;
    }

    /**
     * @PrePersist
     * @PreUpdate
     */
    public function validate()
    {
//        if ($this->name == 'ben') {
//            throw new Exception("is ben!");
//        }
    }

}