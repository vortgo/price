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
use Symfony\Component\DomCrawler\Crawler;

class NovusParserService
{
    private $mainUrl = 'https://novus.zakaz.ua';

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
            $html = file_get_contents($pageUrl);
            $products = $this->parseProductsList($html);
            dd($products);
        }
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
                $product->setCode($code)
                    ->setName($name)
                    ->setPrice($price)
                    ->setImageLink($image);
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
    private function getLastPage($fullUrl)
    {
        $html = file_get_contents($fullUrl);
        $crawler = new Crawler($html);
        $lastLink = $crawler->filter('.pagination.pagination-centered')
            ->filter('span.page a');
        if ($lastLink) {
            return $lastLink->text();
        }
        return 0;
    }


}
