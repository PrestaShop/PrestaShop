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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This form class is responsible to generate the product shipping form
 */
class ProductShipping extends CommonModelAbstractType
{
    private $translator;
    private $container;
    private $carriersChoices;

    /**
     * Constructor
     *
     * @param object $container The SF2 container
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->translator = $container->get('prestashop.adapter.translator');
        $this->locales = $container->get('prestashop.adapter.legacy.context')->getLanguages();

        $carriers = $this->container->get('prestashop.adapter.data_provider.carrier')->getCarriers($this->locales[0]['id_lang'], false, false, false, null, \Carrier::ALL_CARRIERS);
        $this->carriersChoices = [];
        foreach ($carriers as $carrier) {
            $this->carriersChoices[$carrier['id_carrier']] = $carrier['name'].' ('.$carrier['delay'].')';
        }
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('width', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package width', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('height', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package height', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('depth', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package depth', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('weight', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Package weight', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('additional_shipping_cost', 'number', array(
            'required' => false,
            'label' => $this->translator->trans('Additional shipping fees (for a single item)', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Type(array('type' => 'float'))
            )
        ))
        ->add('selectedCarriers', 'choice', array(
            'choices' =>  $this->carriersChoices,
            'expanded' =>  true,
            'multiple' =>  true,
            'required' =>  false,
            'label' => $this->translator->trans('Carriers', [], 'AdminProducts')
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_shipping';
    }
}
