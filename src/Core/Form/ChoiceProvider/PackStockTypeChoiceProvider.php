<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class PackStockTypeChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ShopConfigurationInterface
     */
    private $shopConfiguration;

    /**
     * @param TranslatorInterface $translator
     * @param ShopConfigurationInterface $shopConfiguration
     */
    public function __construct(
        TranslatorInterface $translator,
        ShopConfigurationInterface $shopConfiguration
    ) {
        $this->translator = $translator;
        $this->shopConfiguration = $shopConfiguration;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices(): array
    {
        $choices = $this->getLabelValuePairs();

        $defaultLabel = sprintf(
            '%s (%s)',
            $this->translator->trans('Default', [], 'Admin.Global'),
            array_search((int) $this->shopConfiguration->get('PS_PACK_STOCK_TYPE'), $choices, true)
        );

        $choices[$defaultLabel] = PackStockType::STOCK_TYPE_DEFAULT;

        return $choices;
    }

    /**
     * @return array<string, int>
     */
    private function getLabelValuePairs(): array
    {
        return [
            $this->translator->trans('Use pack quantity', [], 'Admin.Catalog.Feature') => PackStockType::STOCK_TYPE_PACK_ONLY,
            $this->translator->trans('Use quantity of products in the pack', [], 'Admin.Catalog.Feature') => PackStockType::STOCK_TYPE_PRODUCTS_ONLY,
            $this->translator->trans('Use both, whatever is lower', [], 'Admin.Catalog.Feature') => PackStockType::STOCK_TYPE_BOTH,
        ];
    }
}
