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

namespace Elcodi\Component\Cart\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Cart\Entity\Interfaces\CartLineInterface;
use Elcodi\Component\Cart\EventDispatcher\CartEventDispatcher;
use Elcodi\Component\Cart\EventDispatcher\CartLineEventDispatcher;
use Elcodi\Component\Cart\Factory\CartFactory;
use Elcodi\Component\Cart\Factory\CartLineFactory;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;
use Elcodi\Component\Product\Entity\Interfaces\PurchasableInterface;

/**
 * Cart manager service
 *
 * This service hosts all cart and cartLine related actions.
 * This class has not states, so every method just has input parameters and
 * return some output values.
 *
 * Some of these methods also can dispatch some Cart events
 *
 * Api Methods:
 *
 * * addLine(AbstractCart, CartLine) : self
 * * removeLine(AbstractCart, CartLine) : self
 * * silentRemoveLine(AbstractCart, CartLine) : self
 * * emptyLines() : self
 *
 * * increaseCartLineQuantity(CartLine, $quantity) : self
 * * decreaseCartLineQuantity(CartLine, $quantity) : self
 * * setCartLineQuantity(CartLine, $quantity) : self
 *
 * * addProduct(AbstractCart, PurchasableInterface, $quantity) : self
 */
class CartManager
{
    /**
     * @var CartEventDispatcher
     *
     * Cart Event Dispatcher
     */
    private $cartEventDispatcher;

    /**
     * @var CartLineEventDispatcher
     *
     * CartLine Event Dispatcher
     */
    private $cartLineEventDispatcher;

    /**
     * @var CartLineFactory
     *
     * CartLine Factory
     */
    private $cartLineFactory;

    /**
     * @var ObjectManager
     *
     * ObjectManager for Cart entity
     */
    private $cartObjectManager;

    /**
     * Construct method
     *
     * @param CartEventDispatcher     $cartEventDispatcher     Cart Event Dispatcher
     * @param CartLineEventDispatcher $cartLineEventDispatcher CartLine Event dispatcher
     * @param CartLineFactory         $cartLineFactory         CartLine factory
     * @param ObjectManager           $cartObjectManager       Cart object manager
     */
    public function __construct(
        CartEventDispatcher $cartEventDispatcher,
        CartLineEventDispatcher $cartLineEventDispatcher,
        CartLineFactory $cartLineFactory,
        ObjectManager $cartObjectManager
    ) {
        $this->cartEventDispatcher = $cartEventDispatcher;
        $this->cartLineEventDispatcher = $cartLineEventDispatcher;
        $this->cartLineFactory = $cartLineFactory;
        $this->cartObjectManager = $cartObjectManager;
    }

    /**
     * Removes CartLine from Cart
     *
     * This method dispatches all Cart Load events, if defined.
     * If this method is called in CartCheckEvents, $dispatchEvents should be
     * set to false
     *
     * @param CartInterface     $cart     Cart
     * @param CartLineInterface $cartLine Cart line
     *
     * @return $this Self object
     */
    public function removeLine(
        CartInterface $cart,
        CartLineInterface $cartLine
    ) {
        $this->silentRemoveLine($cart, $cartLine);

        $this
            ->cartEventDispatcher
            ->dispatchCartLoadEvents($cart);

        return $this;
    }

    /**
     * Removes CartLine from Cart
     *
     * @param CartInterface     $cart     Cart
     * @param CartLineInterface $cartLine Cart line
     *
     * @return $this Self object
     */
    public function silentRemoveLine(
        CartInterface $cart,
        CartLineInterface $cartLine
    ) {
        $cart->removeCartLine($cartLine);

        $this
            ->cartLineEventDispatcher
            ->dispatchCartLineOnRemoveEvent(
                $cart,
                $cartLine
            );

        return $this;
    }

