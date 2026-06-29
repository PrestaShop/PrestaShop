<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductTypeChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FeatureInterface
     */
    private $combinationFeature;

    /**
     * @param TranslatorInterface $translator
     * @param FeatureInterface $combinationFeature
     */
    public function __construct(
        TranslatorInterface $translator,
        FeatureInterface $combinationFeature
    ) {
        $this->translator = $translator;
        $this->combinationFeature = $combinationFeature;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoicesAttributes()
    {
        return [
            $this->trans('Standard product', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('A physical product that needs to be shipped.', 'Admin.Catalog.Feature'),
                'icon' => 'checkroom',
            ],
            $this->trans('Product with combinations', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('A product with different variations (size, color, etc.) from which customers can choose.', 'Admin.Catalog.Feature'),
                'icon' => 'layers',
            ],
            $this->trans('Pack of products', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('A collection of products from your catalog.', 'Admin.Catalog.Feature'),
                'icon' => 'grid_view',
            ],
            $this->trans('Virtual product', 'Admin.Catalog.Feature') => [
                'data-description' => $this->trans('An intangible product that doesn\'t require shipping. You can also add a downloadable file.', 'Admin.Catalog.Feature'),
                'icon' => 'qr_code',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        $choices = [
            $this->trans('Standard product', 'Admin.Catalog.Feature') => ProductType::TYPE_STANDARD,
            $this->trans('Product with combinations', 'Admin.Catalog.Feature') => ProductType::TYPE_COMBINATIONS,
            $this->trans('Pack of products', 'Admin.Catalog.Feature') => ProductType::TYPE_PACK,
            $this->trans('Virtual product', 'Admin.Catalog.Feature') => ProductType::TYPE_VIRTUAL,
        ];

        if (!$this->combinationFeature->isActive()) {
            unset($choices[$this->trans('Product with combinations', 'Admin.Catalog.Feature')]);
        }

        return $choices;
    }

    /**
     * @param string $id
     * @param string $domain
     *
     * @return string
     */
    private function trans(string $id, string $domain): string
    {
        return $this->translator->trans($id, [], $domain);
    }
}
