<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use JMS\Serializer\Annotation as Serializer;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\Table(name="products")
 * @HasLifecycleCallbacks
 */
class Product extends BaseEntity
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
     * @ORM\Column(type="string", name="prod_describe", nullable=true)
     */
    protected $prodDescribe;

    /**
     * @ORM\Column(type="integer", name="payment", nullable=true)
     */
    protected $payment;

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

    public function setProdDescribe($prodDescribe)
    {
        $this->prodDescribe = $prodDescribe;
    }

    public function getProdDescribe()
    {
        return $this->prodDescribe;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("price")
     */
    public function getPrice()
    {
        return $this->id + 1000;
    }

    public function getPayment()
    {
        return $this->payment;
    }

    public function setPayment($payment): void
    {
        $this->payment = $payment;
    }

    /**
     * @PrePersist
     * @PreUpdate
     */
    public function validate()
    {
        $this->entityValidate(
            is_null($this->payment) || !isset($this->payment) || !is_int($this->payment)
        );
    }

}