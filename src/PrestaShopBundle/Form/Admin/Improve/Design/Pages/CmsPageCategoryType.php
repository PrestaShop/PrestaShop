<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 17.16
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Pages;


use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class CmsPageCategoryType extends AbstractType
{
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
    }
}
