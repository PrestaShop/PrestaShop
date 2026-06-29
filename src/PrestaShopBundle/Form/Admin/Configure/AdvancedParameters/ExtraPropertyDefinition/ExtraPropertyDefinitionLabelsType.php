<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\ExtraPropertyDefinition;

use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * "Labels and descriptions" card: translation wording/domain pairs shown in BO forms and grids.
 */
class ExtraPropertyDefinitionLabelsType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('label_wording', TextType::class, [
                'label' => $this->trans('Label wording', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Translation wording shown as label in BO forms and grids (e.g. "Internal code").', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('label_domain', TextType::class, [
                'label' => $this->trans('Label domain', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Symfony translation domain for the label wording (e.g. "Admin.Catalog.Feature").', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('description_wording', TextType::class, [
                'label' => $this->trans('Description wording', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Translation wording shown as help text below the BO form field.', 'Admin.Advparameters.Help'),
                'required' => false,
            ])
            ->add('description_domain', TextType::class, [
                'label' => $this->trans('Description domain', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Symfony translation domain for the description wording.', 'Admin.Advparameters.Help'),
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->trans('Labels and descriptions', 'Admin.Advparameters.Feature'),
            'icon' => 'translate',
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
