<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Kpi\Refresh;

use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshProviderInterface;
use PrestaShop\PrestaShop\Core\Kpi\Refresh\KpiRefreshValue;

/**
 * Computes the refreshed value for the "Front office Translations" KPI.
 *
 * Sums the per-theme, per-language progress counters that the translation pages store
 * in the KPI configuration.
 */
class FrontofficeTranslationsKpiRefreshProvider implements KpiRefreshProviderInterface
{
    public function __construct(
        protected readonly ThemeRepository $themeRepository,
        protected readonly LanguageDataProvider $languageDataProvider,
        protected readonly ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getValue(array $requestParameters = []): KpiRefreshValue
    {
        $languages = $this->languageDataProvider->getLanguages();

        $total = 0;
        $translated = 0;
        /** @var Theme $theme */
        foreach ($this->themeRepository->getList() as $theme) {
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
