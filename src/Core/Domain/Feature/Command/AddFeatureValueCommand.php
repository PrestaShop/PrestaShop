<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

/**
 * Class AddFeatureValueCommand is used to add predefined feature values (as opposed to custom values which are
 * only assigned to a Specific product)
 */
class AddFeatureValueCommand
{
    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @var string[]
     */
    private $localizedValues;

    /**
     * @param int $featureId
     * @param string[] $localizedValues
     */
    public function __construct(int $featureId, array $localizedValues)
    {
        $this->featureId = new FeatureId($featureId);
        $this->localizedValues = $localizedValues;
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId(): FeatureId
    {
        return $this->featureId;
    }

    /**
     * @return array
     */
    public function getLocalizedValues(): array
    {
        return $this->localizedValues;
    }
}
