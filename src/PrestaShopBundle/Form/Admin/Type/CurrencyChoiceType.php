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

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencyChoiceType extends AbstractType
{
    /**
     * @var CurrencyByIdChoiceProvider
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        CurrencyDataProviderInterface $currencyDataProvider,
        CurrencyByIdChoiceProvider $currencyByIdChoiceProvider
    ) {
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addNormalizer('choices', function (Options $options) {
            $currencies = $this->currencyByIdChoiceProvider->getChoices();

            if ($options['add_all_currencies_option']) {
                return array_merge(
                    ['All currencies' => 0],
                    $currencies
                );
            }

            return $currencies;
        });

        $resolver->addNormalizer('attr', function (Options $options, array $attr) {
            $attr['data-default-currency-symbol'] = $this->currencyDataProvider->getDefaultCurrencySymbol();
            $attr['data-minimumResultsForSearch'] = '7';
            $attr['data-toggle'] = 'select2';

            return $attr;
        });

        $resolver->setDefaults([
            'required' => false,
            'add_all_currencies_option' => false,
            'choice_translation_domain' => false,
            'choices' => [],
            'choice_attr' => $this->currencyByIdChoiceProvider->getChoicesAttributes(),
            'label' => 'Currency',
            'placeholder' => false,
        ]);
    }
}
