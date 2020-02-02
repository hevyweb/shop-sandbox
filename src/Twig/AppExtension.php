<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('totalPrice', [$this, 'totalPrice']),
        ];
    }

    public function totalPrice($orderItems, $decimals = 2, $decPoint = '.', $thousandsSep = ' ')
    {
        $sum = array_sum(
            array_map(function($orderItem) {
                return $orderItem->getPrice() * $orderItem->getQuantity();
            }, $orderItems->toArray())
        );

        $price = number_format($sum, $decimals, $decPoint, $thousandsSep);

        return $price;
    }
}