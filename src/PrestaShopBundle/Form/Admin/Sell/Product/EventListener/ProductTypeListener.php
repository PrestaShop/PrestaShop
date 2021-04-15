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

namespace PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * This listener dynamically updates the form depending on the product type, like
 * is it virtual, a pack, does it have combinations, ...
 */
class ProductTypeListener implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'adaptProductForm',
            FormEvents::PRE_SUBMIT => 'adaptProductForm',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function adaptProductForm(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (ProductType::TYPE_COMBINATIONS === $data['basic']['type']) {
            $form->remove('suppliers');
            $form->remove('stock');
            if ($form->has('shortcuts')) {
                $shortcutsForm = $form->get('shortcuts');
                $shortcutsForm->remove('stock');
            }
        }
        if (ProductType::TYPE_VIRTUAL !== $data['basic']['type']) {
            $form->remove('virtual_product_file');
        }
    }
}
