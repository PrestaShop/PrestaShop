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

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\SuppliersType;
use PrestaShopBundle\Form\Admin\Sell\Product\Specification\ReferencesType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form to edit Combination details.
 */
class CombinationFormType extends TranslatorAwareType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $imagesChoiceProvider;

    /**
     * @var EventSubscriberInterface
     */
    private $combinationListener;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurableFormChoiceProviderInterface $imagesChoiceProvider
     * @param EventSubscriberInterface $combinationListener
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ConfigurableFormChoiceProviderInterface $imagesChoiceProvider,
        EventSubscriberInterface $combinationListener
    ) {
        parent::__construct($translator, $locales);
        $this->imagesChoiceProvider = $imagesChoiceProvider;
        $this->combinationListener = $combinationListener;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('stock', CombinationStockType::class)
            ->add('price_impact', CombinationPriceImpactType::class)
            ->add('references', ReferencesType::class)
            ->add('suppliers', SuppliersType::class, [
                'alert_message' => $this->trans('This interface allows you to specify the suppliers of the current combination.', 'Admin.Catalog.Help'),
            ])
            ->add('images', ChoiceType::class, [
                'label' => $this->trans('Images', 'Admin.Global'),
                'label_tag_name' => 'h2',
                'choices' => $this->imagesChoiceProvider->getChoices(['product_id' => $options['product_id']]),
                'choice_attr' => function ($choice, $key) {
                    return ['data-image-url' => $key];
                },
                'multiple' => true,
                'expanded' => true,
            ])
        ;

        /*
         * This listener adapts the content of the form based on the data, it can remove add or transforms some
         * of the internal fields @see CombinationListener
         */
        $builder->addEventSubscriber($this->combinationListener);
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['product_id'])
            ->setAllowedTypes('product_id', ['int'])
            ->setDefaults([
                'required' => false,
                'label' => false,
            ])
        ;
    }
}
