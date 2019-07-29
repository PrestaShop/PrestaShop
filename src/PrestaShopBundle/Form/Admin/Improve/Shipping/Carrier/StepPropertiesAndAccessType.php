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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/**
 * Defines form part for create/edit carrier Properties and group access step
 */
class StepPropertiesAndAccessType extends AbstractType
{
    /**
     * @var array
     */
    private $customerGroupChoices;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param array $customerGroupChoices
     * @param TranslatorInterface $translator
     */
    public function __construct(array $customerGroupChoices, TranslatorInterface $translator)
    {
        $this->customerGroupChoices = $customerGroupChoices;
        $this->translator = $translator;
    }

    /**
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $gtoConstraint = new GreaterThanOrEqual([
            'value' => 0,
            'message' => $this->translator->trans(
                'Value cannot be less than %value%.',
                ['%value%' => 0],
                'Admin.Notifications.Error'
            ),
        ]);

        $builder
            ->add('max_width', NumberType::class, [
                'required' => false,
                'constraints' => [$gtoConstraint],
            ])
            ->add('max_height', NumberType::class, [
                'required' => false,
                'constraints' => [$gtoConstraint],
            ])
            ->add('max_depth', NumberType::class, [
                'required' => false,
                'constraints' => [$gtoConstraint],
            ])
            ->add('max_weight', NumberType::class, [
                'required' => false,
                'constraints' => [$gtoConstraint],
            ])
            ->add('group_association', MaterialChoiceTableType::class, [
                'choices' => $this->customerGroupChoices,
            ])
        ;
    }
}
