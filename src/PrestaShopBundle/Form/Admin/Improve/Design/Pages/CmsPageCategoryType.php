<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 17.16
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Pages;


use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
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
        ;
    }
}
