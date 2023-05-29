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

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CurrencyChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicableGroupsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    protected $countryByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    protected $groupByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    protected $shopByIdChoiceProvider;

    /**
     * @var bool
     */
    protected $isMultiShopEnabled;

    /**
     * @var int
     */
    protected $contextShopId;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $countryByIdChoiceProvider,
        FormChoiceProviderInterface $groupByIdChoiceProvider,
        FormChoiceProviderInterface $shopByIdChoiceProvider,
        bool $isMultiShopEnabled,
        int $contextShopId
    ) {
        parent::__construct($translator, $locales);
        $this->countryByIdChoiceProvider = $countryByIdChoiceProvider;
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
        $this->shopByIdChoiceProvider = $shopByIdChoiceProvider;
        $this->isMultiShopEnabled = $isMultiShopEnabled;
        $this->contextShopId = $contextShopId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countries = array_merge([
            $this->trans('All countries', 'Admin.Global') => 0,
        ], $this->countryByIdChoiceProvider->getChoices());
        $groups = array_merge([
            $this->trans('All groups', 'Admin.Global') => 0,
        ], $this->groupByIdChoiceProvider->getChoices());

        $builder
            ->add('currency_id', CurrencyChoiceType::class, [
                'add_all_currencies_option' => true,
            ])
            ->add('country_id', ChoiceType::class, [
                'label' => false,
                'placeholder' => false,
                'choices' => $countries,
                'required' => false,
            ])
            ->add('group_id', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'choices' => $groups,
            ])
        ;

        if ($this->isMultiShopEnabled) {
            $builder->add('shop_id', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'choices' => $this->buildShopChoices(),
            ]);
        }
    }

    /**
     * @return array<string, int>
     */
    private function buildShopChoices(): array
    {
        $choices = [
            $this->trans('All stores', 'Admin.Global') => 0,
        ];

        $allShops = $this->shopByIdChoiceProvider->getChoices();
        foreach ($allShops as $name => $shopId) {
            if ($shopId === $this->contextShopId) {
                $choices[$name] = $shopId;
                break;
            }
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'columns_number' => 4,
        ]);
    }
}
