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
                    'empty_data' => '',
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

    protected function formatExceptionsData($fileList, $shopId)
    {
        if (!is_array($fileList)) {
            $fileList = ($fileList) ? array($fileList) : [];
        }

        if ($shopId) {
            $shop = new Shop($shopId);
            $content .= ' ('.$shop->name.')';
        }

        $data = [
            $this->trans('___________ CUSTOM ___________', [], 'Admin.Design.Feature') => 0
        ];

        // @todo do something better with controllers
        $controllers = Dispatcher::getControllers(_PS_FRONT_CONTROLLER_DIR_);
        ksort($controllers);

        foreach ($fileList as $k => $v) {
            if (! array_key_exists($v, $controllers)) {
                $content .= '<option value="'.$v.'">'.$v.'</option>';
            }
        }

        $content .= '<option disabled="disabled">'.$this->trans('____________ CORE ____________', [], 'Admin.Design.Feature').'</option>';

        foreach ($controllers as $k => $v) {
            $content .= '<option value="'.$k.'">'.$k.'</option>';
        }

        $modules_controllers_type = array(
            'admin' => $this->trans('Admin modules controller', [], 'Admin.Design.Feature'),
            'front' => $this->trans('Front modules controller', [], 'Admin.Design.Feature')
        );
        foreach ($modules_controllers_type as $type => $label) {
            $content .= '<option disabled="disabled">____________ '.$label.' ____________</option>';
            $all_modules_controllers = Dispatcher::getModuleControllers($type);
            foreach ($all_modules_controllers as $module => $modules_controllers) {
                foreach ($modules_controllers as $cont) {
                    $content .= '<option value="module-'.$module.'-'.$cont.'">module-'.$module.'-'.$cont.'</option>';
                }
            }
        }

        return $content;
    }
}
