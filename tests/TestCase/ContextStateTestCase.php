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

namespace Tests\TestCase;

use Cart;
use Context;
use Country;
use Currency;
use Customer;
use Language;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShopBundle\Translation\TranslatorComponent as Translator;
use Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Integration\Utility\ContextMockerTrait;

abstract class ContextStateTestCase extends TestCase
{
    /*
     * Use the trait to make sure context is backup and restored before/after the class tests. However,
     * context mocking is handled via the custom createContextMock to match specific use cases.
     */
    use ContextMockerTrait;

    /**
     * @param array $contextFields
     *
     * @return Context
     */
    protected function createContextMock(array $contextFields): Context
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $contextMock
            ->method('getTranslator')
            ->willReturn(
                $this
                    ->getMockBuilder(Translator::class)
                    ->disableOriginalConstructor()
                    ->setMethodsExcept([
                        'setLocale',
                        'getLocale',
                    ])
                    ->getMock()
            );

        foreach ($contextFields as $fieldName => $contextValue) {
            $contextMock->$fieldName = $contextValue;
            if ($fieldName === 'language' && $contextValue instanceof Language) {
                $contextMock->getTranslator()->setLocale('test' . $contextValue->id);
            }
            if ($fieldName === 'currentLocale') {
                $contextMock
                    ->method('getCurrentLocale')
                    ->willReturnCallback(static function () use ($contextMock) {
                        return $contextMock->currentLocale;
                    })
                ;
            }
        }
        LegacyContext::setInstanceForTesting($contextMock);

        return $contextMock;
    }

    /**
     * @param string $className
     * @param int $objectId
     *
     * @return MockObject|Cart|Country|Currency|Customer|Language|Shop
     */
    protected function createContextFieldMock(string $className, int $objectId)
    {
        $contextFieldMockBuilder = $this->getMockBuilder($className)->disableOriginalConstructor();

        /** @var Cart|Country|Currency|Customer|Language|Shop $contextField */
        $contextField = $contextFieldMockBuilder->getMock();

        $contextField->id = $objectId;

        if ($className == Language::class) {
            $contextField->locale = 'test' . $objectId;
        }

        return $contextField;
    }

    protected function createLegacyControllerContextMock(string $controllerName): LegacyControllerContext|MockObject
    {
        $legacyControllerContextBuilder = $this->getMockBuilder(LegacyControllerContext::class)
            // Since most fields ar readonly and set via the constructor we must specify them this way
            ->setConstructorArgs([
                $this->createMock(ContainerInterface::class),
                $controllerName,
                'admin',
                7,
                null,
                42,
                'token',
                '',
                'index.php',
            ])
        ;

        /** @var LegacyControllerContext $legacyControllerContext */
        $legacyControllerContext = $legacyControllerContextBuilder->getMock();

        return $legacyControllerContext;
    }
}
