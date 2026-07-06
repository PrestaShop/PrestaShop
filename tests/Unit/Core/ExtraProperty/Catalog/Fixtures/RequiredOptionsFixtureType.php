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
 * Mimics a form type that cannot be built without options (like EditProductFormType and its
 * required product_id/shop_id/product_type) — used by FormFieldTreeProviderTest to cover the
 * introspection options providers.
 */
class RequiredOptionsFixtureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('reference', TextType::class);

        if ('advanced' === $options['mode']) {
            $builder->add('advanced_reference', TextType::class);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired(['mode'])
            ->setAllowedTypes('mode', 'string');
    }
}
