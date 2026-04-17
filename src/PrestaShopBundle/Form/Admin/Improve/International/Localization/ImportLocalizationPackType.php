<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Core\Localization\Pack\Import\LocalizationPackImportConfigInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

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
                'label' => $this->trans(
                    'Localization pack you want to import',
                    'Admin.International.Feature'
                ),
                'choices' => $this->localizationPackChoices,
                'choice_translation_domain' => false,
                'autocomplete' => true,
            ])
            ->add('content_to_import', ChoiceType::class, [
                'label' => $this->trans(
                    'Content to import',
                    'Admin.International.Feature'
                ),
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
                'label' => $this->trans(
                    'Download pack data',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'If set to yes then the localization pack will be downloaded from prestashop.com. Otherwise the local xml file found in the localization folder of your PrestaShop installation will be used.',
                    'Admin.International.Help'
                ),
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
