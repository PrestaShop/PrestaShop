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

namespace PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Command;

use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Exception\FeatureValueConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Language\FeatureValueId;

/**
 * Edits feature value
 */
class EditFeatureValueCommand
{
    /**
     * @var FeatureValueId
     */
    private $featureValueId;

    /**
     * @var array
     */
    private $localizedValues;

    /**
     * @var FeatureId
     */
    private $featureId;

    /**
     * @param int $featureValueId
     */
    public function __construct($featureValueId)
    {
        $this->featureValueId = new FeatureValueId($featureValueId);
    }

    /**
     * @return FeatureValueId
     */
    public function getFeatureValueId()
    {
        return $this->featureValueId;
    }

    /**
     * @return array
     */
    public function getLocalizedValues()
    {
        return $this->localizedValues;
    }

    /**
     * @param array $localizedValues
     *
     * @return EditFeatureValueCommand
     */
    public function setLocalizedValues(array $localizedValues)
    {
        if (empty($localizedValues)) {
            throw new FeatureValueConstraintException(
                'Feature value cannot be empty',
                FeatureValueConstraintException::EMPTY_VALUE
            );
        }

        $this->localizedValues = $localizedValues;

        return $this;
    }

    /**
     * @return FeatureId
     */
    public function getFeatureId()
    {
        return $this->featureId;
    }

    /**
     * @param int $featureId
     *
     * @return EditFeatureValueCommand
     */
    public function setFeatureId($featureId)
    {
        $this->featureId = new FeatureId($featureId);

        return $this;
    }
}
