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

namespace Elcodi\Component\EntityTranslator\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use Elcodi\Component\EntityTranslator\Form\Type\TranslatableFieldType;
use Elcodi\Component\EntityTranslator\Services\Interfaces\EntityTranslationProviderInterface;

/**
 * Class EntityTranslatorFormEventListener
 */
class EntityTranslatorFormEventListener implements EventSubscriberInterface
{
    /**
     * @var EntityTranslationProviderInterface
     *
     * Entity Translation provider
     */
    private $provider;

    /**
     * @var array
     *
     * Translation configuration
     */
    private $configuration;

    /**
     * @var array
     *
     * Locales
     */
    private $locales;

    /**
     * @var string
     *
     * Master locale
     */
    private $masterLocale;

    /**
     * @var boolean
     *
     * Fallback is enabled.
     *
     * If a field is required and the fallback flag is enabled, all translations
     * will not be required anymore, but just the translation with same language
     * than master
     */
    private $fallback;

    /**
     * @var array
     *
     * Submitted data in plain mode
     */
    private $submittedData = [];

    /**
     * @var array
     *
     * Local and temporary backup of translations
     */
    private $collectedTranslations = [];

    /**
     * Construct method
     *
     * @param EntityTranslationProviderInterface $provider      EntityTranslation provider
     * @param array                              $configuration EntityTranslation configuration
     * @param array                              $locales       Locales
     * @param string                             $masterLocale  Master locale
     * @param boolean                            $fallback      Fallback
     */
    public function __construct(
        EntityTranslationProviderInterface $provider,
        array $configuration,
        array $locales,
        $masterLocale,
        $fallback
    ) {
        $this->provider = $provider;
        $this->configuration = $configuration;
        $this->locales = $locales;
        $this->masterLocale = $masterLocale;
        $this->fallback = $fallback;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     *
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT   => 'preSubmit',
            FormEvents::POST_SUBMIT  => 'postSubmit',
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    /**
     * Pre set data
     *
     * @param FormEvent $event Event
     */
    public function preSetData(FormEvent $event)
    {
        $entity = $event->getData();
        $entityConfiguration = $this->getTranslatableEntityConfiguration($entity);
        if (null === $entityConfiguration) {
            return;
        }

        $form = $event->getForm();
        foreach ($entityConfiguration['fields'] as $fieldName => $fieldConfiguration) {
            if (!$form->has($fieldName)) {
                continue;
            }

            $formConfig = $form
                ->get($fieldName)
                ->getConfig();

            $type = new TranslatableFieldType(
                $this->provider,
                $formConfig,
                $entity,
                $fieldName,
                $entityConfiguration,
                $fieldConfiguration,
                $this->locales,
                $this->masterLocale,
                $this->fallback
            );

            $form
                ->remove($fieldName)
                ->add($fieldName, $type, [
                    'mapped' => false,
                ]);
        }
    }

    /**
     * Pre submit
     *
     * @param FormEvent $event Event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $formHash = $this->getFormHash($form);
        $this->submittedData[$formHash] = $event->getData();
    }

    /**
     * Post submit
     *
     * @param FormEvent $event Event
     */
    public function postSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        if (!$form->isValid()) {
            return;
        }

        $entity = $event->getData();
        $configuration = $this->getTranslatableEntityConfiguration($entity);
        if (null === $configuration) {
            return;
        }

        $formHash = $this->getFormHash($form);
        $this->collectTranslations($formHash, $configuration, $entity);
    }

    /**
     * Method executed at the end of the response. Save all entity translations
     * previously generated and waiting for being flushed into database and
     * cache layer
     */
    public function saveEntityTranslations()
    {
        if (empty($this->collectedTranslations)) {
            return;
        }

        foreach ($this->collectedTranslations as $entities) {
            foreach ($entities as $data) {
                $this->setTranslations(
                    $data['alias'],
                    $data['object'],
                    $data['idGetter'],
                    $data['fields']
                );
            }
        }

        $this
            ->provider
            ->flushTranslations();
    }

    /**
     * Get form unique hash
     *
     * @param FormInterface $form Form
     *
     * @return string Form hash
     */
    private function getFormHash(FormInterface $form)
    {
        return spl_object_hash($form);
    }

    /**
     * Get configuration for a translatable entity, or null if the entity is not
     * translatable
     *
     * @param mixed $entity Entity
     *
     * @return array|null Configuration
     */
    private function getTranslatableEntityConfiguration($entity)
    {
        $classes = $this->getNamespacesFromClass(get_class($entity));

        foreach ($classes as $class) {
            if (array_key_exists($class, $this->configuration)) {
                return $this->configuration[$class];
            }
        }

        return null;
    }

    /**
     * Get all possible classes given an object
     *
     * @param string $namespace Namespace
     *
     * @return string[] Set of classes and interfaces
     */
    private function getNamespacesFromClass($namespace)
    {
        return array_merge(
            [ $namespace ],
            class_parents($namespace),
            class_implements($namespace)
        );
    }

    /**
     * @param string $formHash
     * @param array  $configuration
     * @param mixed  $entity
     */
    private function collectTranslations($formHash, array $configuration, $entity)
    {
        $fields = [];
        $data = $this->submittedData[$formHash];
        foreach ($configuration['fields'] as $fieldName => $fieldConfiguration) {
            if (!isset($data[$fieldName])) {
                continue;
            }

            $field = $data[$fieldName];
            foreach ($this->locales as $locale) {
                $fields[$fieldName][$locale] = $field["{$locale}_{$fieldName}"];
            }

            if ($this->masterLocale && isset($field["{$this->masterLocale}_{$fieldName}"])) {
                $setter = $fieldConfiguration['setter'];
                $entity->{$setter}($field["{$this->masterLocale}_{$fieldName}"]);
            }
        }

        $this->collectedTranslations[$formHash][] = [
            'object' => $entity,
            'idGetter' => $configuration['idGetter'],
            'alias' => $configuration['alias'],
            'fields' => $fields,
        ];
    }

    /**
     * Set translations for an entity
     *
     * @param string $alias
     * @param mixed  $entity
     * @param string $idGetter
     * @param array  $fields
     */
    private function setTranslations($alias, $entity, $idGetter, $fields)
    {
        $id = $entity->{$idGetter}();
        foreach ($fields as $fieldName => $locales) {
            foreach ($locales as $locale => $translation) {
                $this
                    ->provider
                    ->setTranslation($alias, $id, $fieldName, $translation, $locale);
            }
        }
    }
}
