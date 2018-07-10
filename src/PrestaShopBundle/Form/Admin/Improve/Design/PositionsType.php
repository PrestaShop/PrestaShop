<?php
/**
 * 2007-2018 PrestaShop
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
namespace PrestaShopBundle\Form\Admin\Improve\Design;

use Dispatcher;
use Symfony\Component\Form\Extension\Core\Type as FormType;
use Symfony\Component\Form\FormBuilderInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;

/**
 * This form class generates the "Positions" form in
 * "Improve > Design > Positions" page
 */
class PositionsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'module',
                FormType\ChoiceType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'transplant_to',
                FormType\ChoiceType::class,
                [
                    'required' => false,
                    'choices' => $this->getTransplantTo(),
                ]
            )
            ->add(
                'exceptions_text',
                FormType\TextType::class,
                [
                    'empty_data' => '',
                    'required' => true,
                    'attr' => [
                        'placeholder' => $this->trans('E.g. address, addresses, attachment', 'Admin.Design.Help'),
                    ]
                ]
            )
            ->add(
                'exceptions_list',
                FormType\ChoiceType::class,
                [
                    'expanded' => false,
                    'multiple' => true,
                    'required' => true,
                    'placeholder' => false,
                    'attr' => [
                        'size' => 25,
                    ],
                    'choices' => $this->getExceptionsData(),
                    'choice_attr' => function ($value, $key, $index) {
                        return $value === null ? ['disabled' => 'disabled'] : [];
                    },
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'improve_design_positions';
    }

    protected function getTransplantTo()
    {
        $hooks = array();
        $moduleInstance = Module::getInstanceById((int)Tools::getValue('id_module'));
        $hooks = $moduleInstance->getPossibleHooksList();
    }

    protected function getExceptionsData()
    {
        $data = [
            $this->trans('___________ CUSTOM ___________', 'Admin.Design.Feature') => null
        ];

        $controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
        ksort($controllers);

        $data[$this->trans('____________ CORE ____________', 'Admin.Design.Feature')] = null;
        foreach ($controllers as $k => $v) {
            $data[$k] = false;
        }

        $modulesControllersType = [
            'admin' => $this->trans('Admin modules controller', 'Admin.Design.Feature'),
            'front' => $this->trans('Front modules controller', 'Admin.Design.Feature')
        ];

        foreach ($modulesControllersType as $type => $label) {
            $data[$label] = null;
            $allModulesControllers = Dispatcher::getModuleControllers($type);
            foreach ($allModulesControllers as $module => $modulesControllers) {
                foreach ($modulesControllers as $controller) {
                    $data[sprintf('module-%s-%s', $module, $controller)] = false;
                }
            }
        }

        return $data;
    }
}
