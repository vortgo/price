<?php
/**
 * Created by PhpStorm.
 * User: vortgo
 * Date: 14.03.18
 * Time: 14:50
 */

namespace App\Services\Parsers\DTO;


class Product
{
    /** @var  string */
    private $code;
    /** @var  string */
    private $name;
    /** @var  float */
    private $price;
    /** @var  string */
    private $imageLink;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Product
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = trim(str_replace("\n", "", $name));
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageLink()
    {
        return $this->imageLink;
    }

    /**
     * @param string $imageLink
     * @return Product
     */
    public function setImageLink($imageLink)
    {
        $this->imageLink = $imageLink;
        return $this;
    }
}