    /**
     * Empty cart.
     *
     * This method dispatches all Cart Load events
     *
     * @param CartInterface $cart Cart
     *
     * @return $this Self object
     */
    public function emptyLines(
        CartInterface $cart
    ) {
        $cart
            ->getCartLines()
            ->map(function (CartLineInterface $cartLine) use ($cart) {

                $this->silentRemoveLine($cart, $cartLine);
            });

        $this
            ->cartEventDispatcher
            ->dispatchCartOnEmptyEvent($cart);

        $this
            ->cartEventDispatcher
            ->dispatchCartLoadEvents($cart);

        return $this;
    }

    /**
     * Edit CartLine
     *
     * The line is updated only if it belongs to a Cart
     *
     * This method dispatches all Cart Check and Load events
     *
     * @param CartLineInterface    $cartLine    Cart line
     * @param PurchasableInterface $purchasable purchasable to be edited
     * @param integer              $quantity    item quantity
     *
     * @return $this Self object
     */
    public function editCartLine(
        CartLineInterface $cartLine,
        PurchasableInterface $purchasable,
        $quantity
    ) {
        $cart = $cartLine->getCart();

        if (!($cart instanceof CartInterface)) {
            return $this;
        }

        $cartLine->setPurchasable($purchasable);
        $this->setCartLineQuantity($cartLine, $quantity);

        return $this;
    }

    /**
     * Adds quantity to cartLine
     *
     * If quantity is higher than item stock, throw exception
     *
     * This method dispatches all Cart Check and Load events
     *
     * @param CartLineInterface $cartLine Cart line
     * @param integer           $quantity Number of units to decrease CartLine quantity
     *
     * @return $this Self object
     */
    public function increaseCartLineQuantity(
        CartLineInterface $cartLine,
        $quantity
    ) {
        if (!is_int($quantity) || empty($quantity)) {
            return $this;
        }

        $newQuantity = $cartLine->getQuantity() + $quantity;

        return $this->setCartLineQuantity(
            $cartLine,
            $newQuantity
        );
    }

    /**
     * Removes quantity to cartLine
     *
     * If quantity is 0, deletes whole line
     *
     * This method dispatches all Cart Check and Load events
     *
     * @param CartLineInterface $cartLine Cart line
     * @param integer           $quantity Number of units to decrease CartLine quantity
     *
     * @return $this Self object
     */
    public function decreaseCartLineQuantity(
        CartLineInterface $cartLine,
        $quantity
    ) {
        if (!is_int($quantity) || empty($quantity)) {
            return $this;
        }

        return $this->increaseCartLineQuantity(
            $cartLine,
            ($quantity * -1)
        );
    }

    /**
     * Sets quantity to cartLine
     *
     * If quantity is higher than item stock, throw exception
     *
     * This method dispatches all Cart Check and Load events
     *
     * @param CartLineInterface $cartLine Cart line
     * @param integer           $quantity CartLine quantity to set
     *
     * @return $this Self object
     */
    public function setCartLineQuantity(
        CartLineInterface $cartLine,
        $quantity
    ) {
        if (!is_numeric($quantity)) {
            return $this;
        }

        $cart = $cartLine->getCart();
        if ($cart instanceof CartInterface === false) {
            return $this;
        }

        if ($quantity <= 0) {
            $this->silentRemoveLine($cart, $cartLine);
        } else {
            $previousQuantity = $cartLine->getQuantity();
            $cartLine->setQuantity($quantity);

            $this
                ->cartLineEventDispatcher
                ->dispatchCartLineOnEditEvent(
                    $cart,
                    $cartLine,
                    $previousQuantity
                );
        }

        $this
            ->cartEventDispatcher
            ->dispatchCartLoadEvents($cart);

        return $this;
    }

