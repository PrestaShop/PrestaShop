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

namespace PrestaShop\PrestaShop\Adapter\Feature\FeatureValue\CommandHandler;

use Feature;
use FeatureValue;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Command\EditFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\CommandHandler\EditFeatureValueHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Exception\FeatureValueConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;

/**
 * Handles feature edition using legacy logic
 */
final class EditFeatureValueHandler implements EditFeatureValueHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditFeatureValueCommand $command)
    {
        $featureValue = new FeatureValue($command->getFeatureValueId()->getValue());

        if (null !== $command->getFeatureId()) {
            $this->assertFeatureExists($command->getFeatureId());
            $featureValue->id_feature = $command->getFeatureId()->getValue();
        }

        if (null !== $command->getLocalizedValues()) {
            $featureValue->value = $command->getLocalizedValues();
        }

        if (false === $featureValue->validateFields(false)) {
            throw new FeatureValueConstraintException('Invalid data when updating feature value');
        }
        if (false === $featureValue->validateFieldsLang(false)) {
            throw new FeatureValueConstraintException(
                'Invalid data when updating feature value',
                FeatureValueConstraintException::INVALID_VALUE
            );
        }
        if (false === $featureValue->update()) {
            throw new FeatureValueConstraintException(
                sprintf('Failed to edit Feature value with id "%s".', $featureValue->id)
            );
        }
    }

    /**
     * Asserts that feature exists by given ID
     *
     * @param FeatureId $featureId
     *
     * @throws FeatureNotFoundException
     */
    private function assertFeatureExists(FeatureId $featureId)
    {
        $feature = new Feature($featureId->getValue());

        if (empty($feature->id)) {
            throw new FeatureNotFoundException($featureId, 'Feature could not be found.');
        }
    }
}
