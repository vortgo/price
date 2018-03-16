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
    /** @var  array */
    private $imageLinks = [];
    /** @var  string */
    private $shopCodePrefix = null;

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
     * @return array
     */
    public function getImageLinks()
    {
        return $this->imageLinks;
    }

    /**
     * @param string $imageLink
     * @return Product
     */
    public function setImageLinks($imageLink)
    {
        $this->imageLinks[] = $imageLink;
        return $this;
    }

    /**
     * @return string
     */
    public function getShopCodePrefix()
    {
        return $this->shopCodePrefix === '' ? null : $this->shopCodePrefix;
    }

    /**
     * @param string $shopCodePrefix
     * @return Product
     */
    public function setShopCodePrefix(string $shopCodePrefix): Product
    {
        $this->shopCodePrefix = $shopCodePrefix;
        return $this;
    }
}
