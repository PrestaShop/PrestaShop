<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Product;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product custom fields configuration form
 */
class ProductCustomField extends CommonAbstractType
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
        $this->locales = $legacyContext->getLanguages();
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('id_customization_field','Symfony\Component\Form\Extension\Core\Type\HiddenType', array(
            'required' => false,
        ))
        ->add('label', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'options' => [ 'constraints' => array(
                new Assert\NotBlank(),
                new Assert\Length(array('min' => 2))
            )],
            'locales' => $this->locales,
            'hideTabs' => true,
            'label' => $this->translator->trans('Label', [], 'Admin.Global')
        ))
        ->add('type', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => $this->translator->trans('Type', [], 'Admin.Catalog.Feature'),
            'choices'  => array(
                $this->translator->trans('Text', [], 'Admin.Global') => 1,
                $this->translator->trans('File', [], 'Admin.Global') => 0,
            ),
            'attr' => array(
                'class' => 'c-select',
            ),
            'choices_as_values' => true,
            'required' =>  true
        ))->add('require', 'Symfony\Component\Form\Extension\Core\Type\CheckboxType', array(
            'label'    => $this->translator->trans('Required', [], 'Admin.Global'),
            'required' => false,
        ));
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_custom_field';
    }
}
