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

namespace Elcodi\Component\Geo\Validator\Constraints;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use Elcodi\Component\Geo\Services\Interfaces\LocationProviderInterface;
use Elcodi\Component\Geo\ValueObject\LocationData;

/**
 * Class CityExistsValidator
 */
class CityExistsValidator extends ConstraintValidator
{
    /**
     * @var LocationProviderInterface
     *
     * A location provider
     */
    private $locationProvider;

    /**
     * Builds a new class.
     *
     * @param LocationProviderInterface $locationProvider
     */
    public function __construct(LocationProviderInterface $locationProvider)
    {
        $this->locationProvider = $locationProvider;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        $location = $this->findLocation($value);
        if (!$this->validateLocation($location)) {
            $this
                ->context
                ->addViolation('Select a city');
        }
    }

    /**
     * Find LocationData from an id
     *
     * @param $locationId
     *
     * @return LocationData|null
     */
    private function findLocation($locationId)
    {
        /**
         * @var LocationData $location
         */
        try {
            return $this
                ->locationProvider
                ->getLocation($locationId);

        } catch (EntityNotFoundException $e) {
            return null;
        }
    }

    /**
     * Check for LocationData validation
     *
     * @param LocationData|null $location
     *
     * @return bool
     */
    private function validateLocation($location)
    {
        if (!$location instanceof LocationData) {
            return false;
        }

        if ('city' === $location->getType()) {
            return true;
        }

        $children = $this
            ->locationProvider
            ->getChildren($location->getId());

        return count($children) === 0;
    }
}
