<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class AdvancedConfigurationType is responsible for building 'Improve > International > Localization' page
 * 'Advanced' form.
 */
class AdvancedConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('language_identifier', TextType::class, [
                'label' => $this->trans(
                    'Language identifier',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The ISO 639-1 identifier for the language of the country where your web server is located (en, fr, sp, ru, pl, nl, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('country_identifier', TextType::class, [
                'label' => $this->trans(
                    'Country identifier',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'The ISO 3166-1 alpha-2 identifier for the country/region where your web server is located, in lowercase (us, gb, fr, sp, ru, pl, nl, etc.).',
                    'Admin.International.Help'
                ),
            ])
            ->add('language_pack_url', TextType::class, [
                'label' => $this->trans(
                    'Language pack URL',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'Where translation packs are downloaded from. %version% is replaced by the shop version and %locale% by the language being installed.',
                    'Admin.International.Help'
                ),
                'constraints' => $this->getPackUrlConstraints(),
            ])
            ->add('emails_pack_url', TextType::class, [
                'label' => $this->trans(
                    'Email translations pack URL',
                    'Admin.International.Feature'
                ),
                'help' => $this->trans(
                    'Where email translation packs are downloaded from. The same %version% and %locale% placeholders apply.',
                    'Admin.International.Help'
                ),
                'constraints' => $this->getPackUrlConstraints(),
            ]);
    }

    /**
     * These values are URL templates rather than URLs, so the Url constraint cannot be used on them:
     * it reads %ve of %version% as a percent escape and rejects the shop's own default value.
     *
     * The %locale% placeholder is required because a URL without it downloads the same archive for
     * every language installed, and that failure is silent - the response is still a valid archive.
     *
     * @return array<int, mixed>
     */
    private function getPackUrlConstraints(): array
    {
        return [
            new NotBlank(),
            new Regex([
                'pattern' => '~^https?://~',
                'message' => $this->trans('The URL must start with http:// or https://.', 'Admin.International.Notification'),
            ]),
            new Regex([
                'pattern' => '~%locale%~',
                'message' => $this->trans('The URL must contain the %locale% placeholder.', 'Admin.International.Notification'),
            ]),
        ];
    }
}
