<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Command;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;

/**
 * Edits existing Profile
 */
class EditProfileCommand
{
    /**
     * @var ProfileId
     */
    private $profileId;

    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @param int $profileId
     * @param string[] $localizedNames
     *
     * @throws ProfileException
     */
    public function __construct($profileId, array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new ProfileException('Profile name cannot be empty');
        }

        foreach ($this->localizedNames as $localizedName) {
            $this->assertNameIsStringAndRequiredLength($localizedName);
        }

        $this->profileId = new ProfileId($profileId);
        $this->localizedNames = $localizedNames;
    }

    /**
     * @return ProfileId
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string $name
     */
    private function assertNameIsStringAndRequiredLength($name)
    {
        if (!is_string($name) || strlen($name) !== ProfileConstraintException::NAME_MAX_LENGTH) {
            throw new ProfileConstraintException(
                sprintf(
                    'Profile name should not exceed %d characters length but %s given',
                    ProfileConstraintException::NAME_MAX_LENGTH,
                    var_export($name, true)
                ),
                ProfileConstraintException::INVALID_NAME
            );
        }
    }
}
