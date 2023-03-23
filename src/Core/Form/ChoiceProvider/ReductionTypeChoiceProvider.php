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

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReductionTypeChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        TranslatorInterface $translator,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        $this->translator = $translator;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * @return array<string, string>
     */
    public function getChoices(array $options): array
    {
        $options = $this->configureOptions($options);

        if ($options['symbol_as_label']) {
            return [
                $this->currencyDataProvider->getDefaultCurrency()->symbol => Reduction::TYPE_AMOUNT,
                '%' => Reduction::TYPE_PERCENTAGE,
            ];
        }

        return [
            $this->translator->trans('Percent (%)', [], 'Admin.Global') => Reduction::TYPE_PERCENTAGE,
            $this->translator->trans('Amount', [], 'Admin.Global') => Reduction::TYPE_AMOUNT,
        ];
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function configureOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(['symbol_as_label'])
            ->setAllowedTypes('symbol_as_label', 'bool')
        ;

        return $resolver->resolve($options);
    }
}
