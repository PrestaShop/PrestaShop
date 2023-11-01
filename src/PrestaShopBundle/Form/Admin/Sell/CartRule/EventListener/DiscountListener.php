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

namespace PrestaShopBundle\Form\Admin\Sell\CartRule\EventListener;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\DiscountApplicationChoiceProvider;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DiscountListener implements EventSubscriberInterface
{
    /**
     * @var DiscountApplicationChoiceProvider
     */
    private $discountApplicationChoiceProvider;

    /**
     * @var FormCloner
     */
    private $formCloner;

    public function __construct(
        DiscountApplicationChoiceProvider $discountApplicationChoiceProvider,
        FormCloner $formCloner
    ) {
        $this->discountApplicationChoiceProvider = $discountApplicationChoiceProvider;
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptDiscountChoices',
            FormEvents::PRE_SUBMIT => 'adaptDiscountChoices',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function adaptDiscountChoices(FormEvent $event): void
    {
        $data = $event->getData();
        if (!isset($data['reduction']['type'])) {
            return;
        }

        $form = $event->getForm();
        $discountApplicationField = $form->get('discount_application');
        // adjust discount application choices depending on reduction type data
        $newDiscountApplicationField = $this->formCloner->cloneForm($discountApplicationField, [
            'choices' => $this->discountApplicationChoiceProvider->getChoices([
                'reduction_type' => $data['reduction']['type'],
            ]),
        ]);

        // replace previous form with the new one, containing updated options
        $form->add($newDiscountApplicationField);
    }
}
