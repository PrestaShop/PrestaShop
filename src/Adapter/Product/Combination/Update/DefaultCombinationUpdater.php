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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Combination\Update;

use Combination;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotAddCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CannotUpdateCombinationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Updates default combination for product
 */
class DefaultCombinationUpdater
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(
        CombinationRepository $combinationRepository
    ) {
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * @param CombinationId $defaultCombinationId
     *
     * @throws CoreException
     * @throws CannotAddCombinationException
     * @throws CombinationNotFoundException
     * @throws ProductConstraintException
     */
    public function setDefaultCombination(CombinationId $defaultCombinationId): void
    {
        $newDefaultCombination = $this->combinationRepository->get($defaultCombinationId);
        $productId = new ProductId((int) $newDefaultCombination->id_product);
        $currentDefaultCombination = $this->combinationRepository->findDefaultCombination($productId);

        if ($currentDefaultCombination) {
            $this->updateCombination($currentDefaultCombination, false);
        }

        $this->updateCombination($newDefaultCombination, true);
    }

    /**
     * @param Combination $combination
     * @param bool $isDefault
     *
     * @throws CannotAddCombinationException
     */
    private function updateCombination(Combination $combination, bool $isDefault): void
    {
        $combination->default_on = $isDefault;
        $this->combinationRepository->partialUpdate(
            $combination,
            ['default_on'],
            CannotUpdateCombinationException::FAILED_UPDATE_DEFAULT_COMBINATION
        );
    }
}
