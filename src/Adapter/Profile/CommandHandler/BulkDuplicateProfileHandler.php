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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use Access;
use PrestaShop\PrestaShop\Core\Domain\Profile\Command\BulkDuplicateProfileCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\CommandHandler\BulkDuplicateProfileHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\FailedToDuplicateProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileException;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\ProfileNotFoundException;
use PrestaShopException;
use Profile;

/**
 * Class BulkDuplicateProfileHandler
 *
 * @internal
 */
final class BulkDuplicateProfileHandler extends AbstractProfileHandler implements BulkDuplicateProfileHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(BulkDuplicateProfileCommand $command)
    {
        $entityIds = $command->getProfileIds();

        foreach ($entityIds as $entityId) {
            $entityIdValue = $entityId->getValue();

            try {
                $profile = new Profile($entityIdValue);

                if ($profile->id != $entityIdValue) {
                    throw new ProfileNotFoundException(sprintf('Profile with id %s cannot be found.', var_export($entityId, true)));
                }

                unset($profile->id);
                $profile->name = array_map(function ($name) {
                    return $name . ' (copy)';
                }, $profile->name);

                if (false === $profile->add() || !Access::copyAccess((int) $entityIdValue, $profile->id)) {
                    throw new FailedToDuplicateProfileException(sprintf('Failed to delete Profile with id %s', var_export($entityId, true)), FailedToDuplicateProfileException::CANNOT_ADD_PROFILE);
                }
            } catch (PrestaShopException $e) {
                throw new ProfileException(sprintf('Unexpected error occurred when duplicating Profile with id %s', var_export($entityIdValue, true)), 0, $e);
            }
        }
    }
}
