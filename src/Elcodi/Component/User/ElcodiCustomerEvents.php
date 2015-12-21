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

namespace Elcodi\Component\User;

/**
 * ElcodiCustomerEvents
 */
final class ElcodiCustomerEvents
{
    /**
     * This event is launched when an address has changed.
     *
     * event.name : address.onchange
     * event.class : Address
     */
    const ADDRESS_ONCHANGE = 'address.onchange';
}
