<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Form\Admin\Feature;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This Class is responsible to generate the product Features form
 */
class ProductFeature extends CommonAbstractType
{
    private $featureDataProvider;
    private $translator;
    private $locales;
    private $router;
    private $features;

    /**
     * Constructor
     *
     * @param object $translator
     * @param object $legacyContext
     * @param object $router
     * @param object $featureDataProvider
     */
    public function __construct($translator, $legacyContext, $router, $featureDataProvider)
    {
        $this->translator = $translator;
        $this->locales = $legacyContext->getLanguages();
        $this->router = $router;
        $this->featureDataProvider = $featureDataProvider;
        $this->features = $this->formatDataChoicesList(
            $this->featureDataProvider->getFeatures($this->locales[0]['id_lang']),
            'id_feature'
        );
    }

    /**
     * {@inheritdoc}
     *
     * Builds form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('feature', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => $this->translator->trans('Feature', array(), 'Admin.Catalog.Feature'),
            'choices' =>  $this->features,
            'choices_as_values' => true,
            'required' =>  false,
            'attr' => array(
                'data-action' => $this->router->generate('admin_feature_get_feature_values', array('idFeature' => 1)),
                'data-toggle' => 'select2',
                'data-minimumResultsForSearch' => '7',
                'class' => 'feature-selector',
            ),
            'placeholder' => $this->translator->trans('Choose a feature', array(), 'Admin.Catalog.Feature'),
        ))
        ->add('value', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => $this->translator->trans('Pre-defined value', array(), 'Admin.Catalog.Feature'),
            'required' =>  false,
            'choices_as_values' => true,
            'attr' => array(
                'class' => 'feature-value-selector',
                'data-minimumResultsForSearch' => '7',
            ),
            'placeholder' => $this->translator->trans('Choose a value', array(), 'Admin.Catalog.Feature'),
            'disabled' => true,
        ))
        ->add('custom_value', 'PrestaShopBundle\Form\Admin\Type\TranslateType', array(
            'type' => 'Symfony\Component\Form\Extension\Core\Type\TextType',
            'options' => [],
            'locales' => $this->locales,
            'hideTabs' => true,
            'required' =>  false,
            'label' => $this->translator->trans('OR Customized value', array(), 'Admin.Catalog.Feature'),
        ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if (!$data || !$data['feature'] || $data['custom_value']) {
                return;
            }

            $choices = $this->formatDataChoicesList(
                $this->featureDataProvider->getFeatureValuesWithLang($this->locales[0]['id_lang'], $data['feature']),
                'id_feature_value',
                'value'
            );

            $this->updateValueField($form, $choices);

        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            if (empty($data['value'])) {
                return;
            }

            $choices = $this->formatDataChoicesList(
                $this->featureDataProvider->getFeatureValuesWithLang($this->locales[0]['id_lang'], $data['feature']),
                'id_feature_value',
                'value'
            );

            $this->updateValueField($form, $choices);
        });
    }

    private function updateValueField(Form $form, $choices)
    {
        $form->add('value', 'Symfony\Component\Form\Extension\Core\Type\ChoiceType', array(
            'label' => $this->translator->trans('Pre-defined value', array(), 'Admin.Catalog.Feature'),
            'required' =>  false,
            'attr' => array('class' => 'feature-value-selector'),
            'choices' => $choices,
            'choices_as_values' => true,
            'placeholder' => $this->translator->trans('Choose a value', array(), 'Admin.Catalog.Feature'),
        ));
    }

    /**
     * Returns the block prefix of this type.
     *
     * @return string The prefix name
     */
    public function getBlockPrefix()
    {
        return 'product_feature';
    }
}
