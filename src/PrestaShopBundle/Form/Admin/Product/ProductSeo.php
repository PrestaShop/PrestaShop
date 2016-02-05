<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product SEO form
 */
class ProductSeo extends CommonAbstractType
{
    private $translator;
    private $locales;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->context = $legacyContext;
        $this->locales = $legacyContext->getLanguages();
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('meta_title', \PrestaShopBundle\Form\Admin\Type\TranslateType::class, array(
            'type' => FormType\TextType::class,
            'options' => [
                'attr' => ['placeholder' => $this->translator->trans('Same title as the product, can be edited', [], 'AdminProducts')],
                'required' => false
            ],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' => $this->translator->trans('Meta title', [], 'AdminProducts'),
            'required' => false
        ))
        ->add('meta_description', \PrestaShopBundle\Form\Admin\Type\TranslateType::class, array(
            'type' => FormType\TextType::class,
            'options' => [
                'attr' => ['placeholder' => $this->translator->trans('Same description as the product, can be edited', [], 'AdminProducts')],
                'required' => false
            ],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' => $this->translator->trans('Meta description', [], 'AdminProducts'),
            'required' => false
        ))
        ->add('link_rewrite', \PrestaShopBundle\Form\Admin\Type\TranslateType::class, array(
            'type' => FormType\TextType::class,
            'options' => [],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' => $this->translator->trans('Friendly URL', [], 'AdminProducts'),
        ))
        ->add('redirect_type', FormType\ChoiceType::class, array(
            'choices'  => array(
                $this->translator->trans('No redirect (404)', [], 'AdminProducts') => '404',
                $this->translator->trans('Catalog Redirected permanently (301)', [], 'AdminProducts') => '301',
                $this->translator->trans('Redirected temporarily (302)', [], 'AdminProducts') => '302',
            ),
            'choices_as_values' => true,
            'required' => true,
            'label' => $this->translator->trans('Redirection when inactive', [], 'AdminProducts'),
        ))
        ->add('id_product_redirected', \PrestaShopBundle\Form\Admin\Type\TypeaheadProductCollectionType::class, array(
            'remote_url' => $this->context->getAdminLink('', false).'ajax_products_list.php?forceJson=1&disableCombination=1&exclude_packs=0&excludeVirtuals=0&limit=20&q=%QUERY',
            'mapping_value' => 'id',
            'mapping_name' => 'name',
            'placeholder' => $this->translator->trans('search in catalog...', [], 'AdminProducts'),
            'template_collection' => '<div class="title col-xs-9">%s</div><button type="button" class="btn btn-default delete"><i class="icon-trash"></i></button>',
            'limit' => 1,
            'required' => false,
            'label' => $this->translator->trans('Related product', [], 'AdminProducts')
        ));
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_seo';
    }
}
