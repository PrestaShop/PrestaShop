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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type\EventListener;

use PrestaShopBundle\Form\Admin\Extension\DisablingSwitchExtension;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormEvents;

class AdaptDisableStateListener implements EventSubscriberInterface
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    public function __construct(FormCloner $formCloner)
    {
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'adaptDisableChildrenState',
        ];
    }

    /**
     * @param PreSubmitEvent $event
     */
    public function adaptDisableChildrenState(PreSubmitEvent $event): void
    {
        $form = $event->getForm();
        if ($form->count() === 0) {
            return;
        }

        $submittedData = $event->getData();
        foreach ($form->all() as $child) {
            if (!$child->getConfig()->getOption(DisablingSwitchExtension::SWITCH_OPTION) || !$child->getConfig()->hasOption(DisablingSwitchExtension::DISABLED_DATA_OPTION)) {
                continue;
            }

            $disablingFieldName = DisablingSwitchExtension::FIELD_PREFIX . $child->getName();
            if (!$form->has($disablingFieldName) || !isset($submittedData[$disablingFieldName])) {
                continue;
            }

            $shouldBeDisabled = $submittedData[$disablingFieldName] === '0';
            if ($shouldBeDisabled) {
                $disabledData = $child->getConfig()->getOption(DisablingSwitchExtension::DISABLED_DATA_OPTION);
                $eventData[$child->getName()] = $disabledData;
                $event->setData($eventData);
            }
        }
    }
}
