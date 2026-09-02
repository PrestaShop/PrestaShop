<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
