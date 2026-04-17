<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Category;

use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryDeleteMode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DeleteCategoriesType.
 */
class DeleteCategoriesType extends AbstractType
{
    /**
     * @var array
     */
    private $categoryDeleteModelChoices;

    /**
     * @param array $categoryDeleteModelChoices
     */
    public function __construct(array $categoryDeleteModelChoices)
    {
        $this->categoryDeleteModelChoices = $categoryDeleteModelChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('delete_mode', ChoiceType::class, [
                'expanded' => true,
                'choices' => $this->categoryDeleteModelChoices,
                'label' => false,
                'data' => CategoryDeleteMode::ASSOCIATE_PRODUCTS_WITH_PARENT_AND_DISABLE,
            ])
            ->add('categories_to_delete', CollectionType::class, [
                'entry_type' => HiddenType::class,
                'label' => false,
                'allow_add' => true,
            ])
            ->add('categories_to_delete_parent', HiddenType::class, [
                'label' => false,
            ]);
    }
}
