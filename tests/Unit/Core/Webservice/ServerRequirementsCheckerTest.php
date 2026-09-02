<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace tests\Unit\Core\Webservice;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Hosting\HostingInformation;
use PrestaShop\PrestaShop\Core\Configuration\PhpExtensionCheckerInterface;
use PrestaShop\PrestaShop\Core\Webservice\ServerRequirementsChecker;
use Symfony\Contracts\Translation\TranslatorInterface;

class ServerRequirementsCheckerTest extends TestCase
{
    /**
     * @var TranslatorInterface
     */
    private $mockedTranslator;

    /**
     * @var Configuration|MockObject
     */
    private $mockedConfiguration;

    /**
     * @var HostingInformation
     */
    private $mockedHostingInformation;

    /**
     * @var PhpExtensionCheckerInterface
     */
    private $mockedPhpExtensionChecker;

    protected function setUp(): void
    {
        $this->mockedTranslator = $this->createMock(TranslatorInterface::class);
        $this->mockedTranslator
            ->method('trans')
            ->will($this->returnArgument(0));

        $this->mockedConfiguration = $this->createMock(Configuration::class);
        $this->mockedHostingInformation = $this->getMockBuilder(HostingInformation::class)
            ->getMock();

        $this->mockedPhpExtensionChecker = $this->createMock(PhpExtensionCheckerInterface::class);
    }

    public function testNoErrorsAreReturnedWhenSslIsEnabled()
    {
        $this->mockedConfiguration
            ->method('getBoolean')
            ->will($this->returnValue(true));

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertNotContains('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $errors);
    }

    public function testThatErrorIsReturnedWhenSslIsNotEnabled()
    {
        $this->mockedConfiguration
            ->method('getBoolean')
            ->will($this->returnValue(false));

        $errors = $this->createNewServerRequirementsChecker()->checkForErrors();

        $this->assertContains('It is preferable to use SSL (https:) for webservice calls, as it avoids the "man in the middle" type security issues.', $errors);
    }

    /**
     * @return ServerRequirementsChecker
     */
    private function createNewServerRequirementsChecker()
    {
        return new ServerRequirementsChecker(
            $this->mockedTranslator,
            $this->mockedConfiguration,
            $this->mockedHostingInformation,
            $this->mockedPhpExtensionChecker
        );
    }
}
