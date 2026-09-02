<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

final class ChangeCountriesZoneType extends AbstractType
{
    public function __construct(private readonly ConfigurableFormChoiceProviderInterface $zoneChoiceProvider)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_zone_id', ChoiceType::class, [
                'choices' => $this->zoneChoiceProvider->getChoices([]),
                'translation_domain' => false,
            ])
            ->add('country_ids', CollectionType::class, [
                'allow_add' => true,
                'entry_type' => HiddenType::class,
                'label' => false,
            ])
        ;

        $builder->get('country_ids')
            ->addModelTransformer(new CallbackTransformer(
                static function ($countryIds) {
                    return $countryIds;
                },
                static function (array $countryIds) {
                    return array_map(static function ($countryId) {
                        return (int) $countryId;
                    }, $countryIds);
                }
            ))
        ;
    }
}
