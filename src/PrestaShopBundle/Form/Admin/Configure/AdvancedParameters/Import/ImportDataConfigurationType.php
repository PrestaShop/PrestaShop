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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ImportDataConfigurationType is responsible for displaying the configuration of the
 * Advanced Parameters -> Import -> second step list.
 */
class ImportDataConfigurationType extends TranslatorAwareType
{
    /**
     * @var array choices for data matches
     */
    private $dataMatchChoices;

    /**
     * @var array choices for entity fields
     */
    private $entityFieldChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $dataMatchChoices
     * @param array $entityFieldChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $dataMatchChoices,
        array $entityFieldChoices
    ) {
        parent::__construct($translator, $locales);

        $this->dataMatchChoices = $dataMatchChoices;
        $this->entityFieldChoices = $entityFieldChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matches', ChoiceType::class, [
                'choices' => $this->dataMatchChoices,
                'choice_translation_domain' => false,
            ])
            ->add('match_name', TextType::class, [
                'required' => false,
            ])
            ->add('skip', IntegerType::class, [
                'data' => 1,
            ])
            ->add('type_value', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => [
                        $this->trans('Ignore this column', 'Admin.Advparameters.Feature') => 'no',
                    ] +
                    $this->entityFieldChoices,
                    'choice_translation_domain' => false,
                    'label' => false,
                ],
                'label' => false,
            ])
            ->add('entity', HiddenType::class)
            ->add('csv', HiddenType::class)
            ->add('iso_lang', HiddenType::class)
            ->add('truncate', HiddenType::class)
            ->add('match_ref', HiddenType::class)
            ->add('regenerate', HiddenType::class)
            ->add('forceIDs', HiddenType::class)
            ->add('sendemail', HiddenType::class)
            ->add('separator', HiddenType::class)
            ->add('multiple_value_separator', HiddenType::class);
    }
}
