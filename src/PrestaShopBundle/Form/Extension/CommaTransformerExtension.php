<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommaTransformerExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [
            NumberType::class,
            MoneyType::class,
        ];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setNormalizer('attr', function (Options $options, $value) {
            $classAttribute = $value['class'] ?? '';
            $classes = explode(' ', $classAttribute);
            if (!in_array('js-comma-transformer', $classes)) {
                $classes[] = 'js-comma-transformer';
            }

            $value['class'] = implode(' ', $classes);

            return $value;
        });
    }
}
