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

namespace Elcodi\Component\User\Services;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Elcodi\Component\Geo\Entity\Interfaces\AddressInterface;
use Elcodi\Component\Geo\Services\AddressManager;
use Elcodi\Component\User\Entity\Interfaces\AbstractUserInterface;
use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;
use Elcodi\Component\User\EventDispatcher\CustomerEventDispatcher;
use Elcodi\Component\User\EventDispatcher\Interfaces\UserEventDispatcherInterface;
use Elcodi\Component\User\Services\Abstracts\AbstractUserManager;

/**
 * Manager for Customer entities
 */
class CustomerManager extends AbstractUserManager
{
    /**
     * @var EntityManager
     */
    private $addressObjectManager;

    /**
     * @var CustomerEventDispatcher
     */
    private $customerEventDispatcher;

    /**
     * @var AddressManager
     */
    private $addressManager;

    /**
     * @param UserEventDispatcherInterface $userEventDispatcher
     * @param EntityManager                $addressObjectManager
     * @param CustomerEventDispatcher      $customerEventDispatcher
     * @param AddressManager               $addressManager
     */
    public function __construct(
        UserEventDispatcherInterface $userEventDispatcher,
        EntityManager $addressObjectManager,
        CustomerEventDispatcher $customerEventDispatcher,
        AddressManager $addressManager
    ) {
        $this->addressObjectManager = $addressObjectManager;
        $this->customerEventDispatcher = $customerEventDispatcher;
        $this->addressManager = $addressManager;
        parent::__construct($userEventDispatcher);
    }

    /**
     * Register new User into the web.
     * Creates new token given a user, with related Role set.
     *
     * @param CustomerInterface AbstractUserInterface User to register
     *
     * @return $this Self object
     */
    public function register(AbstractUserInterface $user)
    {
        parent::register($user);

        /**
         * @var CustomerInterface $user
         */
        $this
            ->userEventDispatcher
            ->dispatchOnCustomerRegisteredEvent($user);

        return $this;
    }

    /**
     * Saves the customer address.
     *
     * @param CustomerInterface $customer
     * @param AddressInterface  $address
     */
    public function saveCustomerAddress(
        CustomerInterface $customer,
        AddressInterface $address
    ) {
        $customer->removeAddress($address);
        $savedAddress = $this->addressManager->saveAddress($address);
        $customer->addAddress($savedAddress);

        if (!$savedAddress->equals($address)) {
            $this->customerEventDispatcher->dispatchCustomerAddressOnChangeEvent(
                $customer,
                $address,
                $savedAddress
            );
        }

        $this->saveCustomer($customer);
    }

    /**
     * Saves the customer
     *
     * @param CustomerInterface $customer
     */
    public function saveCustomer(CustomerInterface $customer)
    {
        $this
            ->addressObjectManager
            ->persist($customer);

        $this
            ->addressObjectManager
            ->flush($customer);
    }
}
