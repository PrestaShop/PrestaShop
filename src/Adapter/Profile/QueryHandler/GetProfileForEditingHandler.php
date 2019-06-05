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

namespace PrestaShop\PrestaShop\Adapter\Profile\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Query\GetProfileForEditing;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryHandler\GetProfileForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\QueryResult\EditableProfile;
use PrestaShop\PrestaShop\Core\Domain\Profile\ValueObject\ProfileId;
use Profile;

/**
 * Gets Profile for editing using legacy object model
 */
final class GetProfileForEditingHandler implements GetProfileForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetProfileForEditing $query)
    {
        $profile = $this->getProfile($query->getProfileId());

        return new EditableProfile(
            $query->getProfileId(),
            $profile->name
        );
    }

    /**
     * @param ProfileId $profileId
     *
     * @return Profile
     *
     * @throws ProfileNotFoundException
     */
    private function getProfile(ProfileId $profileId)
    {
        $profile = new Profile($profileId->getValue());

        if ($profile->id !== $profileId->getValue()) {
            throw new ProfileNotFoundException(
                sprintf('Profile with id "%s" was not found', $profileId->getValue())
            );
        }

        return $profile;
    }
}
