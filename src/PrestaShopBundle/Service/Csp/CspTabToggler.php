<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Service\Csp;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Enables or disables the "Security headers" and "Content Security Policy" tabs depending on the
 * 'csp' feature flag.
 */
final class CspTabToggler
{
    /** @var list<string> */
    public const TAB_CLASS_NAMES = ['AdminSecurityHeaders', 'AdminSecurityCsp'];

    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
        private readonly TabRepository $tabRepository,
    ) {
    }

    public function sync(): void
    {
        if ($this->featureFlagChecker instanceof ResetInterface) {
            $this->featureFlagChecker->reset(); // refresh FeatureFlagChecker cache
        }

        $enabled = $this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP);

        foreach (self::TAB_CLASS_NAMES as $className) {
            $this->tabRepository->changeStatusByClassName($className, $enabled);
        }
    }
}
