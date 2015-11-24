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

use PrestaShopBundle\Form\Admin\Type\CommonModelAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslateType;

/**
 * This form class is responsible to generate the product SEO form
 */
class ProductSeo extends CommonModelAbstractType
{
    private $translator;
    private $locales;

    /**
     * Constructor
     *
     * @param object $container The SF2 container
     */
    public function __construct($container)
    {
        $this->translator = $container->get('prestashop.adapter.translator');
        $this->locales = $container->get('prestashop.adapter.legacy.context')->getLanguages();
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('meta_title', new TranslateType(
            'text',
            array('required' => false),
            $this->locales
        ), array(
            'label' => $this->translator->trans('Meta title', [], 'AdminProducts'),
            'required' => false
        ))
        ->add('meta_description', new TranslateType(
            'text',
            array('required' => false),
            $this->locales
        ), array(
            'label' => $this->translator->trans('Meta description', [], 'AdminProducts'),
            'required' => false
        ))
        ->add('link_rewrite', new TranslateType(
            'text',
            array(),
            $this->locales
        ), array('label' => $this->translator->trans('Friendly URL:', [], 'AdminProducts')));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_seo';
    }
}
