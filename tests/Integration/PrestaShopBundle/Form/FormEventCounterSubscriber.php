<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * This subscriber is used for test purposes, it subscribes to all form events and
 * count the number of times they are called.
 */
class FormEventCounterSubscriber implements EventSubscriberInterface
{
    private $eventCalls = [];

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => ['preSubmit', -1024],
            FormEvents::POST_SUBMIT => ['postSubmit', -1024],
            FormEvents::PRE_SET_DATA => ['preSetData', -1024],
            FormEvents::POST_SET_DATA => ['postSetData', -1024],
            FormEvents::SUBMIT => ['submit', -1024],
        ];
    }

    public function preSubmit(FormEvent $event): void
    {
        $this->incrementEvent(FormEvents::PRE_SUBMIT);
    }

    public function postSubmit(FormEvent $event): void
    {
        $this->incrementEvent(FormEvents::POST_SUBMIT);
    }

    public function preSetData(FormEvent $event): void
    {
        $this->incrementEvent(FormEvents::PRE_SET_DATA);
    }

    public function postSetData(FormEvent $event): void
    {
        $this->incrementEvent(FormEvents::POST_SET_DATA);
    }

    public function submit(FormEvent $event): void
    {
        $this->incrementEvent(FormEvents::SUBMIT);
    }

    /**
     * @param string $eventName
     */
    private function incrementEvent(string $eventName): void
    {
        if (!isset($this->eventCalls[$eventName])) {
            $this->eventCalls[$eventName] = 0;
        }

        $this->eventCalls[$eventName] = $this->eventCalls[$eventName] + 1;
    }
}
