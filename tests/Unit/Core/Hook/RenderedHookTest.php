<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Hook;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Hook\HookInterface;
use PrestaShop\PrestaShop\Core\Hook\RenderedHook;

class RenderedHookTest extends TestCase
{
    /**
     * @var HookInterface
     */
    private $hookStub;

    /**
     * @var RenderedHook
     */
    private $renderedHook;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->hookStub = $this->createMock(HookInterface::class);

        $this->renderedHook = new RenderedHook($this->hookStub, $this->content());
    }

    public function testGetHook()
    {
        $this->assertInstanceOf(HookInterface::class, $this->renderedHook->getHook());
        $this->assertSame($this->hookStub, $this->renderedHook->getHook());
    }

    public function testGetContent()
    {
        $this->assertIsArray($this->renderedHook->getContent());
        $this->assertSame($this->content(), $this->renderedHook->getContent());
    }

    public function testOutputContent()
    {
        /** @see RenderedHookTest::content() */
        $expected = '<h1>Hello World</h1><p>How are you?</p> '; // one extra space in the end is intended.
        $this->assertIsString($this->renderedHook->outputContent());
        $this->assertSame($expected, $this->renderedHook->outputContent());
    }

    /**
     * This will return the expected content for the rendered Hook.
     *
     * @return array
     */
    private function content()
    {
        return [
            'module_1' => '<h1>Hello World</h1>',
            'module_2' => '<p>How are you?</p>',
            'module_3' => ' ',
        ];
    }
}
