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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Unit\Core\Email;

use Egulias\EmailValidator\EmailLexer;
use Egulias\EmailValidator\Result\InvalidEmail;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Email\SwiftMailerValidation;

class SwiftMailerValidationTest extends TestCase
{
    /**
     * @var SwiftMailerValidation
     */
    protected $validator;

    /**
     * @var EmailLexer
     */
    protected $lexer;

    protected function setUp(): void
    {
        $this->validator = new SwiftMailerValidation();
        $this->lexer = new EmailLexer();
    }

    public function testForEmptyWarnings(): void
    {
        $this->assertEquals([], $this->validator->getWarnings());
    }

    public function testForNonASCII(): void
    {
        $this->assertNull($this->validator->getError());
        $this->assertFalse($this->validator->isValid('é@gmail.com', $this->lexer));
        $this->assertInstanceOf(InvalidEmail::class, $this->validator->getError());
    }

    public function testForASCII(): void
    {
        $this->assertNull($this->validator->getError());
        $this->assertTrue($this->validator->isValid('test@gmail.com', $this->lexer));
        $this->assertNull($this->validator->getError());
    }
}
