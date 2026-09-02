<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Tag;

use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagType
 */
class TagType extends AbstractType
{
    use TranslatorAwareTrait;
    /**
     * @var array
     */
    private $languagesChoices;

    /**
     * @param array $languagesChoices
     */
    public function __construct(
        array $languagesChoices,
        TranslatorInterface $translator,
    ) {
        $this->languagesChoices = $languagesChoices;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', [], 'Admin.Global'),
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label' => $this->trans('Language', [], 'Admin.Global'),
                'choices' => $this->languagesChoices,
            ])
            ->add('products', ProductSearchType::class, [
                'include_combinations' => false,
                'label' => $this->trans('Products', [], 'Admin.Catalog.Feature'),
                'min_length' => 3,
                'limit' => 0,
            ])
        ;
    }
}
