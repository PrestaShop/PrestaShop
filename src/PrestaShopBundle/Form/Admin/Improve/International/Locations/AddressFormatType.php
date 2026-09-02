<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\ValidAddressFormat;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatFieldsProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Custom form type backing the country address-format Vue 3 visual builder.
 *
 * The submitted name remains the parent's field name (i.e. country[address_format])
 * because this type binds directly to a string — the Vue component owns a hidden
 * input with that name and keeps it in sync with the visual editor state.
 *
 * Server-side options resolved here (available_objects, required_fields,
 * default_format, sample_data, required_fields_url, translations) are serialized
 * as data-* attributes by the address_format_builder_widget block.
 */
class AddressFormatType extends AbstractType
{
    /**
     * Built-in PrestaShop default layout — used when no per-country format is set
     * and as the source of the "Default for this country" reset action.
     *
     * Mirrors the legacy AdminCountriesController's hard-coded default layout.
     */
    public const DEFAULT_LAYOUT = "firstname lastname\ncompany\nvat_number\naddress1\naddress2\npostcode city\nCountry:name\nphone";

    /**
     * Form theme path — applied via the form_theme option (see PrestaShopBundle's
     * FormThemeExtension) so the widget block stays scoped to this type instead of
     * being registered globally in app/config/config.yml.
     */
    private const FORM_THEME = '@PrestaShop/Admin/Improve/International/Country/FormTheme/address_format_builder.html.twig';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RouterInterface $router,
        private readonly AddressFormatFieldsProviderInterface $fieldsProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('address_format_options', [
            'available_objects' => $options['available_objects'],
            'required_fields' => $options['required_fields'],
            'default_format' => $options['default_format'],
            'sample_data' => $options['sample_data'],
            'required_fields_url' => $options['required_fields_url'],
            'translations' => $options['translations'],
        ]);
    }

    public function buildView(FormView $view, \Symfony\Component\Form\FormInterface $form, array $options): void
    {
        $view->vars['address_format_options'] = $form->getConfig()->getAttribute('address_format_options');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                // HiddenType bubbles errors to the parent form by default; this field
                // has a visible UI surface (the Vue builder), so violations must render
                // inline on the field itself.
                'error_bubbling' => false,
                'empty_data' => '',
                // The Vue mount script (initAddressFormatBuilder.ts) locates the hidden
                // input by the `js-address-format-input` selector to keep it in sync with
                // the visual editor — propagated to the input via the form theme's
                // {{ block('hidden_widget') }} call.
                'attr' => ['class' => 'js-address-format-input'],
                'form_theme' => self::FORM_THEME,
                'available_objects' => null,
                'required_fields' => null,
                'default_format' => self::DEFAULT_LAYOUT,
                'sample_data' => null,
                'required_fields_url' => null,
                'translations' => null,
                'constraints' => [
                    new NotBlank(),
                    new ValidAddressFormat(),
                ],
            ])
            ->setNormalizer('available_objects', fn ($options, $value) => $value ?? $this->buildAvailableObjects())
            ->setNormalizer('required_fields', fn ($options, $value) => $value ?? $this->fetchRequiredFields())
            ->setNormalizer('sample_data', fn ($options, $value) => $value ?? $this->buildSampleData())
            ->setNormalizer('required_fields_url', fn ($options, $value) => $value ?? $this->buildRequiredFieldsUrl())
            ->setNormalizer('translations', fn ($options, $value) => $value ?? $this->buildTranslations())
            ->setAllowedTypes('available_objects', ['array', 'null'])
            ->setAllowedTypes('required_fields', ['array', 'null'])
            ->setAllowedTypes('default_format', 'string')
            ->setAllowedTypes('sample_data', ['array', 'null'])
            ->setAllowedTypes('required_fields_url', ['string', 'null'])
            ->setAllowedTypes('translations', ['array', 'null']);
    }

    public function getBlockPrefix(): string
    {
        return 'address_format_builder';
    }

    public function getParent(): string
    {
        return HiddenType::class;
    }

    /**
     * @return array<string, list<string>>
     */
    private function buildAvailableObjects(): array
    {
        $objects = [];
        foreach ($this->fieldsProvider->getPickerClasses() as $className) {
            $objects[$className] = $this->fieldsProvider->getFieldsForClass($className);
        }

        return $objects;
    }

    /**
     * @return list<string>
     */
    private function fetchRequiredFields(): array
    {
        return $this->fieldsProvider->getRequiredFields();
    }

    /**
     * Fixed sample customer used by the live preview pane (John Doe / 16 Main street / Paris).
     *
     * @return array<string, array<string, string>>
     */
    private function buildSampleData(): array
    {
        return [
            'Customer' => [
                'firstname' => 'John',
                'lastname' => 'DOE',
                'company' => 'Acme Ltd.',
                'email' => 'john@example.com',
                'website' => 'acme.com',
                'vat_number' => 'FR12345678901',
                'siret' => '123 456 789 12345',
                'birthday' => '1985-04-12',
            ],
            'Warehouse' => [
                'reference' => 'WH-01',
                'name' => 'Main warehouse',
                'management_type' => 'FIFO',
            ],
            'Country' => ['name' => 'France', 'iso_code' => 'FR', 'call_prefix' => '33'],
            'State' => ['name' => 'Île de France', 'iso_code' => 'FR-IDF'],
            'Address' => [
                // Address has firstname/lastname/company/vat_number as public properties
                // (the legacy Address ObjectModel exposes them) — bare tokens like
                // `firstname` resolve to Address:firstname, so the preview reads them here.
                'firstname' => 'John',
                'lastname' => 'DOE',
                'company' => 'Acme Ltd.',
                'vat_number' => 'FR12345678901',
                'address1' => '16 Main street',
                'address2' => '2nd floor',
                'postcode' => '75002',
                'city' => 'Paris',
                'phone' => '0102030405',
                'phone_mobile' => '+33 6 12 34 56 78',
                'dni' => '12345678A',
                'other' => '3rd floor',
            ],
        ];
    }

    private function buildRequiredFieldsUrl(): string
    {
        return $this->router->generate('admin_addresses_index', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            . '#addressRequiredFieldsContainer';
    }

    /**
     * @return array<string, string>
     */
    private function buildTranslations(): array
    {
        return [
            'mode.visual' => $this->translator->trans('Visual editor', [], 'Admin.International.Feature'),
            'mode.raw' => $this->translator->trans('Raw template', [], 'Admin.International.Feature'),
            'reset.button' => $this->translator->trans('Reset', [], 'Admin.Actions'),
            'reset.default' => $this->translator->trans('Default for this country', [], 'Admin.International.Feature'),
            'reset.lastSaved' => $this->translator->trans('Last saved format', [], 'Admin.International.Feature'),
            'reset.clear' => $this->translator->trans('Clear format', [], 'Admin.International.Feature'),
            'reset.confirm' => $this->translator->trans('Are you sure you want to clear this address format?', [], 'Admin.International.Notification'),
            'banner.missingOne' => $this->translator->trans('1 required field missing:', [], 'Admin.International.Feature'),
            'banner.missingMany' => $this->translator->trans('{count} required fields missing:', [], 'Admin.International.Feature'),
            'banner.allPresent' => $this->translator->trans('All required fields are present.', [], 'Admin.International.Feature'),
            'banner.insertAll' => $this->translator->trans('Insert all missing', [], 'Admin.International.Feature'),
            'lines.empty' => $this->translator->trans('Drop fields here, or click + below', [], 'Admin.International.Feature'),
            'lines.add' => $this->translator->trans('Add line', [], 'Admin.International.Feature'),
            'lines.remove' => $this->translator->trans('Remove line', [], 'Admin.International.Feature'),
            'lines.removeChip' => $this->translator->trans('Remove field', [], 'Admin.International.Feature'),
            'lines.dragHint' => $this->translator->trans('Drag to reorder line', [], 'Admin.International.Feature'),
            'picker.title' => $this->translator->trans('Available fields', [], 'Admin.International.Feature'),
            'picker.search' => $this->translator->trans('Search across all objects…', [], 'Admin.International.Feature'),
            'picker.noMatch' => $this->translator->trans('No fields match your search.', [], 'Admin.International.Feature'),
            'picker.required' => $this->translator->trans('Required field', [], 'Admin.International.Feature'),
            'picker.alreadyAdded' => $this->translator->trans('Already in the format', [], 'Admin.International.Feature'),
            'picker.requiredFooter' => $this->translator->trans('Required fields — managed in [link]Customers › Addresses[/link]', [], 'Admin.International.Feature'),
            'preview.title' => $this->translator->trans('Live preview · Sample customer', [], 'Admin.International.Feature'),
            'preview.empty' => $this->translator->trans('Empty format — drag fields in to see the preview.', [], 'Admin.International.Feature'),
            'raw.help' => $this->translator->trans('Edit the raw template directly. Each line maps to one address line; spaces join placeholders.', [], 'Admin.International.Feature'),
        ];
    }
}
