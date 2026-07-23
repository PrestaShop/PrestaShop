<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use Db;
use Language;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeManagerBuilder;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;

/**
 * Computes the refreshed value for the "Front office Translations" KPI.
 *
 * This is inherently theme/legacy-manifest-driven (it reads per-theme, per-language translation
 * progress counters that are themselves populated by the legacy translation tools), so it relies
 * directly on the legacy ThemeManagerBuilder/Theme repository and Language::getLanguages() helpers.
 */
class FrontofficeTranslationsKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly LegacyContext $legacyContext,
        protected readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $themes = (new ThemeManagerBuilder($this->legacyContext->getContext(), Db::getInstance()))
            ->buildRepository()
            ->getList();
        $languages = Language::getLanguages();

        $total = 0;
        $translated = 0;
        foreach ($themes as $theme) {
            foreach ($languages as $language) {
                $kpiKey = substr(strtoupper($theme->getName() . '_' . $language['iso_code']), 0, 16);
                $total += (int) $this->configuration->get('TRANSLATE_TOTAL_' . $kpiKey);
                $translated += (int) $this->configuration->get('TRANSLATE_DONE_' . $kpiKey);
            }
        }

        $value = 0;
        if ($translated) {
            $value = round(100 * $translated / $total, 1);
        }
        $value = $value . '%';

        $this->configuration->set('FRONTOFFICE_TRANSLATIONS', $value);
        $this->configuration->set('FRONTOFFICE_TRANSLATIONS_EXPIRE', strtotime('+2 min'));

        return new KpiRefreshValue((string) $value);
    }
}
