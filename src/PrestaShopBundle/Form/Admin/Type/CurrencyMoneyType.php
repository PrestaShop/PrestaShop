<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CurrencyByIdChoiceProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Combines a money input with a currency selector in a single input-group.
 */
class CurrencyMoneyType extends AbstractType
{
    public function __construct(
        private readonly CurrencyDataProviderInterface $currencyDataProvider,
        private readonly CurrencyByIdChoiceProvider $currencyByIdChoiceProvider,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', MoneyType::class, [
                'scale' => $options['scale'],
                'currency' => $this->currencyDataProvider->getDefaultCurrencyIsoCode(),
                'default_empty_data' => 0,
            ])
            ->add('currency', ChoiceType::class, [
                'choices' => $this->buildCurrencySymbolChoices(),
                'choice_translation_domain' => false,
                'placeholder' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'scale' => 6,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/currency_money.html.twig',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'currency_money';
    }

    /**
     * Build choices using currency symbols as labels and currency IDs as values.
     */
    private function buildCurrencySymbolChoices(): array
    {
        $choices = [];
        $choicesAttributes = $this->currencyByIdChoiceProvider->getChoicesAttributes();

        foreach ($this->currencyByIdChoiceProvider->getChoices() as $label => $id) {
            $symbol = $choicesAttributes[$label]['symbol'] ?? $label;
            $choices[$symbol] = $id;
        }

        return $choices;
    }
}
