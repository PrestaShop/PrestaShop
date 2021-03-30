<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ZoneType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var bool
     */
    private $isMultistoreEnabled;

    /**
     * ZoneType constructor.
     *
     * @param TranslatorInterface $translator
     * @param bool $isMultistoreEnabled
     */
    public function __construct(TranslatorInterface $translator, bool $isMultistoreEnabled)
    {
        $this->translator = $translator;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'help' => $this->translator->trans('Zone name (e.g. Africa, West Coast, Neighboring Countries).', [], 'Admin.International.Help'),
                'label' => $this->translator->trans('Name', [], 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => 64,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters.',
                            ['%limit%' => 64],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'generic_name',
                    ]),
                ],
            ])
            ->add('enabled', SwitchType::class, [
                'required' => false,
                'help' => $this->translator->trans('Allow or disallow shipping to this zone.', [], 'Admin.International.Help'),
                'label' => $this->translator->trans('Active', [], 'Admin.Global'),
            ]);

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty.', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'label' => $this->translator->trans('Shop association', [], 'Admin.Global'),
            ]);
        }
    }
}
