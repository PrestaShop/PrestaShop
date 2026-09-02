<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\TaxRule\TaxRuleSettings;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for creating/editing a tax rule within a tax rules group
 */
class TaxRuleType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FormChoiceProviderInterface $taxByIdChoiceProvider,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tax_rules_group_id', HiddenType::class)
            ->add('country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'required' => true,
                'add_all_countries_option' => true,
            ])
            ->add('state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => false,
                'choices' => [],
                'multiple' => false,
                'placeholder' => $this->trans('All', 'Admin.Global'),
            ])
            ->add('zipcode', TextType::class, [
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'required' => false,
                'help' => $this->trans('You can define a range of Zip/Postal codes (e.g., 44000-44100) or simply use one Zip/Postal code.', 'Admin.International.Help'),
                'attr' => [
                    'placeholder' => $this->trans('e.g. 44000 or 44000-44100', 'Admin.International.Help'),
                ],
            ])
            ->add('behavior', ChoiceType::class, [
                'label' => $this->trans('Behavior', 'Admin.Global'),
                'required' => true,
                'choices' => [
                    $this->trans('This tax only', 'Admin.International.Feature') => TaxRuleSettings::BEHAVIOR_TAX_ONLY,
                    $this->trans('Combine', 'Admin.International.Feature') => TaxRuleSettings::BEHAVIOR_COMBINE,
                    $this->trans('One after another', 'Admin.International.Feature') => TaxRuleSettings::BEHAVIOR_ONE_AFTER_ANOTHER,
                ],
            ])
            ->add('tax', ChoiceType::class, [
                'label' => $this->trans('Tax', 'Admin.Global'),
                'required' => true,
                'choices' => array_merge(
                    [$this->trans('No Tax', 'Admin.International.Notification') => 0],
                    $this->taxByIdChoiceProvider->getChoices()
                ),
            ])
            ->add('description', TextType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 100,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            ['%limit%' => 100]
                        ),
                    ]),
                ],
            ])
        ;

        $rebuildStateChoices = function (FormEvent $event): void {
            $data = $event->getData();

            // POST_SET_DATA: data is an array (from DataProvider), PRE_SUBMIT: data is raw form data
            $countryId = (int) ($data['country'] ?? 0);

            if ($countryId === 0) {
                return;
            }

            $states = $this->getStatesForCountry($countryId);

            if (empty($states)) {
                return;
            }

            $event->getForm()->add('state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => false,
                'choices' => $states,
                'multiple' => false,
                'placeholder' => $this->trans('All', 'Admin.Global'),
            ]);
        };

        // Load state choices on initial form render (edit mode with existing data)
        $builder->addEventListener(FormEvents::POST_SET_DATA, $rebuildStateChoices);

        // Rebuild state choices on submit so Symfony's ChoiceType validation accepts the dynamically loaded value
        $builder->addEventListener(FormEvents::PRE_SUBMIT, $rebuildStateChoices);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'is_edit' => false,
            'constraints' => [
                new Callback([
                    'callback' => [$this, 'validateZipCode'],
                ]),
            ],
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }

    /**
     * Validates the zip code against the selected country's format.
     *
     * @param array $data form data
     * @param ExecutionContextInterface $context
     */
    public function validateZipCode(array $data, ExecutionContextInterface $context): void
    {
        $zipcode = $data['zipcode'] ?? null;

        if (empty($zipcode)) {
            return;
        }

        $countryId = (int) ($data['country'] ?? 0);

        // "All countries" selected — skip per-country validation
        if ($countryId === 0) {
            return;
        }

        $country = $this->connection->createQueryBuilder()
            ->select('c.need_zip_code', 'c.zip_code_format', 'c.iso_code', 'cl.name')
            ->from($this->dbPrefix . 'country', 'c')
            ->leftJoin('c', $this->dbPrefix . 'country_lang', 'cl', 'c.id_country = cl.id_country AND cl.id_lang = 1')
            ->where('c.id_country = :countryId')
            ->setParameter('countryId', $countryId)
            ->executeQuery()
            ->fetchAssociative();

        if (!$country || !$country['need_zip_code'] || empty($country['zip_code_format'])) {
            return;
        }

        // Parse range: "75000-75015" → ["75000", "75015"]
        $codes = preg_split('/-/', $zipcode);

        foreach ($codes as $code) {
            $code = trim($code);

            if ($code === '') {
                continue;
            }

            if (!$this->matchesZipFormat($code, $country['zip_code_format'], $country['iso_code'])) {
                $context->buildViolation(
                    'The Zip/Postal code is invalid. It must be typed as follows: %format% for %country%.'
                )
                    ->atPath('zipcode')
                    ->setParameter('%format%', $this->humanReadableFormat($country['zip_code_format'], $country['iso_code']))
                    ->setParameter('%country%', $country['name'] ?? '')
                    ->addViolation();

                return;
            }
        }
    }

    /**
     * Checks if a zip code matches the country's format pattern.
     */
    private function matchesZipFormat(string $zipCode, string $format, string $isoCode): bool
    {
        $zipRegexp = '/^' . $format . '$/ui';
        $zipRegexp = str_replace('N', '[0-9]', $zipRegexp);
        $zipRegexp = str_replace('L', '[a-zA-Z]', $zipRegexp);
        $zipRegexp = str_replace('C', $isoCode, $zipRegexp);

        return (bool) preg_match($zipRegexp, $zipCode);
    }

    /**
     * Converts zip format pattern to human-readable example (N→0, L→A, C→ISO).
     */
    private function humanReadableFormat(string $format, string $isoCode): string
    {
        return str_replace(
            ['N', 'L', 'C'],
            ['0', 'A', $isoCode],
            $format
        );
    }

    /**
     * Returns states for a country as [name => id] for ChoiceType.
     *
     * @param int $countryId
     *
     * @return array<string, int>
     */
    private function getStatesForCountry(int $countryId): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select('s.id_state', 's.name')
            ->from($this->dbPrefix . 'state', 's')
            ->where('s.id_country = :countryId')
            ->andWhere('s.active = 1')
            ->setParameter('countryId', $countryId)
            ->orderBy('s.name', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        $choices = [];
        foreach ($rows as $row) {
            $choices[$row['name']] = (int) $row['id_state'];
        }

        return $choices;
    }
}
