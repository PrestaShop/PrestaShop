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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Translation\TranslatorInterface;

class StockMovementType extends TranslatorAwareType
{
    /**
     * @var FormCloner
     */
    private $formCloner;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormCloner $formCloner
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormCloner $formCloner
    ) {
        parent::__construct($translator, $locales);
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_add', TextPreviewType::class, [
                'label' => $this->trans('Date & Time', 'Admin.Global'),
            ])
            ->add('employee', TextPreviewType::class, [
                'label' => $this->trans('Employee', 'Admin.Global'),
            ])
            ->add('delta_quantity', TextPreviewType::class, [
                'label' => $this->trans('Quantity', 'Admin.Global'),
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // Check that data exists, during prototype rendering it will be empty
            $previewClass = 'stock_movement_quantity';
            if (!empty($data['delta_quantity'])) {
                $previewClass .= $data['delta_quantity'] >= 0 ? ' increased_quantity' : ' decreased_quantity';
            }

            // Override delta_quantity field with updated options
            $form->add($this->formCloner->cloneForm($form->get('delta_quantity'), [
                'preview_class' => $previewClass,
            ]));
        });
    }
}
