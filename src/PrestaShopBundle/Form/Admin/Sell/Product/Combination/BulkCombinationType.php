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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * For combination update in bulk action
 */
class BulkCombinationType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('stock', CombinationStockType::class)
            ->add('price_impact', CombinationPriceImpactType::class)
            ->add('reference', TextType::class, [
                'disabling_toggle' => true,
                'required' => false,
                'label' => $this->trans('Reference', 'Admin.Global'),
                'label_help_box' => $this->trans('Your reference code for this product. Allowed special characters: .-_#.', 'Admin.Catalog.Help'),
            ])
        ;

        $this->modifyForm($builder);
    }

    /**
     * @param FormBuilderInterface $builder
     */
    private function modifyForm(FormBuilderInterface $builder): void
    {
        //@todo: adding disabling toggle part should be made reusable and moved to eventSubscribers.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (PreSetDataEvent $event) {
            $form = $event->getForm();

            $stockType = $form->get('stock');
            $stockQuantitiesType = $stockType->get('quantities');
            $stockOptionsType = $stockType->get('options');
            $priceImpactType = $form->get('price_impact');

            $stockQuantitiesType->remove('stock_movements');
            $stockOptionsType->remove('stock_location');
            $priceImpactType->remove('unit_price');

            $this->addDisablingToggle([
                $stockQuantitiesType,
                $stockOptionsType,
                $priceImpactType,
            ]);
        });
    }

    /**
     * @param FormInterface[] $forms
     */
    private function addDisablingToggle(array $forms): void
    {
        $formCloner = new FormCloner();
        /* @var FormInterface $childForm */
        foreach ($forms as $form) {
            foreach ($form->all() as $childForm) {
                $newForm = $formCloner->cloneForm(
                    $childForm,
                    array_merge($childForm->getConfig()->getOptions(), ['disabling_toggle' => true])
                );

                $form->add($newForm);
            }
        }
    }
}
