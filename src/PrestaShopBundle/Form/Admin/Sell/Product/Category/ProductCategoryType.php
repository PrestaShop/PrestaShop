<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Category;

use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductCategoryType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('display_name', TextPreviewType::class, [
                'attr' => [
                    'class' => 'category-name-preview-input',
                ],
                'preview_class' => 'category-name-preview',
            ])
            ->add('name', HiddenType::class, [
                'attr' => [
                    'class' => 'category-name-input',
                ],
            ])
            ->add('id', HiddenType::class)
        ;
    }
}
