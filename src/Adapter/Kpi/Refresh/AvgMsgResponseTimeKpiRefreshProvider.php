<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Stats\Repository\StatsRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;
use PrestaShop\PrestaShop\Core\Shop\ShopContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Computes the refreshed value for the "Average Message Response Time" KPI.
 */
class AvgMsgResponseTimeKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly StatsRepository $statsRepository,
        protected readonly ShopContextInterface $shopContext,
        protected readonly ConfigurationInterface $configuration,
        protected readonly TranslatorInterface $translator
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $shopIds = $this->shopContext->getContextShopIds();
        $dateFrom = date('Y-m-d', strtotime('-31 day'));
        $dateTo = date('Y-m-d', strtotime('-1 day'));

        $rows = $this->statsRepository->getMessageResponseDelays($dateFrom, $dateTo, $shopIds);

        if (empty($rows)) {
            $average = 0;
        } else {
            $questionsSum = 0;
            $repliesSum = 0;
            foreach ($rows as $row) {
                $questionsSum += strtotime($row['question']);
                $repliesSum += strtotime($row['reply']);
            }
            $average = round(($repliesSum - $questionsSum) / count($rows) / 3600, 1);
        }

        $value = $this->translator->trans(
            '%average% hours',
            ['%average%' => $average],
            'Admin.Stats.Feature'
        );

        $this->configuration->set('AVG_MSG_RESPONSE_TIME', $value);
        $this->configuration->set('AVG_MSG_RESPONSE_TIME_EXPIRE', strtotime('+4 hour'));

        return new KpiRefreshValue((string) $value);
    }
}
