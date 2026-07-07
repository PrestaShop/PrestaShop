<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog\Fixtures;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Small nested form used by FormFieldTreeProviderTest.
 */
class RootFixtureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'The name'])
            ->add('shipping_address', AddressFixtureType::class)
            ->add('active', CheckboxType::class)
            // Compound on the builder (one child per choice), but its children are internal
            // machinery: the tree must read it as a leaf.
            ->add('groups', ChoiceType::class, [
                'choices' => ['Visitor' => 1, 'Guest' => 2, 'Customer' => 3],
                'expanded' => true,
                'multiple' => true,
            ]);
    }
}
