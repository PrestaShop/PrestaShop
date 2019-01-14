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

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\InvalidException;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayout;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Language;

class MailTemplateGeneratorTest extends TestCase
{
    /** @var string */
    private $tempDir;

    /** @var string */
    private $outputTempDir;

    /** @var string */
    private $coreTempDir;

    /** @var string */
    private $modulesTempDir;

    /** @var Filesystem */
    private $fs;

    /** @var MailLayoutCollectionInterface */
    private $layouts;

    public function setUp()
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_layouts';
        $this->outputTempDir = $this->tempDir . DIRECTORY_SEPARATOR . 'output';
        $this->coreTempDir = $this->outputTempDir . DIRECTORY_SEPARATOR . MailTemplateInterface::CORE_CATEGORY;
        $this->modulesTempDir = $this->outputTempDir . DIRECTORY_SEPARATOR . MailTemplateInterface::MODULES_CATEGORY;
        $this->fs->remove($this->outputTempDir);
        $this->layouts = new MailLayoutCollection([
            new MailLayout(
                'account',
                implode(DIRECTORY_SEPARATOR, [$this->coreTempDir, 'account.html.twig']),
                implode(DIRECTORY_SEPARATOR, [$this->coreTempDir, 'account.txt.twig'])
            ),
            new MailLayout(
                'followup_1',
                implode(DIRECTORY_SEPARATOR, [$this->modulesTempDir, 'followup', 'followup_1.html.twig']),
                '',
                'followup'
            ),
            new MailLayout(
                'productoutofstock',
                '',
                implode(DIRECTORY_SEPARATOR, [$this->modulesTempDir, 'ps_emailalerts', 'productoutofstock.txt.twig']),
                'ps_reminder'
            ),
        ]);
        $this->fs->mkdir($this->coreTempDir);
        $this->fs->mkdir($this->modulesTempDir);
    }

    public function testConstructor()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $catalogMock = $this->getMockBuilder(MailLayoutCatalogInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $catalogMock,
            $mailRenderer
        );
        $this->assertNotNull($generator);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\InvalidException
     * @expectedExceptionMessage Invalid theme used "toto", only available themes are: titi, tata
     */
    public function testInvalidTheme()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['titi', 'tata']),
            $mailRenderer
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('toto', $this->createLanguageMock(), $this->coreTempDir, $this->modulesTempDir);
    }

    public function testInvalidCoreOutputFolders()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['titi', 'tata', 'toto']),
            $mailRenderer
        );
        $this->assertNotNull($generator);

        $fakeFolder = $this->tempDir . DIRECTORY_SEPARATOR . 'invisible';
        $caughtException = null;
        try {
            $generator->generateThemeTemplates('toto', $this->createLanguageMock(), $fakeFolder, $this->modulesTempDir);
        } catch (InvalidException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidException::class, $caughtException);
        $expectedMessage = sprintf(
            'Invalid core output folder "%s"',
            $fakeFolder
        );
        $this->assertEquals($expectedMessage, $caughtException->getMessage());
    }

    public function testInvalidModulesOutputFolders()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['titi', 'tata', 'toto']),
            $mailRenderer
        );
        $this->assertNotNull($generator);

        $fakeFolder = $this->tempDir . DIRECTORY_SEPARATOR . 'invisible';
        $caughtException = null;
        try {
            $generator->generateThemeTemplates('toto', $this->createLanguageMock(), $this->coreTempDir, $fakeFolder);
        } catch (InvalidException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidException::class, $caughtException);
        $expectedMessage = sprintf(
            'Invalid modules output folder "%s"',
            $fakeFolder
        );
        $this->assertEquals($expectedMessage, $caughtException->getMessage());
    }

    public function testGenerateTemplates()
    {
        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['classic'], $this->layouts),
            $this->createRendererMock()
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('classic', $this->createLanguageMock(), $this->coreTempDir, $this->modulesTempDir);
        $expectedFiles = [
            'core/account.html' => 'account_html__',
            'core/account.txt' => 'account_txt__',
            'modules/followup/mails/followup_1.html' => 'followup_1_html_followup_',
            'modules/followup/mails/followup_1.txt' => 'followup_1_txt_followup_',
            'modules/ps_reminder/mails/productoutofstock.html' => 'productoutofstock_html_ps_reminder_',
            'modules/ps_reminder/mails/productoutofstock.txt' => 'productoutofstock_txt_ps_reminder_',
        ];
        $this->checkExpectedFiles($expectedFiles);
    }

    public function testGenerateTemplatesWithLocale()
    {
        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['classic'], $this->layouts),
            $this->createRendererMock()
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('classic', $this->createLanguageMock('fr'), $this->coreTempDir, $this->modulesTempDir);
        $expectedFiles = [
            'core/account.html' => 'account_html__fr',
            'core/account.txt' => 'account_txt__fr',
            'modules/followup/mails/followup_1.html' => 'followup_1_html_followup_fr',
            'modules/followup/mails/followup_1.txt' => 'followup_1_txt_followup_fr',
            'modules/ps_reminder/mails/productoutofstock.html' => 'productoutofstock_html_ps_reminder_fr',
            'modules/ps_reminder/mails/productoutofstock.txt' => 'productoutofstock_txt_ps_reminder_fr',
        ];
        $this->checkExpectedFiles($expectedFiles);
    }

    /**
     * @param array $expectedFiles
     */
    private function checkExpectedFiles(array $expectedFiles)
    {
        $finder = new Finder();
        $finder->files()->in($this->outputTempDir);
        $this->assertCount(count($expectedFiles), $finder);
        foreach ($expectedFiles as $expectedFile => $expectedContent) {
            $filePath = implode(DIRECTORY_SEPARATOR, [$this->outputTempDir, $expectedFile]);
            $this->assertTrue($this->fs->exists($filePath), 'File not found ' . $filePath);
            $this->assertEquals($expectedContent, file_get_contents($filePath));
        }
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateRendererInterface
     */
    private function createRendererMock()
    {
        $renderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $renderer
            ->expects($this->exactly($this->layouts->count()))
            ->method('renderHtml')
            ->will($this->returnCallback(function (MailLayoutInterface $layout, Language $language) {
                return implode('_', [$layout->getName(), 'html', $layout->getModuleName(), $language->iso_code]);
            }))
        ;

        $renderer
            ->expects($this->exactly($this->layouts->count()))
            ->method('renderTxt')
            ->will($this->returnCallback(function (MailLayoutInterface $layout, Language $language) {
                return implode('_', [$layout->getName(), 'txt', $layout->getModuleName(), $language->iso_code]);
            }))
        ;

        return $renderer;
    }

    /**
     * @param array $availableThemes
     * @param MailLayoutCollectionInterface|null $collection
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailLayoutCatalogInterface
     */
    private function createCatalogMock($availableThemes = [], MailLayoutCollectionInterface $collection = null)
    {
        $catalogMock = $this->getMockBuilder(MailLayoutCatalogInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (!empty($availableThemes)) {
            $catalogMock
                ->expects($this->once())
                ->method('listThemes')
                ->willReturn($availableThemes)
            ;
        }

        if (null !== $collection) {
            $catalogMock
                ->expects($this->once())
                ->method('listLayouts')
                ->with($this->equalTo('classic'))
                ->willReturn($collection)
            ;
        }

        return $catalogMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Language
     */
    private function createLanguageMock($isoCode = null)
    {
        $languageMock = $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()
            ->getMock();

        $languageMock->iso_code = $isoCode;

        return $languageMock;
    }
}
