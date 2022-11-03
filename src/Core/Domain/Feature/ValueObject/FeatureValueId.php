<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\InvalidFeatureValueIdException;

/**
 * Defines FeatureValue ID with it's constraints.
 */
class FeatureValueId
{
    /**
     * @var int
     */
    private $featureValueId;

    /**
     * @param int $featureValueId
     *
     * @throws InvalidFeatureValueIdException
     */
    public function __construct(int $featureValueId)
    {
        $this->assertIsGreaterThanZero($featureValueId);

        $this->featureValueId = $featureValueId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->featureValueId;
    }

    /**
     * @param int $featureValueId
     *
     * @throws InvalidFeatureValueIdException
     */
    private function assertIsGreaterThanZero(int $featureValueId)
    {
        if (0 >= $featureValueId) {
            throw new InvalidFeatureValueIdException(sprintf('Invalid feature id %d supplied. Feature id must be positive integer.', $featureValueId));
        }
    }
}
