<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextWithUnitType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EmployeeOptionsType defines form for employee options.
 */
class EmployeeOptionsType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $canOptionsBeChanged;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $canOptionsBeChanged
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $canOptionsBeChanged
    ) {
        parent::__construct($translator, $locales);

        $this->canOptionsBeChanged = $canOptionsBeChanged;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password_change_time', TextWithUnitType::class, [
                'required' => false,
                'unit' => $this->trans('minutes', 'Admin.Advparameters.Feature'),
                'disabled' => !$this->canOptionsBeChanged,
            ])
            ->add('allow_employee_specific_language', SwitchType::class, [
                'required' => false,
                'disabled' => !$this->canOptionsBeChanged,
            ]);
    }
}
