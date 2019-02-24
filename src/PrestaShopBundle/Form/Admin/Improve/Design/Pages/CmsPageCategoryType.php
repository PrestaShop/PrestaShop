<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 17.16
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Pages;


use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class CmsPageCategoryType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $allCmsCategories;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @param array $allCmsCategories
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(array $allCmsCategories, $isShopFeatureEnabled)
    {
        $this->allCmsCategories = $allCmsCategories;
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class)
            ->add('is_displayed', SwitchType::class, [
                'required' => false,
            ])
            ->add('parent_category', MaterialChoiceTreeType::class, [
                'required' => false,
                'choices_tree' => $this->allCmsCategories,
                'choice_value' => 'id_cms_category',
            ])
            ->add('description', TranslatableType::class, [
                'required' => false,
                'type' => TextareaType::class,
            ])
            ->add('meta_title', TranslatableType::class, [
                'required' => false,
            ])
            ->add('meta_description', TranslatableType::class, [
                'required' => false,
            ])
            ->add('meta_keywords', TranslatableType::class, [
                'required' => false,
            ])
            ->add('friendly_url', TranslatableType::class)
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->trans('Shop association', [], 'Admin.Global')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }
}
