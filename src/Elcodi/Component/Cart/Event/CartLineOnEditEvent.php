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

namespace Elcodi\Component\Cart\Event;

use Elcodi\Component\Cart\Entity\Interfaces\CartInterface;
use Elcodi\Component\Cart\Entity\Interfaces\CartLineInterface;
use Elcodi\Component\Cart\Event\Abstracts\AbstractCartLineEvent;

/**
 * Class CartLineOnEditEvent
 */
class CartLineOnEditEvent extends AbstractCartLineEvent
{
    /**
     * @var int
     */
    private $previousStock;

    /**
     * Construct method
     *
     * @param CartInterface     $cart     Cart
     * @param CartLineInterface $cartLine Cart line
     * @param integer           $previousStock
     */
    public function __construct(
        CartInterface $cart,
        CartLineInterface $cartLine,
        $previousStock
    ) {
        parent::__construct(
            $cart,
            $cartLine
        );

        $this->previousStock = $previousStock;
    }

    /**
     * Get the previous stock.
     *
     * @return int
     */
    public function getPreviousStock()
    {
        return $this->previousStock;
    }
}
