<?php
/**
 * Created by PhpStorm.
 * User: vortgo
 * Date: 14.03.18
 * Time: 16:43
 */

namespace App\Services\Parsers;

use App\Models\Category;
use App\Models\Image;
use App\Models\Price;
use App\Models\Product;
use App\Models\Shop;
use App\Services\Parsers\DTO\Category as CategoryDTO;
use App\Services\Parsers\DTO\Product as ProductDTO;

class StoreParsedDataService
{
    /** @var  Shop */
    private $shop;
    /** @var Category */
    private $category;

    /**
     * Save parsed data
     *
     * @param ProductDTO $product
     * @param CategoryDTO $category
     * @param string $shop
     */
    public function save(ProductDTO $product, CategoryDTO $category, string $shop)
    {
        $shopModel = $this->saveShop($shop);
        $categoryModel = $this->saveCategory($category);
        $this->saveProduct($product, $categoryModel, $shopModel);
    }

    /**
     * Save and cache shop
     *
     * @param $shop
     * @return Shop
     */
    private function saveShop($shop): Shop
    {
        if ($this->shop && $this->shop->alias === strtolower($shop)) {
            return $this->shop;
        }

        if ($existedShop = Shop::whereAlias(strtolower($shop))->first()) {
            $this->shop = $existedShop;
            return $this->shop;
        }

        $this->shop = Shop::create([
            'name'  => $shop,
            'alias' => strtolower($shop),
        ]);
        return $this->shop;
    }

    /**
     * Save and cache category
     *
     * @param CategoryDTO $category
     * @return Category|CategoryDTO
     */
    private function saveCategory(CategoryDTO $category): Category
    {
        if ($this->category && $this->category->name === $category->getName()) {
            return $this->category;
        }

        if ($existCategory = Category::whereName($category->getName())->first()) {
            $this->category = $existCategory;
            return $existCategory;
        }

        $this->category = Category::create([
            'name' => $category->getName(),
        ]);
        return $this->category;
    }

    /**
     * Save product
     *
     * @param ProductDTO $product
     * @param Category $categoryModel
     * @param Shop $shopModel
     * @return Product
     */
    private function saveProduct(ProductDTO $product, Category $categoryModel, Shop $shopModel): Product
    {
        $codePrefix = $product->getShopCodePrefix();
        if (!$productModel = Product::whereCode($product->getCode())->whereShopPrefix($codePrefix)->first()) {
            return $this->createNewProduct($product, $categoryModel, $shopModel);
        }

        $this->attachImages($product, $productModel);
        $this->attachPrice($product, $productModel, $shopModel);
        return $productModel;
    }

    /**
     * Create new product
     *
     * @param ProductDTO $product
     * @param Category $categoryModel
     * @param Shop $shopModel
     * @return Product
     */
    private function createNewProduct(ProductDTO $product, Category $categoryModel, Shop $shopModel): Product
    {
        $productModel = Product::create([
            'name' => $product->getName(),
            'code' => $product->getCode(),
            'shop_prefix' => $product->getShopCodePrefix() ?? null,
        ]);
        $productModel->categories()->attach($categoryModel->id);
        $this->attachPrice($product, $productModel, $shopModel);
        $this->attachImages($product, $productModel);
        return $productModel;
    }

    /**
     * Attach price to product
     *
     * @param ProductDTO $product
     * @param Product $productModel
     * @param Shop $shopModel
     * @return Price
     */
    private function attachPrice(ProductDTO $product, Product $productModel, Shop $shopModel): Price
    {
        return $priceModel = Price::create([
            'product_id' => $productModel->id,
            'shop_id'    => $shopModel->id,
            'price'      => $product->getPrice(),
        ]);
    }

    /**
     * Attach images to product
     *
     * @param ProductDTO $product
     * @param Product $productModel
     */
    private function attachImages(ProductDTO $product, Product $productModel)
    {
        if (!count($product->getImageLinks())) {
            return;
        }

        foreach ($product->getImageLinks() as $imageLink) {
            $name = substr($imageLink, strrpos($imageLink, '/') + 1);

            if ($name === 'undefined.jpg') {
                continue;
            }

            if (Image::whereFilename($name)->whereProductId($productModel->id)->first()) {
                continue;
            }

            $path = 'products/' . $productModel->id . '/' . $name;
            $contents = file_get_contents($imageLink);
            \Storage::disk('images')->put($path, $contents);

            Image::create([
                'filename'   => $name,
                'product_id' => $productModel->id,
            ]);
        }
    }
}
