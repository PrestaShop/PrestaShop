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

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CombinationIdChoiceProvider implements ConfigurableFormChoiceProviderInterface
{
    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var CombinationNameBuilderInterface
     */
    private $combinationNameBuilder;

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @param CombinationRepository $combinationRepository
     * @param AttributeRepository $attributeRepository
     * @param CombinationNameBuilderInterface $combinationNameBuilder
     * @param int $languageId
     */
    public function __construct(
        CombinationRepository $combinationRepository,
        AttributeRepository $attributeRepository,
        CombinationNameBuilderInterface $combinationNameBuilder,
        int $languageId
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->attributeRepository = $attributeRepository;
        $this->languageId = new LanguageId($languageId);
        $this->combinationNameBuilder = $combinationNameBuilder;
    }

    /**
     * @param array<string, int> $options
     *
     * @return array<string, int>
     */
    public function getChoices(array $options): array
    {
        $options = $this->resolveOptions($options);
        $combinationIds = $this->combinationRepository->getCombinationIds(new ProductId($options['product_id']));
        $attributesInfo = $this->attributeRepository->getAttributesInfoByCombinationIds($combinationIds, $this->languageId);

        $choices = [];
        foreach ($attributesInfo as $combinationIdValue => $combinationAttributesInfo) {
            $choices[$this->combinationNameBuilder->buildName($combinationAttributesInfo)] = $combinationIdValue;
        }

        return $choices;
    }

    /**
     * @param array<string, int> $options
     */
    private function resolveOptions(array $options): array
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(['product_id']);
        $resolver->setAllowedTypes('product_id', 'int');

        return $resolver->resolve($options);
    }
}
