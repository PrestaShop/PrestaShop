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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer;

use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\InitialPrice;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;

/**
 * The purpose of this transformer is to display the disabling value of fixed price -1 into an empty string, but since
 * the field is a MoneyType it already has a money type transformer which causes some bugs because the value is already
 * converted for current locale and the format is not compatible with DecimalNumber which is used by InitialPrice.
 */
class SpecificPriceFixedPriceTransformer implements DataTransformerInterface
{
    /**
     * @var NumberToLocalizedStringTransformer
     */
    private $numberTransformer;

    public function __construct(int $scale = null, ?bool $grouping = false, ?int $roundingMode = NumberToLocalizedStringTransformer::ROUND_HALF_UP)
    {
        $this->numberTransformer = new NumberToLocalizedStringTransformer($scale, $grouping, $roundingMode);
    }

    public function transform($fixedPriceLocalizedValue)
    {
        $floatValue = $this->numberTransformer->reverseTransform((string) $fixedPriceLocalizedValue);
        if (InitialPrice::isInitialPriceValue((string) $floatValue)) {
            return '';
        }

        return $fixedPriceLocalizedValue;
    }

    public function reverseTransform($fixedPriceViewValue)
    {
        if ($fixedPriceViewValue === '') {
            return InitialPrice::INITIAL_PRICE_VALUE;
        }

        return $fixedPriceViewValue;
    }
}
