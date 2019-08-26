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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for address add/edit
 */
class CustomerAddressType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $currencyChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $currencyChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $currencyChoices
    ) {
        $this->translator = $translator;
        $this->currencyChoices = $currencyChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('customer_email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('dni', TextType::class, [
                'required' => false,
            ])
            ->add('alias', TextType::class, [
                'required' => true,
            ])
            ->add('firstname', TextType::class, [
                'required' => true,
            ])
            ->add('lastname', TextType::class, [
                'required' => true,
            ])
            ->add('company', TextType::class, [
                'required' => false,
            ])
            ->add('vat_number', TextType::class, [
                'required' => false,
            ])
            ->add('address1', TextType::class, [
                'required' => true,
            ])
            ->add('address2', TextType::class, [
                'required' => false,
            ])
            ->add('city', TextType::class, [
                'required' => true,
            ])
            ->add('postcode', TextType::class, [
                'required' => true,
            ])
            ->add('id_country', CountryChoiceType::class, [
                'required' => true,
            ])
            ->add('id_state', CountryChoiceType::class, [
                'required' => false,
            ])
            ->add('phone_mobile', TextType::class, [
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'required' => false,
            ])
            ->add('other', TextareaType::class, [
                'required' => false,
            ]);

    }
}
