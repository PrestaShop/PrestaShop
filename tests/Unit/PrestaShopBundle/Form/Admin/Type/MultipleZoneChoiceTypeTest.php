<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Type;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\MultipleZoneChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MultipleZoneChoiceTypeTest extends TestCase
{
    public function testTheMarkInactiveOptionReachesTheChoiceProvider(): void
    {
        $choiceProvider = $this->createMock(ConfigurableFormChoiceProviderInterface::class);
        $choiceProvider
            ->expects(self::once())
            ->method('getChoices')
            ->with(['active' => false, 'active_first' => false, 'mark_inactive' => true])
            ->willReturn(['Europe (Inactive)' => 1]);

        // ChoiceType, the parent, defines the option the type normalizes
        $resolver = (new OptionsResolver())->setDefault('choices', []);
        (new MultipleZoneChoiceType($choiceProvider))->configureOptions($resolver);
        $options = $resolver->resolve(['mark_inactive' => true]);

        self::assertSame(['Europe (Inactive)' => 1], $options['choices']);
    }
}
