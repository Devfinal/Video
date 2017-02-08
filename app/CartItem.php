<?php

namespace App;

class CartItem
{
    /**
     * The item product.
     *
     * @var Product
     */
    public $product;

    /**
     * The item size.
     *
     * @var string|null
     */
    public $size;

    /**
     * @param Product $product
     * @param string|null $size
     */
    public function __construct(Product $product, $size = null)
    {
        $this->product = $product;
        $this->size = $size;
    }
}
