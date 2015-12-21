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

namespace Elcodi\Component\Geo\Services;


use Doctrine\Common\Persistence\ObjectManager;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;
use Elcodi\Component\Geo\EventDispatcher\AddressEventDispatcher;
use Elcodi\Component\Core\Services\ObjectDirector;
use Symfony\Component\Validator\Constraints\All;

/**
 * Class AddressManager
 */
class AddressManager
{
    /**
     * @var ObjectDirector
     *
     * Address object manager
     */
    private $addressDirector;

    /**
     * @var AddressEventDispatcher
     *
     * Address event dispatcher
     */
    private $addressEventDispatcher;

    /**
     * @var ObjectManager
     */
    private $addressObjectManager;

    /**
     * Builds an address manager
     *
     * @param ObjectDirector         $addressDirector
     * @param AddressEventDispatcher $addressEventDispatcher An address event
     *                                                       dispatcher
     */
    public function __construct(
        ObjectDirector $addressDirector,
        ObjectManager $addressObjectManager,
        AddressEventDispatcher $addressEventDispatcher
    ) {
        $this->addressDirector = $addressDirector;
        $this->addressObjectManager = $addressObjectManager;
        $this->addressEventDispatcher = $addressEventDispatcher;
    }

    /**
     * Saves and returns the saved address.
     * (Could be different from the received one)
     *
     * @param AddressInterface $address
     *
     * @return AddressInterface
     */
    public function saveAddress(AddressInterface $address)
    {
        if ($this->addressHasChanged($address)) {
            $clonedAddress = $this->cloneAddress($address);
            $this->refreshAddress($address);
            $this->save($clonedAddress);
            return $clonedAddress;
        }

        $this->save($address);
        return $address;
    }

    /**
     * Checks if an address has changed.
     *
     * @param AddressInterface $address
     *
     * @return mixed
     */
    public function addressHasChanged(AddressInterface $address)
    {
        $clonedAddress = clone $address;
        $this->refreshAddress($address);

        $isEqual = $address->equals($clonedAddress);

        $this->addressObjectManager->merge($clonedAddress);

        return !$isEqual;
    }

    /**
     * Saves the given address.
     *
     * @param AddressInterface $address
     */
    private function save(AddressInterface $address)
    {
        $this->addressObjectManager->persist($address);
        $this->addressObjectManager->flush($address);
    }

    /**
     * Clones the address.
     *
     * @param AddressInterface $address
     *
     * @return AddressInterface
     */
    private function cloneAddress(AddressInterface $address)
    {
        $clonedAddress = clone $address;
        $this->addressObjectManager->detach($clonedAddress);
        $clonedAddress->setId(null);
        return $clonedAddress;
    }

    /**
     * Refreshes the address from the database.
     *
     * @param AddressInterface $address
     */
    private function refreshAddress(AddressInterface $address)
    {
        $this->addressObjectManager->refresh($address);
    }
}
