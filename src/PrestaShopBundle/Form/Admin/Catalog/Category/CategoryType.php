<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Catalog\Category;

use PrestaShopBundle\Form\Admin\Type\CategoryChoiceTreeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CategoryType.
 */
class CategoryType extends AbstractCategoryType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // Root category is always disabled
        $disabledCategories = array_merge(
            [
                (int) $this->configuration->get('PS_ROOT_CATEGORY'),
            ],
            $options['subcategories']
        );

        if (null !== $options['id_category']) {
            // when using CategoryType to edit category
            // user should not be able to select that category as parent
            $disabledCategories[] = $options['id_category'];
        }

        $builder
            ->add('id_parent', CategoryChoiceTreeType::class, [
                'disabled_values' => $disabledCategories,
                'label' => $this->trans('Parent category', 'Admin.Catalog.Feature'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'id_category' => null,
                'subcategories' => [],
            ])
            ->setAllowedTypes('subcategories', ['array'])
            ->setAllowedTypes('id_category', ['int', 'null']);
    }
}
