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
 * Class TopCategoryKpi.
 *
 * @internal
 */
final class TopCategoryKpi implements KpiInterface
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
     * @var int
     */
    private $employeeIdLang;

    /**
     * @param TranslatorInterface $translator
     * @param ConfigurationInterface $kpiConfiguration
     * @param string $sourceUrl
     * @param int $employeeIdLang
     */
    public function __construct(
        TranslatorInterface $translator,
        ConfigurationInterface $kpiConfiguration,
        $sourceUrl,
        $employeeIdLang
    ) {
        $this->translator = $translator;
        $this->kpiConfiguration = $kpiConfiguration;
        $this->sourceUrl = $sourceUrl;
        $this->employeeIdLang = $employeeIdLang;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $helper = new HelperKpi();
        $helper->id = 'box-top-category';
        $helper->icon = 'money';
        $helper->color = 'color3';
        $helper->title = $this->translator->trans('Top Category', [], 'Admin.Catalog.Feature');
        $helper->subtitle = $this->translator->trans('30 days', [], 'Admin.Global');

        $topCategory = $this->kpiConfiguration->get('TOP_CATEGORY');

        if (isset($topCategory[$this->employeeIdLang])) {
            $helper->value = $topCategory[$this->employeeIdLang];
        }

        $topCategoryExpire = $this->kpiConfiguration->get('TOP_CATEGORY_EXPIRE');
        if (isset($topCategoryExpire[$this->employeeIdLang])) {
            $topCategoryExpire = $topCategoryExpire[$this->employeeIdLang];
        } else {
            $topCategoryExpire = false;
        }

        $helper->source = $this->sourceUrl;
        $helper->refresh = $topCategoryExpire < time();

        return $helper->generate();
    }
}
