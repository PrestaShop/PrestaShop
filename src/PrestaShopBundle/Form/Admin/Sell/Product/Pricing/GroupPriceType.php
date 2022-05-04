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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class GroupPriceType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface|FormChoiceAttributeProviderInterface
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $countryByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $groupByIdChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $currencyByIdChoiceProvider,
        FormChoiceProviderInterface $countryByIdChoiceProvider,
        FormChoiceProviderInterface $groupByIdChoiceProvider
    )
    {
        parent::__construct($translator, $locales);
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->countryByIdChoiceProvider = $countryByIdChoiceProvider;
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currency_id', ChoiceType::class, [
                'label' => $this->trans('Currency', 'Admin.Global'),
                'placeholder' => $this->trans('All currencies', 'Admin.Global'),
                'choices' => $this->currencyByIdChoiceProvider->getChoices(),
                'choice_attr' => $this->currencyByIdChoiceProvider->getChoicesAttributes(),
                'required' => false,
            ])
            ->add('country_id', ChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'placeholder' => $this->trans('All countries', 'Admin.Global'),
                'choices' => $this->countryByIdChoiceProvider->getChoices(),
                'required' => false,
            ])
            ->add('group_id', ChoiceType::class, [
                'label' => $this->trans('Group', 'Admin.Global'),
                'required' => false,
                'placeholder' => $this->trans('All groups', 'Admin.Global'),
                'choices' => $this->groupByIdChoiceProvider->getChoices(),
            ])
        ;

        if ($options['is_multishop_enabled']) {
            $builder->add('shop_id', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->shopByIdChoiceProvider->getChoices(),
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'columns_number' => 4,
            'is_multishop_enabled' => false,
        ]);
    }

}
