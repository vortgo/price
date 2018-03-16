<?php
/**
 * Created by PhpStorm.
 * User: vortgo
 * Date: 14.03.18
 * Time: 9:56
 */

namespace App\Services\Parsers\Novus;


use App\Services\Parsers\DTO\Category;
use App\Services\Parsers\DTO\Product;
use App\Services\Parsers\StoreParsedDataService;
use Symfony\Component\DomCrawler\Crawler;

class NovusParserService
{
    private $mainUrl = 'https://novus.zakaz.ua';
    private $shop = 'Novus';
    /** @var StoreParsedDataService */
    private $storeDataService;

    public function __construct()
    {
        $this->storeDataService = new StoreParsedDataService();
    }

    /**
     * Parse categories
     *
     * @return array
     */
    public function parseCategories()
    {
        /** @var  $categories */
        $categories = [];
        $html = file_get_contents($this->mainUrl);
        $crawler = new Crawler($html);
        $menu = $crawler->filter('ul.menu li');
        foreach ($menu as $item) {
            $li = new Crawler($item, $this->mainUrl);
            $a = $li->filter('a');
            $category = new Category();
            $category->setLink($a->attr('href'))->setName($a->text());
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * Update products price
     *
     * @param Category $category
     */
    public function updateProductsPrices(Category $category)
    {
        $pageUrl = $this->makeUrlToCategory($category->getLink());
        $lastPage = $this->getLastPage($pageUrl);
        for ($i = 1; $i <= $lastPage; $i++) {
            $pageUrl = $this->makeUrlToCategory($category->getLink(), $i);
            $this->updateProductsPriceOnPage($pageUrl,$category);
        }
    }

    /**
     * Update products price on one page by url
     *
     * @param $pageUrl
     * @param Category $category
     */
    public function updateProductsPriceOnPage($pageUrl,Category $category)
    {
        $html = file_get_contents($pageUrl);
        $products = $this->parseProductsList($html);
        $this->storeProductsOnPage($products, $category);
    }

    /**
     * Parse products list
     *
     * @param $html
     * @return array
     */
    public function parseProductsList($html)
    {
        $parsedProducts = [];
        $crawler = new Crawler($html);
        $products = $crawler->filter('div.one-product') ?? [];
        foreach ($products as $item) {
            $item = new Crawler($item);
            $product = new Product();
            $code = $item->filter('form.add-to-cart-js')->attr('data') ?? null;
            $price = $item->filter('span.price')->text() . '.' . $item->filter('span.kopeiki')->text() ?? null;
            $name = $item->filter('div.one-product-name')->text() ?? null;
            $image = $item->filter('div.one-product-image img')->attr('src') ?? null;
            if ($code && $price && $name)
                $product->setCode(preg_replace('~\D+~','',$code))
                    ->setShopCodePrefix(preg_replace ("/[^a-zа-я\s]/si","",$code))
                    ->setName($name)
                    ->setPrice($price)
                    ->setImageLinks($this->getImageLink($image));
            $parsedProducts[] = $product;
        }
        return $parsedProducts;
    }

    /**
     * Make url to category
     *
     * @param $categoryUrl
     * @param int $page
     * @return string
     */
    public function makeUrlToCategory($categoryUrl, $page = 1)
    {
        return $this->mainUrl . $categoryUrl . '?page=' . $page;
    }

    /**
     * Get last page
     *
     * @param $fullUrl
     * @return int|string
     */
    public function getLastPage($fullUrl)
    {
        $html = file_get_contents($fullUrl);
        $crawler = new Crawler($html);
        $list = $crawler->filter('.pagination.pagination-centered');
        if ($count = $list->filter('a')->count()) {
            return $list->filter('a')->eq($count - 2)->text();
        }

        return 0;
    }

    /**
     * Store array of Product
     *
     * @param array $products
     * @param Category $category
     */
    private function storeProductsOnPage(array $products, Category $category)
    {
        foreach ($products as $product) {
            $this->storeDataService->save($product, $category, $this->shop);
        }
    }

    /**
     * Sanitize url link
     *
     * @param $url
     * @return string
     */
    private function getImageLink($url)
    {
        if (strpos($url, '//') === 0) {
            $url = 'https:' . $url;
        } elseif (strpos($url, '/') === 0) {
            $url = $this->mainUrl . $url;
        }
        return $url;
    }
}
