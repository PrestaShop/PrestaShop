<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTheme;
use PrestaShop\PrestaShop\Core\MailTemplate\MailThemeCollection;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

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

    /** @var LayoutCollectionInterface */
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
        $this->layouts = new LayoutCollection([
            new Layout(
                'account',
                implode(DIRECTORY_SEPARATOR, [$this->coreTempDir, 'account.html.twig']),
                implode(DIRECTORY_SEPARATOR, [$this->coreTempDir, 'account.txt.twig'])
            ),
            new Layout(
                'followup_1',
                implode(DIRECTORY_SEPARATOR, [$this->modulesTempDir, 'followup', 'followup_1.html.twig']),
                '',
                'followup'
            ),
            new Layout(
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

        $catalogMock = $this->getMockBuilder(LayoutCatalogInterface::class)
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
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException
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
        } catch (FileNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(FileNotFoundException::class, $caughtException);
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
        } catch (FileNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(FileNotFoundException::class, $caughtException);
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
            ->will($this->returnCallback(function (LayoutInterface $layout, LanguageInterface $language) {
                return implode('_', [$layout->getName(), 'html', $layout->getModuleName(), $language->getIsoCode()]);
            }))
        ;

        $renderer
            ->expects($this->exactly($this->layouts->count()))
            ->method('renderTxt')
            ->will($this->returnCallback(function (LayoutInterface $layout, LanguageInterface $language) {
                return implode('_', [$layout->getName(), 'txt', $layout->getModuleName(), $language->getIsoCode()]);
            }))
        ;

        return $renderer;
    }

    /**
     * @param array $availableThemes
     * @param LayoutCollectionInterface|null $collection
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LayoutCatalogInterface
     */
    private function createCatalogMock($availableThemes = [], LayoutCollectionInterface $collection = null)
    {
        $catalogMock = $this->getMockBuilder(LayoutCatalogInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (!empty($availableThemes)) {
            $themes = new MailThemeCollection();
            foreach ($availableThemes as $availableTheme) {
                $themes->add(new MailTheme($availableTheme));
            }

            $catalogMock
                ->expects($this->once())
                ->method('listThemes')
                ->willReturn($themes)
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
     * @param string|null $isoCode
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LanguageInterface
     */
    private function createLanguageMock($isoCode = null)
    {
        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $languageMock
            ->expects(null !== $isoCode ? $this->atLeastOnce() : $this->any())
            ->method('getIsoCode')
            ->willReturn($isoCode)
        ;

        return $languageMock;
    }
}
