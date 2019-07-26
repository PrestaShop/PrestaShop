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

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

final class CustomizableFeatureValue
{
    public const MAX_SIZE = 255;

    private $featureId;

    /**
     * @param int $featureId
     * @param string $featureValue
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $featureId, string $featureValue)
    {
        $this->setFeatureId($featureId);

        $this->assertValueNameIsValid($featureValue);
        $this->assertValueSizeIsValid($featureValue);
    }

    private function setFeatureId(int $featureId): void
    {
        /**
         * @todo: change me when FeatureId object is available.
         */
        $this->featureId = new class($featureId) {

            private $featureId;

            public function __construct(int $featureId)
            {
                $this->featureId = $featureId;
            }

            public function getValue(): int
            {
                return $this->featureId;
            }
        };
    }

    /**
     * @param string $featureValue
     *
     * @throws ProductConstraintException
     */
    private function assertValueNameIsValid(string $featureValue): void
    {
        $pattern = '/^[^<>={}]*$/u';
        if (!preg_match($pattern, $featureValue)) {
            throw new ProductConstraintException(
                sprintf(
                    'Customizable feature value name "%s" did not matched given regex pattern "%s"',
                    $featureValue,
                    $pattern
                ),
                ProductConstraintException::INVALID_CUSTOMIZABLE_FEATURE_VALUE
            );
        }
    }

    /**
     * @param string $featureValue
     *
     * @throws ProductConstraintException
     */
    private function assertValueSizeIsValid(string $featureValue): void
    {
        if (strlen($featureValue) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Customizable feature value name "%s" is too long. Max size is %d',
                    $featureValue,
                    self::MAX_SIZE
                ),
                ProductConstraintException::CUSTOMIZABLE_FEATURE_VALUE_TOO_LONG
            );
        }
    }
}
