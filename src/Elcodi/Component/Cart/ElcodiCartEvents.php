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

namespace Elcodi\Component\Cart;

/**
 * Core Events related with all core entities
 */
final class ElcodiCartEvents
{
    /**
     * This event is dispatched when a new cart is created
     *
     * event.name : cart.oncreate
     * event.class : CartOnCreateEvent
     */
    const CART_ONCREATE = 'cart.oncreate';

    /**
     * This event is dispatched before the Cart is loaded
     *
     * event.name : cart.preload
     * event.class : CartPreLoadEvent
     */
    const CART_PRELOAD = 'cart.preload';

    /**
     * This event is dispatched when the Cart is loaded
     *
     * event.name : cart.onload
     * event.class : CartOnLoadEvent
     */
    const CART_ONLOAD = 'cart.onload';

    /**
     * This event is dispatched when the Cart emptied
     *
     * event.name : cart.onempty
     * event.class : CartOnEmptyEvent
     */
    const CART_ONEMPTY = 'cart.onempty';

    /**
     * This event is dispatched when an inconsistency is found in a cart
     *
     * event.name : cart.inconsistent
     * event.class : CartInconsistentEvent
     */
    const CART_INCONSISTENT = 'cart.inconsistent';

    /**
     * CartLine events
     */

    /**
     * This event is dispatched when a CartLine is being added into a Cart
     *
     * event.name : cart_line.onadd
     * event.class : CartLineOnAddEvent
     */
    const CARTLINE_ONADD = 'cart_line.onadd';

    /**
     * This event is dispatched when a CartLine edited
     *
     * event.name : cart_line.onedit
     * event.class : CartLineOnEditEvent
     */
    const CARTLINE_ONEDIT = 'cart_line.onedit';

    /**
     * This event is dispatched when a CartLine is removed from a Cart
     *
     * event.name : cart_line.onremove
     * event.class : CartLineOnRemoveEvent
     */
    const CARTLINE_ONREMOVE = 'cart_line.onremove';

    /**
     * Order events
     */

    /**
     * Order created events
     */

    /**
     * This event is dispatched before an order is created
     *
     * event.name : order.precreated
     * event.class : OrderPreCreatedEvent
     */
    const ORDER_PRECREATED = 'order.precreated';

    /**
     * This event is dispatched when an order has just been created
     *
     * event.name : order.oncreated
     * event.class : OrderOnCreatedEvent
     */
    const ORDER_ONCREATED = 'order.oncreated';

    /**
     * This event is dispatched when an order has been finally created
     *
     * event.name : order.postcreated
     * event.class : OrderPostCreatedEvent
     */
    const ORDER_POSTCREATED = 'order.postcreated';

    /**
     * Orderline created events
     */

    /**
     * This event is dispatched when an orderline is created
     *
     * event.name : order_line.oncreated
     * event.class : OrderLineOnCreatedEvent
     */
    const ORDERLINE_ONCREATED = 'order_line.oncreated';

    /**
     * Cart address events
     */

    /**
     * This event is dispatched when a cart billing address is changed
     *
     * event.name : cart_billing_address.onchange
     * event.class : CartAddressOnChangeEvent
     */
    const CARTBILLINGADDRESS_ONCHANGE = 'cart_billing_address.onchange';

    /**
     * This event is dispatched when a cart shipping address is changed
     *
     * event.name : cart_shipping_address.onchange
     * event.class : CartAddressOnChangeEvent
     */
    const CARTDELIVERYADDRESS_ONCHANGE = 'cart_delivery_address.onchange';
}
