<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Team\Employee\Configuration;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;

/**
 * Class OptionsChecker checks if employee options can be changed depending on current shop context.
 */
final class OptionsChecker implements OptionsCheckerInterface
{
    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @param FeatureInterface $multistoreFeature
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     */
    public function __construct(
        FeatureInterface $multistoreFeature,
        MultistoreContextCheckerInterface $multistoreContextChecker
    ) {
        $this->multistoreFeature = $multistoreFeature;
        $this->multistoreContextChecker = $multistoreContextChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function canBeChanged(): bool
    {
        if (!$this->multistoreFeature->isUsed()
            && $this->multistoreContextChecker->isSingleShopContext()
        ) {
            return true;
        }

        return $this->multistoreContextChecker->isAllShopContext();
    }
}
