<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Type;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The javascript component is built per container, so a widget that both asks for automatic
 * initialisation and is instantiated by its page would end up with two of them on the same input.
 * That is what keeps the option off unless a form asks for it, and this pins it.
 */
class EntitySearchInputAutoInitTest extends TestCase
{
    public function testAutomaticInitialisationIsOffUnlessAFormAsksForIt(): void
    {
        $this->assertFalse($this->resolve([])['auto_init']);
    }

    public function testAFormCanAskForAutomaticInitialisation(): void
    {
        $this->assertTrue($this->resolve(['auto_init' => true])['auto_init']);
    }

    public function testTheOptionOnlyAcceptsABoolean(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->resolve(['auto_init' => 'yes']);
    }

    /**
     * @return array<string, mixed>
     */
    private function resolve(array $options): array
    {
        $resolver = new OptionsResolver();
        (new EntitySearchInputType($this->createMock(TranslatorInterface::class)))->configureOptions($resolver);

        return $resolver->resolve($options);
    }
}
