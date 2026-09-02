<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencyChoiceType extends AbstractType
{
    /**
     * @var CurrencyByIdChoiceProvider
     */
    private $currencyByIdChoiceProvider;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        CurrencyDataProviderInterface $currencyDataProvider,
        CurrencyByIdChoiceProvider $currencyByIdChoiceProvider
    ) {
        $this->currencyByIdChoiceProvider = $currencyByIdChoiceProvider;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->addNormalizer('choices', function (Options $options) {
            $currencies = $this->currencyByIdChoiceProvider->getChoices();

            if ($options['add_all_currencies_option']) {
                return array_merge(
                    ['All currencies' => 0],
                    $currencies
                );
            }

            return $currencies;
        });

        $resolver->addNormalizer('attr', function (Options $options, array $attr) {
            $attr['data-default-currency-symbol'] = $this->currencyDataProvider->getDefaultCurrencySymbol();
            $attr['data-minimumResultsForSearch'] = '7';
            $attr['data-toggle'] = 'select2';

            return $attr;
        });

        $resolver->setDefaults([
            'required' => false,
            'add_all_currencies_option' => false,
            'choice_translation_domain' => false,
            'choices' => [],
            'choice_attr' => $this->currencyByIdChoiceProvider->getChoicesAttributes(),
            'label' => 'Currency',
            'placeholder' => false,
        ]);
    }
}
