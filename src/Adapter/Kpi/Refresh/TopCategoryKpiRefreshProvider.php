<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Computes the refreshed value for the "Top Category" KPI.
 */
class TopCategoryKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContextInterface $shopContext,
        protected readonly ConfigurationInterface $configuration,
        protected readonly TranslatorInterface $translator,
        protected readonly LanguageContext $languageContext,
        protected readonly CategoryRepository $categoryRepository
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $shopIds = $this->shopContext->getContextShopIds();
        $dateFrom = date('Y-m-d', strtotime('-1 month'));
        $dateTo = date('Y-m-d');

        $row = $this->statsRepository->getBestSellingCategory($dateFrom, $dateTo, $shopIds);

        if (null === $row) {
            $value = $this->translator->trans('No category', [], 'Admin.Stats.Feature');
        } else {
            $langId = $this->languageContext->getId();
            $localizedNames = $this->categoryRepository->getLocalizedNames([new CategoryId($row['id_category'])]);
            $value = $localizedNames[$row['id_category']][$langId] ?? '';
        }

        $this->configuration->set('TOP_CATEGORY', $value);
        $this->configuration->set('TOP_CATEGORY_EXPIRE', strtotime('+1 day'));

        return new KpiRefreshValue((string) $value);
    }
}
