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

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationAttributeInformation;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CombinationNameByIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var int
     */
    private $languageId;

    /**
     * @param CombinationRepository $combinationRepository
     * @param int $languageId
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        int $languageId
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->languageId = $languageId;
    }

    /**
     * {@inheritDoc}
     */
    public function getChoices(array $options)
    {
        $productId = new ProductId($this->resolveOptions($options)['product_id']);
        $combinations = $this->combinationRepository->getProductCombinations($productId);

        $combinationIds = array_map(function ($combination): int {
            return (int) $combination['id_product_attribute'];
        }, $combinations);

        $attributesInformation = $this->combinationRepository->getAttributesInfoByCombinationIds(
            $combinationIds,
            new LanguageId($this->languageId)
        );

        return $this->formatChoices($attributesInformation);
    }

    protected function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired('product_id');
        $resolver->setAllowedTypes('product_id', 'int');

        return $resolver->resolve($options);
    }

    private function formatChoices(array $attributesInfoByCombinationId): array
    {
        $choices = [];

        foreach ($attributesInfoByCombinationId as $combinationId => $attributesInfo) {
            $choices[$this->buildCombinationName($attributesInfo)] = $combinationId;
        }

        return $choices;
    }

    private function buildCombinationName(array $attributesInformation): string
    {
        $combinedNameParts = [];
        foreach ($attributesInformation as $attributeInfo) {
            $combinedNameParts[] = sprintf(
                '%s - %s',
                $attributeInfo['attribute_group_name'],
                $attributeInfo['attribute_name']
            );
        }

        return implode(', ', $combinedNameParts);
    }
}
