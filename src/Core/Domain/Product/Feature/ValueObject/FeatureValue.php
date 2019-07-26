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

/**
 * Existing Feature value.
 */
final class FeatureValue
{

    private $featureId;

    private $featureValueId;

    /**
     * @param int $featureId
     * @param int $featureValueId
     */
    public function __construct(
        int $featureId,
        int $featureValueId
    ) {
        $this->setFeatureId($featureId);
        $this->setFeatureValueId($featureValueId);
    }

    /**
     * @return mixed
     */
    public function getFeatureId()
    {
        return $this->featureId;
    }

    /**
     * @return mixed
     */
    public function getFeatureValueId()
    {
        return $this->featureValueId;
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

    private function setFeatureValueId(int $featureValueId): void
    {
        /**
         * @todo: change me when FeatureValueId object is available.
         */
        $this->featureValueId = new class($featureValueId) {

            private $featureValueId;

            public function __construct(int $featureValueId)
            {
                $this->featureValueId = $featureValueId;
            }

            public function getValue(): int
            {
                return $this->featureValueId;
            }
        };
    }
}
