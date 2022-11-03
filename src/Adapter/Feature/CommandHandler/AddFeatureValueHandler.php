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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\CommandHandler;

use FeatureValue;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureRepository;
use PrestaShop\PrestaShop\Adapter\Feature\Repository\FeatureValueRepository;
use PrestaShop\PrestaShop\Core\Domain\Feature\Command\AddFeatureValueCommand;
use PrestaShop\PrestaShop\Core\Domain\Feature\CommandHandler\AddFeatureValueHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureValueId;

/**
 * Handles adding of feature value using legacy model.
 */
class AddFeatureValueHandler implements AddFeatureValueHandlerInterface
{
    /**
     * @var FeatureRepository
     */
    private $featureRepository;

    /**
     * @var FeatureValueRepository
     */
    private $featureValueRepository;

    public function __construct(
        FeatureRepository $featureRepository,
        FeatureValueRepository $featureValueRepository
    ) {
        $this->featureRepository = $featureRepository;
        $this->featureValueRepository = $featureValueRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(AddFeatureValueCommand $command): FeatureValueId
    {
        $this->featureRepository->assertExists($command->getFeatureId());

        return $this->featureValueRepository->add($this->fillObjectWithCommand($command));
    }

    /**
     * @param AddFeatureValueCommand $command
     *
     * @return FeatureValue
     */
    private function fillObjectWithCommand(AddFeatureValueCommand $command): FeatureValue
    {
        $featureValue = new FeatureValue();
        $featureValue->id_feature = (int) $command->getFeatureId()->getValue();
        $featureValue->value = $command->getLocalizedValues();

        return $featureValue;
    }
}
