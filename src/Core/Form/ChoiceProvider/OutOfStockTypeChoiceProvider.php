<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Provides form choices for "when product is out of stock" behavior
 */
final class OutOfStockTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    /**
     * @param TranslatorInterface $translator
     * @param ShopConfigurationInterface $configuration
     */
    public function __construct(
        TranslatorInterface $translator,
        ShopConfigurationInterface $configuration
    ) {
        $this->translator = $translator;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $allowOrdersLabel = $this->translator->trans('Allow orders', [], 'Admin.Catalog.Feature');
        $denyOrdersLabel = $this->translator->trans('Deny orders', [], 'Admin.Catalog.Feature');

        $defaultLabel = sprintf(
            '%s (%s)',
            $this->translator->trans('Use default behavior', [], 'Admin.Catalog.Feature'),
            $this->configuration->get('PS_ORDER_OUT_OF_STOCK') ? $allowOrdersLabel : $denyOrdersLabel
        );

        return [
            $denyOrdersLabel => OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE,
            $allowOrdersLabel => OutOfStockType::OUT_OF_STOCK_AVAILABLE,
            $defaultLabel => OutOfStockType::OUT_OF_STOCK_DEFAULT,
        ];
    }
}
