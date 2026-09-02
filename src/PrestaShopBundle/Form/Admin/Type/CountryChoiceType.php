<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CountryChoiceType is responsible for providing country choices with -- symbol in front of array.
 */
class CountryChoiceType extends AbstractType
{
    private array $countriesAttr = [];
    private bool $needDni = false;
    private bool $needPostcode = false;

    private bool $needLogo = false;

    public function __construct(
        private readonly FormChoiceProviderInterface&FormChoiceAttributeProviderInterface $countriesChoiceProvider,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['with_dni_attr'] || $options['with_postcode_attr'] || $options['with_logo_attr']) {
            $this->needDni = $options['with_dni_attr'];
            $this->needPostcode = $options['with_postcode_attr'];
            $this->needLogo = $options['with_logo_attr'];
            $this->countriesAttr = $this->countriesChoiceProvider->getChoicesAttributes();
        }
        parent::buildForm($builder, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => [],
            'choice_attr' => [$this, 'getChoiceAttr'],
            'placeholder' => '--',
            'add_all_countries_option' => false,
            'with_dni_attr' => false,
            'with_postcode_attr' => false,
            'with_logo_attr' => false,
        ]);

        $resolver->addNormalizer('choices', function (Options $options) {
            $countries = $this->countriesChoiceProvider->getChoices();

            if ($options['add_all_countries_option']) {
                return array_merge(
                    [$this->translator->trans('All countries', [], 'Admin.Global') => 0],
                    $countries
                );
            }

            return $countries;
        });

        $resolver
            ->setAllowedTypes('with_dni_attr', 'boolean')
            ->setAllowedTypes('with_postcode_attr', 'boolean')
            ->setAllowedTypes('with_logo_attr', 'boolean');
    }

    public function getChoiceAttr($value, $key)
    {
        $attr = [];
        if ($this->needDni && isset($this->countriesAttr[$key], $this->countriesAttr[$key]['need_dni'])) {
            $attr['need_dni'] = 1;
        }
        if ($this->needPostcode && isset($this->countriesAttr[$key], $this->countriesAttr[$key]['need_postcode'])) {
            $attr['need_postcode'] = 1;
        }
        if ($this->needLogo && isset($this->countriesAttr[$key], $this->countriesAttr[$key]['data-logo'])) {
            $attr['data-logo'] = $this->countriesAttr[$key]['data-logo'];
        }

        return $attr;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
