<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/**
 * Tells whether PDFs go through the Twig pipeline or the legacy PDF/HTMLTemplate stack.
 *
 * Modules customizing PDFs through actionGetPdfRenderer or actionGetPdfTemplateObject
 * only plug into the legacy stack, so it is kept whenever one of them is hooked,
 * even with pdf_twig_renderer enabled.
 */
final class TwigPdfRendererChecker
{
    private const LEGACY_CUSTOMIZATION_HOOKS = [
        'actionGetPdfRenderer',
        'actionGetPdfTemplateObject',
    ];

    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly HookInformationProvider $hookInformationProvider
    ) {
    }

    public function isEnabled(): bool
    {
        if (!$this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_PDF_TWIG_RENDERER)) {
            return false;
        }

        foreach (self::LEGACY_CUSTOMIZATION_HOOKS as $hookName) {
            if (!empty($this->hookInformationProvider->getRegisteredModulesByHookName($hookName))) {
                return false;
            }
        }

        return true;
    }
}
