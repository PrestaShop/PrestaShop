<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Search;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for alias search term
 */
class SearchTermType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', TextType::class, [
                'label' => $this->trans('Search term', 'Admin.Shopparameters.Feature'),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('aliases', CollectionType::class, [
                'label' => $this->trans('Aliases', 'Admin.Shopparameters.Feature'),
                'allow_add' => true,
                'allow_delete' => true,
                'entry_type' => AliasType::class,
                'entry_options' => [
                    'label' => false,
                    'row_attr' => [
                        'class' => 'alias-item',
                    ],
                ],
                'block_prefix' => 'aliases_collection',
                'constraints' => [
                ],
            ])
        ;
    }
}
