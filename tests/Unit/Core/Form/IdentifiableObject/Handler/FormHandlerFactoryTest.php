<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Form\IdentifiableObject\Handler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactory;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerFactoryInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormHandlerFactoryTest extends TestCase
{
    public function testCanBeConstructed()
    {
        $factory = new FormHandlerFactory(
            $this->createMock(HookDispatcherInterface::class),
            $this->createMock(TranslatorInterface::class),
            true
        );

        $this->assertInstanceOf(FormHandlerFactoryInterface::class, $factory);

        return $factory;
    }

    /**
     * @depends testCanBeConstructed
     */
    public function testItCreatesFormHandler(FormHandlerFactoryInterface $factory)
    {
        $formHandler = $factory->create(
            $this->createMock(FormDataHandlerInterface::class)
        );

        $this->assertInstanceOf(FormHandlerInterface::class, $formHandler);
    }
}
