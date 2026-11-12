<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\B2B\BusinessEntity;
use PrestaShopBundle\Entity\Repository\BusinessEntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class BusinessEntityChoiceType extends AbstractType
{
    public function __construct(
        private readonly ShopContext $shopContext,
    ) {
    }

    public function getParent(): string
    {
        return EntityChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'class' => BusinessEntity::class,
                'choice_label' => 'name',
                'shop_ids' => fn (Options $options) => $this->shopContext->isAllShopContext() ? null : $this->shopContext->getAssociatedShopIds(),
                'query_builder' => static function (Options $options) {
                    return static fn (BusinessEntityRepository $repository) => $repository->createByShopIdsQueryBuilder($options['shop_ids']);
                },
            ])
            ->setAllowedTypes('shop_ids', ['null', 'int[]']);
    }
}
