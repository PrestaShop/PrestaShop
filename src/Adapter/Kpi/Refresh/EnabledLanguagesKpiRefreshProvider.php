<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use Language;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;

/**
 * Computes the refreshed value for the "Enabled Languages" KPI.
 *
 * No clean modern equivalent for counting active languages was found (LanguageRepository only
 * exposes single-entity reads), so this relies directly on the legacy Language::countActiveLanguages()
 * read-only static helper.
 */
class EnabledLanguagesKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $value = Language::countActiveLanguages();

        $this->configuration->set('ENABLED_LANGUAGES', $value);
        $this->configuration->set('ENABLED_LANGUAGES_EXPIRE', strtotime('+1 min'));

        return new KpiRefreshValue((string) $value);
    }
}
