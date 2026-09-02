<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class ChangeStatesZoneType extends AbstractType
{
    public function __construct(private ConfigurableFormChoiceProviderInterface $zoneChoiceProvider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('new_zone_id', ChoiceType::class, [
                'choices' => $this->zoneChoiceProvider->getChoices([]),
                'translation_domain' => false,
            ])
            ->add('state_ids', CollectionType::class, [
                'allow_add' => true,
                'entry_type' => HiddenType::class,
                'label' => false,
            ])
        ;

        $builder->get('state_ids')
            ->addModelTransformer(new CallbackTransformer(
                static function ($stateIds) {
                    return $stateIds;
                },
                static function (array $stateIds) {
                    return array_map(static function ($stateId) {
                        return (int) $stateId;
                    }, $stateIds);
                }
            ))
        ;
    }
}
