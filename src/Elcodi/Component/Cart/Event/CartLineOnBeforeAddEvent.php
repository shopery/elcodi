<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014-2015 Elcodi.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 * @author Aldo Chiecchia <zimage@tiscali.it>
 * @author Elcodi Team <tech@elcodi.com>
 */

namespace Elcodi\Component\Cart\Event;

use Symfony\Component\EventDispatcher\Event;

use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;

/**
 * Class CartLineOnBeforeAddEvent
 */
class CartLineOnBeforeAddEvent extends Event
{
    /**
     * @var CartInterface
     *
     * cart
     */
    protected $cart;

    /**
     * @var PurchasableInterface
     *
     * purchasable
     */
    protected $purchasable;

    /**
     * @var int
     *
     * quantity
     */
    protected $quantity;

    /**
     * Construct method
     *
     * @param CartInterface $cart
     * @param PurchasableInterface $purchasable
     * @param int $quantity
     */
    public function __construct(
        CartInterface $cart,
        PurchasableInterface $purchasable,
        $quantity
    ) {
        $this->cart = $cart;
        $this->purchasable = $purchasable;
        $this->quantity = $quantity;
    }

    /**
     * Get cart
     *
     * @return CartInterface Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Get purchasable
     *
     * @return PurchasableInterface Purchasable
     */
    public function getPurchasable()
    {
        return $this->purchasable;
    }

    /**
     * Get quantity
     *
     * @return int Quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}
