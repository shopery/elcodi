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

namespace Elcodi\Component\Cart\EventListener;

use Doctrine\Common\Persistence\ObjectManager;

use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Cart\Repository\CartRepository;
use Elcodi\Component\Cart\Services\CartManager;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;
use Elcodi\Component\User\Event\CustomerAddressOnChangeEvent;

/**
 * Class AddressCloneEventListener
 *
 * These event listener is used when an address is cloned
 *
 * Public methods:
 *
 * * updateCarts
 */
class AddressCloneEventListener
{
    /**
     * @var CartManager
     *
     * The cart manager
     */
    private $cartManager;

    /**
     * Builds an event listener
     *
     * @param CartManager $cartManager
     */
    public function __construct(
        CartManager $cartManager
    ) {
        $this->cartManager = $cartManager;
    }

    /**
     * Updates all the carts with the cloned address
     *
     * @param CustomerAddressOnChangeEvent $event Event
     */
    public function updateCarts(CustomerAddressOnChangeEvent $event)
    {
        $originalAddress = $event->getOriginalAddress();
        $clonedAddress = $event->getClonedAddress();
        $carts = $event->getCustomer()->getCarts();

        foreach ($carts as $cart) {
            /**
             * @var CartInterface $cart
             */
            $deliveryAddress = $cart->getDeliveryAddress();
            $billingAddress = $cart->getBillingAddress();

            if (
                $deliveryAddress instanceof AddressInterface
                && $deliveryAddress->getId() == $originalAddress->getId()
            ) {
                $cart->setDeliveryAddress($clonedAddress);
            }

            if (
                $billingAddress instanceof AddressInterface
                && $billingAddress->getId() == $originalAddress->getId()
            ) {
                $cart->setBillingAddress($clonedAddress);
            }

            $this->cartManager->saveCart($cart);
        }
    }
}
