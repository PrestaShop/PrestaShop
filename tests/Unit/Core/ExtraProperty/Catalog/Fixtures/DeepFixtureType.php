<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog\Fixtures;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Recursively nested form (one "child" sub-form per level) used by FormFieldTreeProviderTest
 * to assert the depth cap of the tree.
 */
class DeepFixtureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['depth'] > 0) {
            $builder->add('child', self::class, ['depth' => $options['depth'] - 1]);
        } else {
            $builder->add('leaf', TextType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'depth' => 10,
        ]);
    }
}
