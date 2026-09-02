<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
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
        $combinationIds = $this->combinationRepository->getCombinationIds(
            new ProductId($options['product_id']),
            // @todo: shopConstraint should probably be passed to options instead of always loading combinations from all shops
            ShopConstraint::allShops()
        );
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
