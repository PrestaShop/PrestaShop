<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeOrderCurrencyType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $currencyChoiceProvider;

    /**
     * @param FormChoiceProviderInterface $currencyChoiceProvider
     */
    public function __construct(FormChoiceProviderInterface $currencyChoiceProvider)
    {
        $this->currencyChoiceProvider = $currencyChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_currency_id', ChoiceType::class, [
                'choices' => $this->getCurrencyChoices($options['current_currency_id']),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'current_currency_id' => null,
            ])
            ->setAllowedTypes('current_currency_id', ['int', 'null'])
        ;
    }

    /**
     * @param int|null $currentCurrencyId
     *
     * @return array
     */
    private function getCurrencyChoices(?int $currentCurrencyId): array
    {
        $choices = $this->currencyChoiceProvider->getChoices();

        if (null === $currentCurrencyId) {
            return $choices;
        }

        $currentCurrencyKey = array_search($currentCurrencyId, $choices, true);

        if ($currentCurrencyKey) {
            unset($choices[$currentCurrencyKey]);
        }

        return $choices;
    }
}
