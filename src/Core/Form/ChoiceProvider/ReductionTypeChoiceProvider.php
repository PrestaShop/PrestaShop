<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

class ReductionTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * @return array<string, string>
     */
    public function getChoices(): array
    {
        return [
            $this->currencyDataProvider->getDefaultCurrencySymbol() => Reduction::TYPE_AMOUNT,
            '%' => Reduction::TYPE_PERCENTAGE,
        ];
    }
}
