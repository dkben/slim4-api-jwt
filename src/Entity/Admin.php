<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AdminRepository")
 * @ORM\Table(name="admiin")
 */
class Admin
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
     * @ORM\Column(type="string")
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="datetime")
     */
//    protected $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
//    protected $updated_at;

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

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getPassword()
    {
        return $this->password;
    }

//    public function setCreatedAt()
//    {
//        $this->created_at = date('Y-m-d H:i:s');
//    }
//
//    public function getCreatedAt()
//    {
//        return $this->created_at;
//    }
//
//    public function setUpdatedAt()
//    {
//        $this->updated_at = date('Y-m-d H:i:s');
//    }
//
//    public function getUpdatedAt()
//    {
//        return $this->updated_at;
//    }

}