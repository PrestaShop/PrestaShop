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
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * This form class is responsible to generate the virtual product
 */
class ProductVirtual extends CommonModelAbstractType
{
    private $translator;
    private $legacyContext;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     */
    public function __construct($translator, $legacyContext)
    {
        $this->translator = $translator;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('is_virtual_file', 'choice', array(
            'choices'  => array(
                1 => $this->translator->trans('Yes', [], 'AdminProducts'),
                0 => $this->translator->trans('No', [], 'AdminProducts'),
            ),
            'expanded' => true,
            'required' => true,
            'multiple' => false,
        ))
        ->add('file', 'file', array(
            'required' => false,
            'label' => $this->translator->trans('File', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\File(array('maxSize' => '8M')),
            )
        ))
        ->add('name', 'text', array(
            'label'    => $this->translator->trans('Filename', [], 'AdminProducts'),
            'constraints' => array(
                new Assert\NotBlank(),
            ),
        ))
        ->add('nb_downloable', 'number', array(
            'label'    => $this->translator->trans('Number of allowed downloads', [], 'AdminProducts'),
            'required' => false,
            'constraints' => array(
                new Assert\Type(array('type' => 'numeric')),
            ),
        ))
        ->add('expiration_date', 'text', array(
            'label'    => $this->translator->trans('Expiration date', [], 'AdminProducts'),
            'required' => false,
            'attr' => ['class' => 'date', 'placeholder' => 'YYY-MM-DD']
        ))
        ->add('nb_days', 'number', array(
            'label'    => $this->translator->trans('Number of days', [], 'AdminProducts'),
            'required' => false,
            'constraints' => array(
                new Assert\Type(array('type' => 'numeric')),
            )
        ))
        ->add('save', 'button', array(
            'label' => $this->translator->trans('Save', [], 'AdminProducts'),
            'attr' => ['class' => 'btn-primary pull-right']
        ));

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            //if this partial form is submit from a parent form, disable it
            if ($form->getParent()) {
                $event->setData([]);
                $form->add('name', 'text', array('mapped' => false));
            } elseif ($data['is_virtual_file'] == 0) {
                //disable name mapping when is virtual not defined to yes
                $form->add('name', 'text', array('mapped' => false));
            }
        });
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'product_virtual';
    }
}
