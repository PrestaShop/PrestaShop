<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Kpi;

use HelperKpi;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AverageProductsInCategoryKpi.
 *
 * @internal
 */
final class AverageProductsInCategoryKpi implements KpiInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var ConfigurationInterface
     */
    private $kpiConfiguration;

    /**
     * @var string
     */
    private $sourceUrl;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $kpiConfiguration
     * @param string $sourceUrl
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $kpiConfiguration,
        $sourceUrl
    ) {
        $this->translator = $translator;
        $this->kpiConfiguration = $kpiConfiguration;
        $this->sourceUrl = $sourceUrl;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-products-per-category';
        $helper->icon = 'search';
        $helper->color = 'color4';
        $helper->title =
            $this->translator->trans('Average number of products per category', [], 'Admin.Catalog.Feature');

        if (false !== $this->kpiConfiguration->get('PRODUCTS_PER_CATEGORY')) {
            $helper->value = $this->kpiConfiguration->get('PRODUCTS_PER_CATEGORY');
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $this->kpiConfiguration->get('PRODUCTS_PER_CATEGORY_EXPIRE') < time();

        return $helper->generate();
    }
}
