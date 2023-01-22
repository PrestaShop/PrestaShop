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

namespace PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\CombinationAttributeInformation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Builds combination name by attributes information
 */
class CombinationNameBuilder implements CombinationNameBuilderInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var string
     */
    protected $attributesSeparator;

    /**
     * @var string
     */
    private $attributesInsideSeparator;

    public function __construct(
        TranslatorInterface $translator,
        string $attributesSeparator,
        string $attributesInsideSeparator
    ) {
        $this->translator = $translator;
        $this->attributesSeparator = $attributesSeparator;
        $this->attributesInsideSeparator = $attributesInsideSeparator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildName(array $attributesInfo): string
    {
        return implode(
            $this->attributesSeparator,
            array_map(
                [$this, 'translateAttribute'],
                $attributesInfo
            )
        );
    }

    protected function translateAttribute(CombinationAttributeInformation $combinationAttributeInfo): string
    {
        return $this->translator->trans(
            '%attribute_group_name% ' . $this->attributesInsideSeparator . ' %attribute_name%',
            [
                '%attribute_group_name%' => $combinationAttributeInfo->getAttributeGroupName(),
                '%attribute_name%' => $combinationAttributeInfo->getAttributeName(),
            ],
            'Admin.Catalog.Feature'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildFullName(string $productName, array $attributesInfo): string
    {
        return $this->translator->trans(
            '%product_name%: %combination_details%',
            [
                '%product_name%' => $productName,
                '%combination_details%' => $this->buildName($attributesInfo),
            ],
            'Admin.Catalog.Feature'
        );
    }
}
