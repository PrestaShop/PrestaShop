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

namespace PrestaShop\PrestaShop\Core\Domain\Feature\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;

/**
 * Adds new feature
 */
class AddFeatureCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var int[]
     */
    private $shopAssociation;

    /**
     * @param string[] $localizedNames
     * @param int[] $shopAssociation
     */
    public function __construct(array $localizedNames, array $shopAssociation = [])
    {
        $this->assertNamesAreValid($localizedNames);

        $this->localizedNames = $localizedNames;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * Asserts that feature names are valid.
     *
     * @param string[] $names
     *
     * @throws FeatureConstraintException
     */
    private function assertNamesAreValid(array $names)
    {
        if (empty($names)) {
            throw new FeatureConstraintException(
                'Feature name cannot be empty',
                FeatureConstraintException::EMPTY_NAME
            );
        }
    }
}
