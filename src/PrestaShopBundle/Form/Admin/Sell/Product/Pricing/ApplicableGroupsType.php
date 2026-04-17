<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\CurrencyChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApplicableGroupsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    protected $groupByIdChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    protected $shopByIdChoiceProvider;

    /**
     * @var bool
     */
    protected $isMultiShopEnabled;

    /**
     * @var int
     */
    protected $contextShopId;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $groupByIdChoiceProvider,
        FormChoiceProviderInterface $shopByIdChoiceProvider,
        bool $isMultiShopEnabled,
        int $contextShopId
    ) {
        parent::__construct($translator, $locales);
        $this->groupByIdChoiceProvider = $groupByIdChoiceProvider;
        $this->shopByIdChoiceProvider = $shopByIdChoiceProvider;
        $this->isMultiShopEnabled = $isMultiShopEnabled;
        $this->contextShopId = $contextShopId;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $groups = array_merge([
            $this->trans('All groups', 'Admin.Global') => 0,
        ], $this->groupByIdChoiceProvider->getChoices());

        $builder
            ->add('currency_id', CurrencyChoiceType::class, [
                'add_all_currencies_option' => true,
                'label' => false,
            ])
            ->add('country_id', CountryChoiceType::class, [
                'add_all_countries_option' => true,
                'label' => false,
                'placeholder' => false,
                'required' => false,
            ])
            ->add('group_id', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'choices' => $groups,
            ])
        ;

        if ($this->isMultiShopEnabled) {
            $builder->add('shop_id', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'placeholder' => false,
                'choices' => $this->buildShopChoices(),
            ]);
        }
    }

    /**
     * @return array<string, int>
     */
    private function buildShopChoices(): array
    {
        $choices = [
            $this->trans('All stores', 'Admin.Global') => 0,
        ];

        $allShops = $this->shopByIdChoiceProvider->getChoices();
        foreach ($allShops as $name => $shopId) {
            if ($shopId === $this->contextShopId) {
                $choices[$name] = $shopId;
                break;
            }
        }

        return $choices;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'columns_number' => 4,
        ]);
    }
}