    /**
     * Add a Purchasable to Cart as a new CartLine
     *
     * This method creates a new CartLine and set item quantity
     * correspondingly.
     *
     * If the Purchasable is already in the Cart, it just increments
     * item quantity by $quantity
     *
     * @param CartInterface        $cart        Cart
     * @param PurchasableInterface $purchasable Product or Variant to add
     * @param integer              $quantity    Number of units to set or increase
     *
     * @return $this Self object
     */
    public function addProduct(
        CartInterface $cart,
        PurchasableInterface $purchasable,
        $quantity
    ) {
        /**
         * If quantity is not a number or is 0 or less, product is not added
         * into cart
         */
        if (!is_int($quantity) || $quantity <= 0) {
            return $this;
        }

        foreach ($cart->getCartLines() as $cartLine) {

            /**
             * @var CartLineInterface $cartLine
             */
            if (
                (get_class($cartLine->getPurchasable()) === get_class($purchasable)) &&
                ($cartLine->getPurchasable()->getId() == $purchasable->getId())
            ) {

                /**
                 * Product already in the Cart, increase quantity
                 */

                return $this->increaseCartLineQuantity($cartLine, $quantity);
            }
        }

        $cartLine = $this->cartLineFactory->create();
        $cartLine
            ->setPurchasable($purchasable)
            ->setQuantity($quantity);

        $this->addLine($cart, $cartLine);

        return $this;
    }

    /**
     * Remove a Purchasable from Cart
     *
     * This method removes a Purchasable from the Cart.
     *
     * If the Purchasable is already in the Cart, it just decreases
     * item quantity by $quantity
     *
     * @param CartInterface        $cart        Cart
     * @param PurchasableInterface $purchasable Product or Variant to add
     * @param integer              $quantity    Number of units to set or increase
     *
     * @return $this Self object
     */
    public function removeProduct(
        CartInterface $cart,
        PurchasableInterface $purchasable,
        $quantity
    ) {
        /**
         * If quantity is not a number or is 0 or less, product is not removed
         * from cart
         */
        if (!is_int($quantity) || $quantity <= 0) {
            return $this;
        }

        foreach ($cart->getCartLines() as $cartLine) {
            /**
             * @var CartLineInterface $cartLine
             */
            if (
                (get_class($cartLine->getPurchasable()) === get_class($purchasable)) &&
                ($cartLine->getPurchasable()->getId() == $purchasable->getId())
            ) {
                /**
                 * Product already in the Cart, decrease quantity
                 */

                return $this->decreaseCartLineQuantity($cartLine, $quantity);
            }
        }

        return $this;
    }

    /**
     * Adds cartLine to Cart
     *
     * This method dispatches all Cart Check and Load events
     * It should NOT be used to add a Purchasable to a Cart,
     * by manually passing a newly crafted CartLine, since
     * no product duplication check is performed: in that
     * case CartManager::addProduct should be used
     *
     * @param CartInterface     $cart     Cart
     * @param CartLineInterface $cartLine Cart line
     *
     * @return $this Self object
     */
    private function addLine(
        CartInterface $cart,
        CartLineInterface $cartLine
    ) {
        $cartLine->setCart($cart);
        $cart->addCartLine($cartLine);

        $this
            ->cartLineEventDispatcher
            ->dispatchCartLineOnAddEvent(
                $cart,
                $cartLine
            );

        $this
            ->cartEventDispatcher
            ->dispatchCartLoadEvents($cart);

        return $this;
    }

    /**
     * Sets the billing address and dispatchs an event.
     *
     * @param CartInterface    $cart
     * @param AddressInterface $address
     */
    public function setBillingAddress(
        CartInterface $cart,
        AddressInterface $address
    ) {
        $cart->setBillingAddress($address);

        $this
            ->cartEventDispatcher
            ->dispatchCartBillingAddressOnChangeEvent($cart);
    }

    /**
     * Sets the delivery address and dispatchs an event.
     *
     * @param CartInterface    $cart
     * @param AddressInterface $address
     */
    public function setDeliveryAddress(
        CartInterface $cart,
        AddressInterface $address
    ) {
        $cart->setDeliveryAddress($address);

        $this
            ->cartEventDispatcher
            ->dispatchCartDeliveryAddressOnChangeEvent($cart);
    }

    /**
     * Saves the given cart
     *
     * @param CartInterface $cart
     */
    public function saveCart(CartInterface $cart)
    {
        if (!$cart->getCartLines()->isEmpty()) {
            $this->cartObjectManager->persist($cart);

            $this
                ->cartObjectManager
                ->flush(array_merge(
                    $cart->getCartLines()->toArray(),
                    [
                        $cart,
                    ]
                ));
        }
    }
}
