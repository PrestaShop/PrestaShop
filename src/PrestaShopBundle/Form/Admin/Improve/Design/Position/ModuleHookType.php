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

namespace PrestaShopBundle\Form\Admin\Improve\Design\Position;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Defines Improve > Design > Positions > Transplant | Edit module-hook form
 */
class ModuleHookType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $moduleNameByIdChoices;

    public function __construct(
        TranslatorInterface $translator,
        array $moduleNameByIdChoices
    ) {
        $this->translator = $translator;
        $this->moduleNameByIdChoices = $moduleNameByIdChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id_module', ChoiceType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans('Please select a module', [], 'Admin.Design.Help'),
                'choices' => $this->moduleNameByIdChoices,
            ])
            ->add('id_hook', ChoiceType::class, [
                'label' => false,
                'placeholder' => $this->translator->trans(
                    'Select a module above before choosing from available hooks', [], 'Admin.Design.Help'),
                'choices' => [],
            ])
            ->add('except_files', ChoiceType::class, [
                'choices' => [],
            ])
        ;
    }
}
