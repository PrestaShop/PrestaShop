<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Catalog\Category;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbstractCategoryType.
 */
abstract class AbstractCategoryType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $customerGroupChoices;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $customerGroupChoices
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $customerGroupChoices,
        FeatureInterface $multistoreFeature
    ) {
        parent::__construct($translator, $locales);

        $this->customerGroupChoices = $customerGroupChoices;
        $this->multistoreFeature = $multistoreFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, array(
                'type' => TextType::class,
            ))
            ->add('active', SwitchType::class, array(
                'required' => false,
                'data' => true,
            ))
            ->add('description', TranslatableType::class, array(
                'type' => TextareaType::class,
                'required' => false,
            ))
            ->add('cover_image', FileType::class, array(
                'required' => false,
            ))
            ->add('thumbnail_image', FileType::class, array(
                'required' => false,
            ))
            ->add('menu_thumbnail_images', FileType::class, array(
                'multiple' => true,
                'required' => false,
            ))
            ->add('meta_title', TranslatableType::class, array(
                'type' => TextWithLengthCounterType::class,
                'options' => array(
                    'max_length' => 70,
                ),
            ))
            ->add('meta_description', TranslatableType::class, array(
                'type' => TextareaType::class,
                'options' => array(
                    'required' => false,
                ),
            ))
            ->add('meta_keyword', TranslatableType::class, array(
                'type' => TextType::class,
                'options' => array(
                    'required' => false,
                ),
            ))
            ->add('link_rewrite', TranslatableType::class, array(
                'type' => TextType::class,
            ))
            ->add('group_association', MaterialChoiceTableType::class, array(
                'choices' => $this->customerGroupChoices,
                'required' => false,
            ))
        ;

        if ($this->multistoreFeature->isUsed()) {
            $builder->add('shop_association', ShopChoiceTreeType::class);
        }
    }
}
