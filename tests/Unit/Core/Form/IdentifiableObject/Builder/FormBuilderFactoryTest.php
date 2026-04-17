<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\Builder;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderFactory;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;

class FormBuilderFactoryTest extends TestCase
{
    public function testCanBeConstructed(): void
    {
        $factory = new FormBuilderFactory(
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(FormRegistryInterface::class)
        );

        $this->assertInstanceOf(FormBuilderFactoryInterface::class, $factory);
    }

    public function testCreate(): void
    {
        // constructor mocks
        $dataProviderMock = $this->createMock(FormDataProviderInterface::class);

        $factory = new FormBuilderFactory(
            $this->createMock(FormFactoryInterface::class),
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(FormRegistryInterface::class)
        );

        $builder = $factory->create('a', $dataProviderMock);

        $this->assertInstanceOf(FormBuilderInterface::class, $builder);
    }
}
