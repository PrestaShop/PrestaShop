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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\FirstName;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\LastName;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

final class EmployeeType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => FirstName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => FirstName::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans('This field cannot be empty', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        'max' => LastName::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => LastName::MAX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
        ;
    }
}
