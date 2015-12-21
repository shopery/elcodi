<?php

namespace Elcodi\Component\Cart\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;
use Elcodi\Component\User\Event\CustomerAddressOnChangeEvent;


/**
 * Class CustomerAddressClonedListener
 *
 * @author Roger Gros <roger@gros.cat>
 */
class CustomerAddressClonedListener
{
    /**
     * @var ObjectManager
     *
     * The cart object manager
     */
    private $cartObjectManager;

    /**
     * Builds an event listener
     *
     * @param ObjectManager  $cartObjectManager
     */
    public function __construct(
        ObjectManager $cartObjectManager
    ) {
        $this->cartObjectManager = $cartObjectManager;
    }

    public function customerAddressCloned(CustomerAddressOnChangeEvent $event)
    {
        $oldAddress = $event->getOriginalAddress();
        $newAddress = $event->getClonedAddress();
        $carts = $event->getCustomer()->getCarts();

        foreach ($carts as $cart) {
            /** @var CartInterface $cart */
            if (!$cart->isOrdered()) {
                $deliveryAddress = $cart->getDeliveryAddress();
                $billingAddress = $cart->getBillingAddress();

                if (
                    $deliveryAddress instanceof AddressInterface
                    && $deliveryAddress->getId() == $oldAddress->getId()
                ) {
                    $cart->setDeliveryAddress($newAddress);
                }

                if (
                    $billingAddress instanceof AddressInterface
                    && $billingAddress->getId() == $oldAddress->getId()
                ) {
                    $cart->setBillingAddress($newAddress);
                }

                $this->cartObjectManager->flush($cart);
            }
        }
    }
}
