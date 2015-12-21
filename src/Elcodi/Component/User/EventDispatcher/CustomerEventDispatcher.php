<?php

namespace Elcodi\Component\User\EventDispatcher;

use Elcodi\Component\Core\EventDispatcher\Abstracts\AbstractEventDispatcher;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;
use Elcodi\Component\User\ElcodiCustomerEvents;
use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;
use Elcodi\Component\User\Event\CustomerAddressOnChangeEvent;

/**
 * Class CustomerEventDispatcher
 *
 * @author Roger Gros <roger@gros.cat>
 */
class CustomerEventDispatcher extends AbstractEventDispatcher
{
    /**
     * Dispatch user address changed event
     *
     * @param CustomerInterface $customer
     * @param AddressInterface  $originalAddress
     * @param AddressInterface  $clonedAddress
     *
     * @return $this Self object
     */
    public function dispatchCustomerAddressOnChangeEvent(
        CustomerInterface $customer,
        AddressInterface $originalAddress,
        AddressInterface $clonedAddress
    ) {
        $event = new CustomerAddressOnChangeEvent(
            $customer,
            $originalAddress,
            $clonedAddress
        );

        $this
            ->eventDispatcher
            ->dispatch(
                ElcodiCustomerEvents::ADDRESS_ONCHANGE,
                $event
            );

        return $this;
    }
}
