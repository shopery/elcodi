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

namespace Elcodi\Component\User\Repository;

use Doctrine\ORM\EntityRepository;

use Elcodi\Component\User\Entity\Interfaces\AbstractUserInterface;
use Elcodi\Component\User\Entity\Interfaces\CustomerInterface;
use Elcodi\Component\User\Repository\Interfaces\UserEmaileableInterface;
use Elcodi\Component\User\Entity\Customer;
use Elcodi\Component\Geo\Entity\Address;

/**
 * Class CustomerRepository
 */
class CustomerRepository extends EntityRepository implements UserEmaileableInterface
{
    /**
     * Find one Entity given an email
     *
     * @param string $email Email
     *
     * @return AbstractUserInterface|null User found
     */
    public function findOneByEmail($email)
    {
        $user = $this
            ->findOneBy([
                'email' => $email,
            ]);

        return ($user instanceof AbstractUserInterface)
            ? $user
            : null;
    }

    /**
     * Find a user address by it's id
     *
     * @param integer $customerId The customer Id
     * @param integer $addressId  The address Id
     *
     * @return boolean
     */
    public function findAddress($customerId, $addressId)
    {
        $customerClass = Customer::class;
        $addressClass = Address::class;

        $dql = <<<DQL
SELECT
    a
FROM
    {$customerClass} c
INNER JOIN
    {$addressClass} a
WHERE
    a.id = :addressId
    AND c.id = :customerId
DQL;


        $result = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('customerId', $customerId)
            ->setParameter('addressId', $addressId)
            ->execute();


        return !empty($result)
            ? reset($result)
            : false;
    }
}
