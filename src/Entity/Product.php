<?php

namespace App\Entity;

use App\Exception\ExceptionResponse;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use InvalidArgumentException;
use Webmozart\Assert\Assert;


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
        // 參考 https://github.com/webmozart/assert
        try {
            Assert::stringNotEmpty($this->prodDescribe, "prodDescribe: 請輸入商品描述");
            Assert::integer($this->payment, "payment: 請輸入整數值");
        } catch (InvalidArgumentException $e) {
            ExceptionResponse::response($e->getMessage(), 400);
        }

        // 棄用，改用 Webmozart Assert 做驗證
//        $this->entityValidate(
//            is_null($this->payment) || !isset($this->payment) || !is_int($this->payment)
//        );
    }

}