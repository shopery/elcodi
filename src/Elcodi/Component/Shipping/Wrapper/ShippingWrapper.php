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

namespace Elcodi\Component\Shipping\Wrapper;

use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Shipping\Entity\ShippingMethod;
use Elcodi\Component\Shipping\EventDispatcher\ShippingEventDispatcher;

/**
 * Class ShippingWrapper
 */
class ShippingWrapper
{
    /**
     * @var ShippingEventDispatcher
     *
     * Shipping event dispatcher
     */
    private $shippingEventDispatcher;

    /**
     * @var array|null
     */
    private $shippingMethods;

    /**
     * Construct
     *
     * @param ShippingEventDispatcher $shippingEventDispatcher Shipping event
     *                                                         dispatcher
     */
    public function __construct(ShippingEventDispatcher $shippingEventDispatcher
    ) {
        $this->shippingEventDispatcher = $shippingEventDispatcher;
    }

    /**
     * Get loaded shipping methods given a cart
     *
     * @param CartInterface $cart Cart
     *
     * @return ShippingMethod[] Shipping methods
     */
    public function get(CartInterface $cart)
    {
        return $this->getShippingMethods($cart);
    }

    /**
     * Get loaded shipping method given its id
     *
     * @param CartInterface $cart             Cart
     * @param string        $shippingMethodId Shipping method id
     *
     * @return ShippingMethod|null Required shipping method
     */
    public function getOneById(CartInterface $cart, $shippingMethodId)
    {
        $shippingMethods = $this->getShippingMethods($cart);

        return array_reduce(
            $shippingMethods,
            function (
                $foundShippingMethod,
                ShippingMethod $shippingMethod
            ) use ($shippingMethodId) {

                return ($shippingMethodId === $shippingMethod->getId())
                    ? $shippingMethod
                    : $foundShippingMethod;
            },
            null
        );
    }

    /**
     * Clears the shipping method
     */
    public function clear()
    {
        $this->shippingMethods = null;
    }

    /**
     * Gets the shipping methods
     *
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function getShippingMethods(CartInterface $cart)
    {
        if (null == $this->shippingMethods) {
            $this->shippingMethods = $this->collectShippingMethods($cart);
        }
        return $this->shippingMethods;
    }

    /**
     * @param CartInterface $cart
     *
     * @return array
     */
    protected function collectShippingMethods(CartInterface $cart)
    {
        return $this
            ->shippingEventDispatcher
            ->dispatchPaymentCollectionEvent($cart);
    }
}
