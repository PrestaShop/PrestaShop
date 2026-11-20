<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShopBundle\Form\DataTransformer\IdToEntityTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EntityChoiceType extends AbstractType
{
    public function getParent(): string
    {
        return EntityType::class;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'input' => 'entity',
            ])
            ->setAllowedValues('input', ['entity', 'id'])
            ->setNormalizer('input', static function (Options $options, string $value) {
                if ('id' !== $value || !$options['multiple']) {
                    return $value;
                }

                throw new InvalidOptionsException('The "id" input is not supported for "multiple" choice.');
            });
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ('id' !== $options['input']) {
            return;
        }

        $builder->addModelTransformer(new IdToEntityTransformer($options['em'], $options['class']));
    }
}
