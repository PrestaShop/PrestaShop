<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form class generates the "Optional Features" form in Performance page.
 */
class OptionalFeaturesType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isCombinationsUsed;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isCombinationsUsed
     */
    public function __construct(TranslatorInterface $translator, array $locales, $isCombinationsUsed)
    {
        parent::__construct($translator, $locales);

        $this->isCombinationsUsed = $isCombinationsUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('combinations', SwitchType::class, [
                'disabled' => $this->isCombinationsUsed,
                'label' => $this->trans('Combinations', 'Admin.Global'),
                'help' => sprintf(
                    '%s<br>%s',
                    $this->trans('Choose "No" to disable Product Combinations.', 'Admin.Advparameters.Help'),
                    $this->trans('You cannot set this parameter to No when combinations are already used by some of your products', 'Admin.Advparameters.Help')
                ),
            ])
            ->add('features', SwitchType::class, [
                'label' => $this->trans('Features', 'Admin.Global'),
                'help' => $this->trans('Choose "No" to disable Product Features.', 'Admin.Advparameters.Help'),
            ])
            ->add('customer_groups', SwitchType::class, [
                'label' => $this->trans('Customer groups', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Choose "No" to disable Customer Groups.', 'Admin.Advparameters.Help'),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'performance_optional_features_block';
    }
}
