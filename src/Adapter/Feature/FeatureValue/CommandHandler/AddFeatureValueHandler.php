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
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\CommandHandler\AddFeatureValueHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Exception\CannotAddFeatureValueException;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValue\Exception\FeatureValueConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Domain\Language\FeatureValueId;

/**
 * Handles feature value addition using legacy logic.
 */
final class AddFeatureValueHandler implements AddFeatureValueHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddFeatureValueCommand $command)
    {
        $this->assertFeatureExists($command->getFeatureId());

        $featureValue = new FeatureValue();
        $featureValue->id_feature = $command->getFeatureId()->getValue();
        $featureValue->value = $command->getLocalizedValues();

        if (false === $featureValue->validateFields(false)) {
            throw new FeatureValueConstraintException('Invalid feature value data');
        }
        if (false === $featureValue->validateFieldsLang(false)) {
            throw new FeatureValueConstraintException('Invalid feature value', FeatureValueConstraintException::INVALID_VALUE);
        }
        if (false === $featureValue->add()) {
            throw new CannotAddFeatureValueException($command->getFeatureId(), 'Unable to create new feature value');
        }

        return new FeatureValueId((int) $featureValue->id);
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
