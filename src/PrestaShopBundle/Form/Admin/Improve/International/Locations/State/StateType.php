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

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations\State;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\UniqueStateIsoCode;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShopBundle\Form\Admin\Type\ConfigurableCountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\ZoneChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for state create/edit actions (Improve > International > Locations > State)
 */
class StateType extends AbstractType
{
    /** @var int maximum number of characters for name */
    public const MAX_NAME_LENGTH = 32;

    /** @var int maximum number of characters for iso code */
    public const MAX_ISO_CODE_LENGTH = 7;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $stateIdValue = isset($builder->getData()['id_state']) && $builder->getData()['id_state'] instanceof StateId ?
            $builder->getData()['id_state']->getValue() :
            null;

        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new TypedRegex([
                        'type' => 'generic_name',
                    ]),
                    new Length([
                        'max' => self::MAX_NAME_LENGTH,
                    ]),
                    new CleanHtml(),
                ],
            ])
            ->add('iso_code', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => self::MAX_ISO_CODE_LENGTH,
                    ]),
                    new TypedRegex([
                        'type' => 'state_iso_code',
                    ]),
                    new CleanHtml(),
                    new UniqueStateIsoCode([
                        'excludeId' => $stateIdValue
                    ]),
                ],
            ])
            ->add('id_country', ConfigurableCountryChoiceType::class, [
                'required' => true,
                'contains_states' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('id_zone', ZoneChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('active', SwitchType::class, [
                'required' => true,
            ]);
    }
}
