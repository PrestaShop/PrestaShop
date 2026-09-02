<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\TestCase;

use Cart;
use Context;
use Country;
use Currency;
use Customer;
use Employee;
use Language;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShopBundle\Translation\TranslatorComponent as Translator;
use Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Tests\Integration\Utility\ContextMockerTrait;
use Twig\Environment;

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

        $locale = 'en';
        if (isset($contextFields['language']) && $contextFields['language'] instanceof Language) {
            $locale = $contextFields['language']->locale;
        }
        $translator = new Translator($locale);
        $contextMock->method('getTranslator')->willReturn($translator);

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
     * @return MockObject|Cart|Country|Currency|Customer|Employee|Language|Shop
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
                'index.php?controller=' . $controllerName,
                'configuration',
                $this->createMock(Request::class),
                1,
                'http://localhost',
                'admin-dev',
                false,
                '9.0.0',
                $this->createMock(Environment::class),
            ])
        ;

        /** @var LegacyControllerContext $legacyControllerContext */
        $legacyControllerContext = $legacyControllerContextBuilder->getMock();

        return $legacyControllerContext;
    }
}
