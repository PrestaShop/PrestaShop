<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductVisibility;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProductVisibilityChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices(): array
    {
        return [
            $this->translator->trans('Everywhere', [], 'Admin.Catalog.Feature') => ProductVisibility::VISIBLE_EVERYWHERE,
            $this->translator->trans('Catalog only', [], 'Admin.Catalog.Feature') => ProductVisibility::VISIBLE_IN_CATALOG,
            $this->translator->trans('Search only', [], 'Admin.Catalog.Feature') => ProductVisibility::VISIBLE_IN_SEARCH,
            $this->translator->trans('Nowhere', [], 'Admin.Catalog.Feature') => ProductVisibility::INVISIBLE,
        ];
    }

    public function getChoicesAttributes(): array
    {
        return [
            $this->translator->trans('Everywhere', [], 'Admin.Catalog.Feature') => [
                'data-description' => $this->translator->trans('Customers can access the product by browsing the catalog, using the search bar, or the link.', [], 'Admin.Catalog.Feature'),
            ],
            $this->translator->trans('Catalog only', [], 'Admin.Catalog.Feature') => [
                'data-description' => $this->translator->trans('Customers can access the product only by browsing the catalog. This is particularly useful to avoid displaying too many similar products in a search.', [], 'Admin.Catalog.Feature'),
            ],
            $this->translator->trans('Search only', [], 'Admin.Catalog.Feature') => [
                'data-description' => $this->translator->trans('Customers can access the product only by using the search bar.', [], 'Admin.Catalog.Feature'),
            ],
            $this->translator->trans('Nowhere', [], 'Admin.Catalog.Feature') => [
                'data-description' => $this->translator->trans('Only customers with the link can access the product.', [], 'Admin.Catalog.Feature'),
            ],
        ];
    }
}
