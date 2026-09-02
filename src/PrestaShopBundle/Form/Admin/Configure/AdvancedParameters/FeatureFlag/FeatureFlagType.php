<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag;

use PrestaShopBundle\Form\Admin\Type\FeatureFlagSwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureFlagType extends TranslatorAwareType
{
    /**
     * @var FormCloner
     */
    protected $formCloner;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormCloner $formCloner
    ) {
        parent::__construct($translator, $locales);
        $this->formCloner = $formCloner;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', FeatureFlagSwitchType::class, [
                'choices' => [
                    $this->trans('Disabled', 'Admin.Global') => false,
                    $this->trans('Enabled', 'Admin.Global') => true,
                ],
                'required' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'adaptSwitchOption'])
        ;
    }

    public function adaptSwitchOption(PreSetDataEvent $event): void
    {
        $featureFlagData = $event->getData();
        $form = $event->getForm();
        $form->add($this->formCloner->cloneForm($form->get('enabled'), [
            'label' => $this->trans($featureFlagData['label'], $featureFlagData['label_domain']),
            'help' => $this->trans($featureFlagData['description'], $featureFlagData['description_domain']),
            'attr' => ['disabled' => $featureFlagData['disabled']],
            'types' => $featureFlagData['type'],
            'used_type' => $featureFlagData['type_used'],
            'forced_by_env' => $featureFlagData['forced_by_env'],
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'required' => false,
            ])
        ;
    }
}
