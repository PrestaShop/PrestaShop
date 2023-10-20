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

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\DefaultCombinationUpdater;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Update\Filler\CombinationFillerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Command\UpdateCombinationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CommandHandler\UpdateCombinationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;

/**
 * Handles the @see UpdateCombinationCommand using legacy object model
 */
class UpdateCombinationHandler implements UpdateCombinationHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationFillerInterface
     */
    private $combinationFiller;

    /**
     * @var DefaultCombinationUpdater
     */
    private $defaultCombinationUpdater;

    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationFillerInterface $combinationFiller,
        DefaultCombinationUpdater $defaultCombinationUpdater
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationFiller = $combinationFiller;
        $this->defaultCombinationUpdater = $defaultCombinationUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateCombinationCommand $command): void
    {
        $combination = $this->combinationRepository->getByShopConstraint($command->getCombinationId(), $command->getShopConstraint());
        $updatableProperties = $this->combinationFiller->fillUpdatableProperties($combination, $command);

        $this->combinationRepository->partialUpdate(
            $combination,
            $updatableProperties,
            $command->getShopConstraint(),
            CannotUpdateCombinationException::FAILED_UPDATE_COMBINATION
        );

        // Only update default if the property is set AND is true
        if (true === $command->isDefault()) {
            $this->defaultCombinationUpdater->setDefaultCombination(
                $command->getCombinationId(),
                $command->getShopConstraint()
            );
        }
    }
}
