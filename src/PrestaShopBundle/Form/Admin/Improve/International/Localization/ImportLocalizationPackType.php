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

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImportConfigInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ImportLocalizationPackType is responsible for building 'Import a localization pack' form
 * in 'Improve > International > Localization'.
 */
class ImportLocalizationPackType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $localizationPackChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param array $localizationPackChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $localizationPackChoices
    ) {
        parent::__construct($translator, $locales);

        $this->localizationPackChoices = $localizationPackChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso_localization_pack', ChoiceType::class, [
                'choices' => $this->localizationPackChoices,
                'choice_translation_domain' => false,
            ])
            ->add('content_to_import', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => $this->getContentToImportChoices(),
                'data' => [
                    LocalizationPackImportConfigInterface::CONTENT_STATES,
                    LocalizationPackImportConfigInterface::CONTENT_TAXES,
                    LocalizationPackImportConfigInterface::CONTENT_CURRENCIES,
                    LocalizationPackImportConfigInterface::CONTENT_LANGUAGES,
                    LocalizationPackImportConfigInterface::CONTENT_UNITS,
                ],
                'choice_translation_domain' => false,
            ])
            ->add('download_pack_data', SwitchType::class, [
                'data' => 1,
            ]);
    }

    /**
     * Get import content choices.
     *
     * @return array
     */
    private function getContentToImportChoices()
    {
        return [
            $this->trans('States', 'Admin.International.Feature') => LocalizationPackImportConfigInterface::CONTENT_STATES,
            $this->trans('Taxes', 'Admin.Global') => LocalizationPackImportConfigInterface::CONTENT_TAXES,
            $this->trans('Currencies', 'Admin.Global') => LocalizationPackImportConfigInterface::CONTENT_CURRENCIES,
            $this->trans('Languages', 'Admin.Global') => LocalizationPackImportConfigInterface::CONTENT_LANGUAGES,
            $this->trans('Units (e.g. weight, volume, distance)', 'Admin.International.Feature') => LocalizationPackImportConfigInterface::CONTENT_UNITS,
            $this->trans('Change the behavior of the price display for groups', 'Admin.International.Feature') => LocalizationPackImportConfigInterface::CONTENT_GROUPS,
        ];
    }
}
