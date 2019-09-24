<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\Feature\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Feature\Exception\FeatureConstraintException;

/**
 * VO for feature identification
 */
final class FeatureId
{
    /**
     * @var int
     */
    private $featureId;

    /**
     * @param int $featureId
     *
     * @throws FeatureConstraintException
     */
    public function __construct(int $featureId)
    {
        $this->assertIsGreaterThanZero($featureId);
        $this->featureId = $featureId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->featureId;
    }

    /**
     * @param int $value
     *
     * @throws FeatureConstraintException
     */
    private function assertIsGreaterThanZero(int $value)
    {
        if (0 > $value) {
            throw new FeatureConstraintException(
                sprintf(
                    'Feature id %s is invalid. Feature id must be greater than zero.',
                    $value
                )
            );
        }
    }
}
