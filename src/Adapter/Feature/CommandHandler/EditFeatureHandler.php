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

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use Feature;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\EditFeatureCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\EditFeatureHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\CannotEditFeatureException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;

/**
 * Handles feature editing.
 */
final class EditFeatureHandler extends AbstractObjectModelHandler implements EditFeatureHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditFeatureCommand $command)
    {
        $feature = new Feature($command->getFeatureId()->getValue());

        if (empty($feature->id)) {
            throw new FeatureNotFoundException('Feature could not be found.');
        }

        if (null !== $command->getLocalizedNames()) {
            $feature->name = $command->getLocalizedNames();
        }

        if (null !== $command->getAssociatedShopIds()) {
            $this->associateWithShops($feature, $command->getAssociatedShopIds());
        }

        if (false === $feature->validateFields(false)) {
            throw new FeatureConstraintException('Invalid data when updating feature');
        }

        if (false === $feature->validateFieldsLang(false)) {
            throw new FeatureConstraintException('Invalid data when updating feature', FeatureConstraintException::INVALID_NAME);
        }

        if (false === $feature->update()) {
            throw new CannotEditFeatureException(sprintf('Failed to edit Feature with id "%s".', $feature->id));
        }
    }
}
