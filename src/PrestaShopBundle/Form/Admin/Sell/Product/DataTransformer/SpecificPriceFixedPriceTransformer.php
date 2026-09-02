<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\DataTransformer;

use NumberFormatter;
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

    public function __construct(?int $scale = null, ?bool $grouping = false, ?int $roundingMode = NumberFormatter::ROUND_HALFUP)
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
