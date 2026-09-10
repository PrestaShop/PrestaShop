<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * "Visibility" card: where an extra property definition is exposed.
 */
class ExtraPropertyDefinitionVisibilityType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly FeatureInterface $multiStoreFeature,
    ) {
        parent::__construct($translator, $locales);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('display_front', CheckboxType::class, [
                'label' => $this->trans('Display in front-office', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Expose this field in front-office presenters (Smarty templates).', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('required', CheckboxType::class, [
                'label' => $this->trans('Required', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Mark the field as required in the BO form and in the Admin API schema. This only drives the HTML/OpenAPI "required" flag — it never adds a server-side validation constraint by itself.', 'Admin.Advparameters.Help'),
                'required' => false,
            ]);

        if ($this->multiStoreFeature->isUsed()) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                // Unlike most shop_association fields this one is an optional RESTRICTION,
                // not a mandatory membership: no NotBlank, and an empty selection means
                // "no restriction" (available in every store).
                'help' => $this->trans('Leave empty to make this property available in all stores.', 'Admin.Advparameters.Help'),
                'required' => false,
                // Override the type's context-shops default: an untouched field must submit
                // as "no restriction", never as a silent restriction to the current context.
                'default_empty_data' => [],
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Visibility', 'Admin.Global'),
            'icon' => 'visibility',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return CardType::class;
    }
}
