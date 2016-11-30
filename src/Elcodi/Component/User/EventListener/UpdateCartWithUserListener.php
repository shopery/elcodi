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

namespace Elcodi\Component\User\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Elcodi\Component\Cart\Wrapper\CartSessionWrapper;
use Symfony\Component\Security\Core\Event\AuthenticationEvent;

use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Cart\Wrapper\CartWrapper;
use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;

/**
 * Class UpdateCartWithUserListener
 */
class UpdateCartWithUserListener
{
    /**
     * @var CartSessionWrapper
     *
     * Cart Wrapper holding reference to current Cart
     */
    private $cartSessionWrapper;

    /**
     * @var ObjectManager
     *
     * Object manager for the Cart entity
     */
    private $cartManager;

    /**
     * Construct method
     *
     * @param ObjectManager $cartManager Object Manager
     */
    public function __construct(
        CartSessionWrapper $cartSessionWrapper,
        ObjectManager $cartManager
    ) {
        $this->cartSessionWrapper = $cartSessionWrapper;
        $this->cartManager = $cartManager;
    }

    /**
     * Assign the Cart stored in session to the logged Customer.
     *
     * When a user has successfully logged in, a check is needed
     * to see if a Cart was created in session when she was not
     * logged.
     *
     * @param AuthenticationEvent $event Event
     */
    public function onAuthenticationSuccess(AuthenticationEvent $event)
    {
        $customer = $this->getLoggedCustomer($event);
        $cart = $this->getSessionCart();

        if ($this->isValidCartAndUserIsCorrectlyLogged($cart, $customer)) {
            $this->assignCartToCustomer($cart, $customer);
        }
    }

    private function isValidCartAndUserIsCorrectlyLogged($cart, $customer)
    {
        return
            ($customer instanceof CustomerInterface) &&
            ($cart instanceof CartInterface && $cart->getId());
    }

    private function getSessionCart()
    {
        return $this
            ->cartSessionWrapper
            ->get();
    }

    private function getLoggedCustomer(AuthenticationEvent $event)
    {
        return $event
            ->getAuthenticationToken()
            ->getUser();
    }

    private function assignCartToCustomer(
        CartInterface $cart,
        CustomerInterface $customer
    ) {
        $cart->setCustomer($customer);
        $this->cartManager->flush($cart);
    }
}
