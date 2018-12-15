<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Adapter\Meta\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Meta\CommandHandler\AddMetaHandler;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class AddMetaHandlerTest
 */
class AddMetaHandlerTest extends TestCase
{
    /**
     * @var HookDispatcherInterface
     */
    private $mockedHook;

    /**
     * @var ValidatorInterface
     */
    private $mockedValidator;

    public function setUp()
    {
        $this->mockedHook = $this->createMock(HookDispatcherInterface::class);
        $this->mockedValidator = $this->createMock(ValidatorInterface::class);
    }

    public function testItThrowsAnExceptionOnMissingUrlRewriteForDefaultLanguage()
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_URL_REWRITE);

        $this->mockedValidator
            ->method('validate')
            ->willReturn(['error'])
        ;

        $handler = new AddMetaHandler(
            $this->mockedHook,
            $this->mockedValidator,
            1
        );

        $command = new AddMetaCommand('not-index-page');
        $handler->handle($command);
    }
}
