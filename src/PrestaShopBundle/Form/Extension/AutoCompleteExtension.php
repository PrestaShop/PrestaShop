<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Adds an option in the form builder to enable autocomplete on select inputs.
 */
class AutoCompleteExtension extends AbstractTypeExtension
{
    private const DEFAULT_MINIMUM_INPUT_LENGTH = 7;

    public static function getExtendedTypes(): iterable
    {
        return [
            ChoiceType::class,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('autocomplete');
        $resolver->setDefined('autocomplete_minimum_choices');

        $resolver->setAllowedTypes('autocomplete', 'bool');
        $resolver->setAllowedTypes('autocomplete_minimum_choices', 'int');

        $resolver->setNormalizer('attr', function (Options $options, ?array $attr) {
            if (isset($options['autocomplete']) && $options['autocomplete']) {
                $attr['data-toggle'] = 'select2';
                $attr['data-minimumResultsForSearch'] = $options['autocomplete_minimum_choices'] ?? self::DEFAULT_MINIMUM_INPUT_LENGTH;
            }

            return $attr;
        });
    }
}
