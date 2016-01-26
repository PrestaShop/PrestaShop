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
namespace PrestaShopBundle\Form\Admin\Feature;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type as FormType;

/**
 * This form class is responsible to generate the product options form
 */
class ProductFeature extends CommonAbstractType
{
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
        $builder->add('feature', FormType\ChoiceType::class, array(
            'label' => $this->translator->trans('Feature', [], 'AdminProducts'),
            'choices' =>  $this->features,
            'required' =>  false,
            'attr' => array(
                'data-action' => $this->router->generate('admin_feature_get_feature_values'),
                'class' => 'feature-selector',
            )
        ))
        ->add('value', FormType\ChoiceType::class, array(
            'label' => $this->translator->trans('Value', [], 'AdminProducts'),
            'required' =>  false,
            'attr' => array('class' => 'feature-value-selector')
        ))
        ->add('custom_value', \PrestaShopBundle\Form\Admin\Type\TranslateType::class, array(
            'type' => FormType\TextType::class,
            'options' => [],
            'locales' => $this->locales,
            'hideTabs' => true,
            'required' =>  false,
            'label' => $this->translator->trans('Custom value', [], 'AdminProducts'),
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

            $form->add('value', FormType\ChoiceType::class, array(
                'label' => $this->translator->trans('Value', [], 'AdminProducts'),
                'choices' => $choices,
                'required' =>  false,
                'attr' => array('class' => 'feature-value-selector'),
            ));

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

            $form->add('value', FormType\ChoiceType::class, array(
                'label' => $this->translator->trans('Value', [], 'AdminProducts'),
                'required' =>  false,
                'attr' => array('class' => 'feature-value-selector'),
                'choices' => $choices,
            ));
        });
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
