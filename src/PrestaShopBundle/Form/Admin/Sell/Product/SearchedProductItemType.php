<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\EntityItemType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchedProductItemType extends CommonAbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('unique_identifier', HiddenType::class, [
                'label' => false,
            ])
            ->add('product_id', HiddenType::class, [
                'label' => false,
            ])
            ->add('combination_id', HiddenType::class, [
                'label' => false,
            ])
        ;
    }

    public function getParent(): string
    {
        return EntityItemType::class;
    }
}
