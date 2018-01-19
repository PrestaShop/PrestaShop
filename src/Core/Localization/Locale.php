<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Localization;

use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use PrestaShop\PrestaShop\Core\Localization\Number\Formatter as NumberFormatter;
use PrestaShop\PrestaShop\Core\Localization\Number\FormatterFactory;
use PrestaShop\PrestaShop\Core\Localization\Specification\Number as NumberSpecification;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberCollection as NumberSpecificationCollection;
use PrestaShop\PrestaShop\Core\Localization\Specification\Price as PriceSpecification;

class Locale implements LocaleInterface
{
    /**
     * Number formatter.
     * Used to format raw numbers in this locale context
     *
     * @var NumberFormatter
     */
    protected $numberFormatter;

    /**
     * All Price formatters, by currency.
     * Used to format prices in this locale context
     *
     * @var NumberFormatter[]
     */
    protected $priceFormatters;

    /**
     * Locale constructor.
     *
     * @param int $roundingMode
     *  Rounding mode that must be used by formatter
     *  Cf. PrestaShop\Decimal\Operation\Rounding::ROUND_* values
     *
     * @param string $numberingSystem
     *  Numbering system to use when formatting the number
     *
     * @see http://cldr.unicode.org/translation/numbering-systems
     *
     * @param NumberSpecification $numberSpecification
     *  Number specification used when formatting a number
     *
     * @param NumberSpecificationCollection $priceSpecifications
     *  Collection of Price specifications (one per installed currency)
     *
     * @param FormatterFactory $formatterFactory
     *  Used to build all the needed formatters
     *
     * @throws LocalizationException
     */
    public function __construct(
        $roundingMode,
        $numberingSystem,
        NumberSpecification $numberSpecification,
        NumberSpecificationCollection $priceSpecifications,
        FormatterFactory $formatterFactory
    ) {
        $this->numberFormatter = $formatterFactory->buildFormatter(
            $numberSpecification,
            $roundingMode,
            $numberingSystem
        );

        /** @var PriceSpecification $priceSpecification */
        foreach ($priceSpecifications as $priceSpecification) {
            if (!$priceSpecification instanceof PriceSpecification) {
                throw new LocalizationException(
                    '$priceSpecifications items must be instances of Price specification. '
                    . get_class($priceSpecification) . ' given.'
                );
            }

            $this->priceFormatters[$priceSpecification->getCurrencyCode()] = $formatterFactory->buildFormatter(
                $priceSpecification,
                $roundingMode,
                $numberingSystem
            );
        }
    }
}
