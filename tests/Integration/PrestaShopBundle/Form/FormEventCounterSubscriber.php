<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
