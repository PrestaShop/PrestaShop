<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ]);
    }
}
