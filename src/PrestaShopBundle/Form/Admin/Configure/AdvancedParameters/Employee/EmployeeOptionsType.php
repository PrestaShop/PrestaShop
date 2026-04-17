<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
        $optionsLock = [];
        if (!$this->canOptionsBeChanged) {
            $optionsLock = [
                'disabled' => true,
                'alert_type' => 'warning',
                'alert_message' => $this->trans(
                    'You can\'t change the value of this configuration field in this store\'s context.',
                    'Admin.Notifications.Warning'
                ),
                'block_prefix' => 'employee_options',
                'form_theme' => '@PrestaShop/Admin/Configure/AdvancedParameters/Employee/FormTheme/employee_options.html.twig',
            ];
        }

        $builder
            ->add('password_change_time', IntegerType::class, [
                'label' => $this->trans('Password regeneration', 'Admin.Advparameters.Feature'),
                'required' => false,
                'unit' => $this->trans('minutes', 'Admin.Advparameters.Feature'),
                'help' => $this->trans(
                    'Security: Minimum time to wait between two password changes.',
                    'Admin.Advparameters.Feature'
                ),
            ] + $optionsLock)
            ->add('allow_employee_specific_language', SwitchType::class, [
                'label' => $this->trans(
                    'Memorize the language used in Admin panel forms',
                    'Admin.Advparameters.Feature'
                ),
                'required' => false,
                'help' => $this->trans(
                    'Allow employees to select a specific language for the Admin panel form.',
                    'Admin.Advparameters.Feature'
                ),
            ] + $optionsLock);
    }
}
