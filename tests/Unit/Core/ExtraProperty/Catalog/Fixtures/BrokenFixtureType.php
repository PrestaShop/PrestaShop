<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Catalog\Fixtures;

use RuntimeException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Form type that cannot be built, used by FormFieldTreeProviderTest to assert resilience.
 */
class BrokenFixtureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        throw new RuntimeException('This fixture form type cannot be built');
    }
}
