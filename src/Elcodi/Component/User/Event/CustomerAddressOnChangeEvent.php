<?php

namespace Elcodi\Component\User\Event;

use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;
use Symfony\Component\EventDispatcher\Event;

use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;

/**
 * Class CustomerAddressClonedEvent
 *
 * @author Roger Gros <roger@gros.cat>
 */
class CustomerAddressOnChangeEvent extends Event
{
    /**
     * @var AddressInterface
     *
     * The original address being cloned
     */
    private $originalAddress;

    /**
     * @var AddressInterface
     *
     * The address clone
     */
    private $clonedAddress;

    /**
     * @var CustomerInterface
     */
    private $customer;

    /**
     * Builds a new address on clone event
     *
     * @param CustomerInterface $customer
     * @param AddressInterface  $originalAddress The original address
     * @param AddressInterface  $clonedAddress   The new address clone
     */
    public function __construct(
        CustomerInterface $customer,
        AddressInterface $originalAddress,
        AddressInterface $clonedAddress
    ) {
        $this->originalAddress = $originalAddress;
        $this->clonedAddress = $clonedAddress;
        $this->customer = $customer;
    }

    /**
     * Get the original address
     *
     * @return AddressInterface
     */
    public function getOriginalAddress()
    {
        return $this->originalAddress;
    }

    /**
     * Get the cloned address
     *
     * @return AddressInterface
     */
    public function getClonedAddress()
    {
        return $this->clonedAddress;
    }

    /**
     * Gets the customer.
     *
     * @return CustomerInterface
     */
    public function getCustomer()
    {
        return $this->customer;
    }
}
