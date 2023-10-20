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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\SearchProductCombinations;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombination;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\ProductCombinationsCollection;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;

class SearchProductCombinationsHandler implements SearchProductCombinationsHandlerInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @param CombinationRepository $combinationRepository
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        CombinationNameBuilderInterface $combinationNameBuilder
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->combinationNameBuilder = $combinationNameBuilder;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(SearchProductCombinations $query): ProductCombinationsCollection
    {
        $combinationsAttributesInformation = $this->combinationRepository->searchProductCombinations(
            $query->getProductId(),
            $query->getLanguageId(),
            $query->getShopConstraint(),
            $query->getSearchPhrase(),
            $query->getLimit()
        );

        $productCombinations = [];
        foreach ($combinationsAttributesInformation as $combinationId => $combinationAttributesInformation) {
            $productCombinations[] = new ProductCombination(
                $combinationId,
                $this->combinationNameBuilder->buildName($combinationAttributesInformation)
            );
        }

        return new ProductCombinationsCollection($productCombinations);
    }
}
