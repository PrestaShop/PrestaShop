<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog\Fixtures;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type defaulting label_tag_name to a heading, like DiscountSupplierType: a definition must be
 * able to pass null to get a plain label back.
 */
class HeadingLabelFixtureType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined('label_tag_name');
        $resolver->setDefault('label_tag_name', 'h3');
        $resolver->setAllowedTypes('label_tag_name', ['null', 'string']);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
