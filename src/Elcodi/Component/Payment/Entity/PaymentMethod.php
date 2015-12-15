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

namespace Elcodi\Component\Payment\Entity;

/**
 * Class PaymentMethod
 */
class PaymentMethod
{
    /**
     * @var string
     *
     * Identifier
     */
    protected $id;

    /**
     * @var string
     *
     * Name
     */
    protected $name;

    /**
     * @var string
     *
     * Description
     */
    protected $description;

    /**
     * @var string
     *
     * Url
     */
    protected $url;

    /**
     * @var Array
     *
     * Payment types (credit cards, etc) allowed
     */
    protected $type;

    /**
     * @var Array
     *
     * Payment method company
     */
    protected $company;

    /**
     * @var string
     *
     * Image url
     */
    protected $imageUrl;

    /**
     * @var string
     *
     * Script
     */
    protected $script;

    /**
     * Contstruct
     *
     * @param string $id          Id
     * @param string $name        Name
     * @param string $description Description
     * @param string $url         Url
     * @param array  $type        Type of payment allowed
     * @param array  $company     Payment Method Company
     * @param string $imageUrl    Image url
     * @param string $script      Script
     */
    public function __construct(
        $id,
        $name,
        $description,
        $url,
        $imageUrl = '',
        $script = '',
        $type = [],
        $company = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->url = $url;
        $this->imageUrl = $imageUrl;
        $this->script = $script;
        $this->type = $type;
        $this->company = $company;
    }

    /**
     * Get id
     *
     * @return string Id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Name
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets field company
     *
     * @return Array
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Gets field type
     *
     * @return Array
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get Description
     *
     * @return string Description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get Url
     *
     * @return string Url
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get ImageUrl
     *
     * @return string ImageUrl
     */
    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    /**
     * Get Script
     *
     * @return string Script
     */
    public function getScript()
    {
        return $this->script;
    }
}
