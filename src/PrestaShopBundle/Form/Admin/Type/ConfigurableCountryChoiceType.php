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
 * Class responsible for providing configurable countries list
 */
class ConfigurableCountryChoiceType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $countriesChoiceProvider;

    /**
     * @param ConfigurableFormChoiceProviderInterface $countriesChoiceProvider
     */
    public function __construct(ConfigurableFormChoiceProviderInterface $countriesChoiceProvider)
    {
        $this->countriesChoiceProvider = $countriesChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        // Set normalizer enables to use closure for choice generation with options
        $resolver->setNormalizer(
            'choices', function (Options $options) {
                return $this->countriesChoiceProvider->getChoices([
                    'active' => $options['active'],
                    'contains_states' => $options['contains_states'],
                    'list_states' => $options['list_states'],
                ]);
            }
        );

        $resolver->setDefaults([
            'active' => false,
            'contains_states' => false,
            'list_states' => false,
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
