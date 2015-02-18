<?php

/*
 * This file is part of the Elcodi package.
 *
 * Copyright (c) 2014 Elcodi.com
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

namespace Elcodi\Component\CartCoupon\EventListener;

use Elcodi\Component\CartCoupon\Event\CartCouponOnCheckEvent;
use Elcodi\Component\CartCoupon\Exception\CouponRulesNotValidateException;
use Elcodi\Component\CartCoupon\Services\CartCouponRuleManager;

/**
 * Class CheckRulesListener
 */
class CheckRulesListener
{
    /**
     * @var CartCouponRuleManager
     *
     * CartCoupon Rule managers
     */
    protected $cartCouponRuleManager;

    /**
     * Construct method
     *
     * @param CartCouponRuleManager     $cartCouponRuleManager     Manager for cart coupon rules
     */
    public function __construct(CartCouponRuleManager $cartCouponRuleManager)
    {
        $this->cartCouponRuleManager = $cartCouponRuleManager;
    }

    /**
     * Check for the rules required by the coupon
     *
     * @param CartCouponOnCheckEvent $event Event
     *
     * @throws CouponRulesNotValidateException
     */
    public function checkCoupon(CartCouponOnCheckEvent $event)
    {
        $isValid = $this
            ->cartCouponRuleManager
            ->checkCouponValidity(
                $event->getCart(),
                $event->getCoupon()
            );

        if (!$isValid) {
            throw new CouponRulesNotValidateException();
        }
    }
}
