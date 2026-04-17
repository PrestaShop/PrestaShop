<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class is responsible for providing configurable zone choices with -- symbol in front of array.
 */
class ZoneChoiceType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $zonesChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $zonesChoiceProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $zonesChoiceProvider)
    {
        $this->zonesChoiceProvider = $zonesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Set normalizer enables to use closure for choice generation with options
        $resolver->setNormalizer(
            'choices', function (Options $options) {
                return $this->zonesChoiceProvider->getChoices([
                    'active' => $options['active'],
                    'active_first' => $options['active_first'],
                ]);
            }
        );

        $resolver->setDefaults([
            'active' => false,
            'active_first' => false,
            'placeholder' => '--',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
