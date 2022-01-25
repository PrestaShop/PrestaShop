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

use PrestaShopBundle\Form\Admin\Extension\DisablingToggleExtension;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DisablingToggleListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'addDisablingToggle',
            FormEvents::PRE_SUBMIT => 'addDisablingToggle',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function addDisablingToggle(FormEvent $event): void
    {
        $form = $event->getForm();
        $parent = $form->getParent();
        $fieldName = DisablingToggleExtension::FIELD_PREFIX . $form->getName();
        if ($parent->has($fieldName)) {
            return;
        }
        $parent->add($fieldName, SwitchType::class, [
            'label' => false,
            'attr' => [
                'container_class' => 'disabling-toggle',
                'data-value-type' => 'boolean',
            ],
        ]);

        $shouldBeDisabled = !$parent->get($fieldName)->getData();

        $formCloner = new FormCloner();
        $newOptions = [
            'disabled' => $shouldBeDisabled,
            'attr' => [
                'disabled' => $shouldBeDisabled,
                'class' => $shouldBeDisabled ? 'disabled' : '',
            ],
        ];

        foreach ($form->all() as $childForm) {
            $config = $childForm->getConfig();
            if ($shouldBeDisabled === $config->getOption('disabled', false)) {
                continue;
            }
            $newChildForm = $formCloner->cloneForm($childForm, array_merge($childForm->getConfig()->getOptions(), $newOptions));
            $form->add($newChildForm);
        }

        //@todo; need configurable (e.g. it should be possible to change if input is disabled when checkbox is checked or when unchecked
        $newForm = $formCloner->cloneForm($form, array_merge($form->getConfig()->getOptions(), $newOptions));
        $parent->add($newForm);
    }
}
