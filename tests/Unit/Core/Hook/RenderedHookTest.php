<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
