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

namespace Tests\Integration\Core\MailTemplate;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Theme;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
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

    /** @var ThemeInterface */
    private $theme;

    public function setUp(): void
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_layouts';
        $this->outputTempDir = $this->tempDir . DIRECTORY_SEPARATOR . 'output';
        $this->coreTempDir = $this->outputTempDir . DIRECTORY_SEPARATOR . MailTemplateInterface::CORE_CATEGORY;
        $this->modulesTempDir = $this->outputTempDir . DIRECTORY_SEPARATOR . MailTemplateInterface::MODULES_CATEGORY;
        $this->fs->remove($this->outputTempDir);
        $this->theme = new Theme('classic');
        $this->theme->setLayouts(new LayoutCollection([
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
        ]));
        $this->fs->mkdir($this->coreTempDir);
        $this->fs->mkdir($this->modulesTempDir);
    }

    public function testConstructor()
    {
        /** @var MailTemplateRendererInterface $mailRenderer */
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator($mailRenderer);
        $this->assertNotNull($generator);
    }

    public function testInvalidCoreOutputFolders()
    {
        /** @var MailTemplateRendererInterface $mailRenderer */
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator($mailRenderer);
        $this->assertNotNull($generator);

        $fakeFolder = $this->tempDir . DIRECTORY_SEPARATOR . 'invisible';
        $caughtException = null;
        try {
            $generator->generateTemplates($this->theme, $this->createLanguageMock(), $fakeFolder, $this->modulesTempDir);
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
        /** @var MailTemplateRendererInterface $mailRenderer */
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator($mailRenderer);
        $this->assertNotNull($generator);

        $fakeFolder = $this->tempDir . DIRECTORY_SEPARATOR . 'invisible';
        $caughtException = null;
        try {
            $generator->generateTemplates($this->theme, $this->createLanguageMock(), $this->coreTempDir, $fakeFolder);
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
        $generator = new MailTemplateGenerator($this->createRendererMock());
        $this->assertNotNull($generator);

        $generator->generateTemplates($this->theme, $this->createLanguageMock(), $this->coreTempDir, $this->modulesTempDir);
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
        $generator = new MailTemplateGenerator($this->createRendererMock());
        $this->assertNotNull($generator);

        $generator->generateTemplates($this->theme, $this->createLanguageMock('fr'), $this->coreTempDir, $this->modulesTempDir);
        $expectedFiles = [
            'core/fr/account.html' => 'account_html__fr',
            'core/fr/account.txt' => 'account_txt__fr',
            'modules/followup/mails/fr/followup_1.html' => 'followup_1_html_followup_fr',
            'modules/followup/mails/fr/followup_1.txt' => 'followup_1_txt_followup_fr',
            'modules/ps_reminder/mails/fr/productoutofstock.html' => 'productoutofstock_html_ps_reminder_fr',
            'modules/ps_reminder/mails/fr/productoutofstock.txt' => 'productoutofstock_txt_ps_reminder_fr',
        ];
        $this->checkExpectedFiles($expectedFiles);
    }

    public function testOverwriteTemplates()
    {
        $expectedFiles = [
            'core/fr/account.html' => 'account_html__fr',
            'core/fr/account.txt' => 'account_txt__fr',
            'modules/followup/mails/fr/followup_1.txt' => 'followup_1_txt_followup_fr',
            'modules/followup/mails/fr/followup_1.html' => 'followup_1_html_followup_fr',
            'modules/ps_reminder/mails/fr/productoutofstock.html' => 'productoutofstock_html_ps_reminder_fr',
            'modules/ps_reminder/mails/fr/productoutofstock.txt' => 'productoutofstock_txt_ps_reminder_fr',
        ];

        $previousFiles = [];
        $fileIndex = 0;
        foreach ($expectedFiles as $expectedFile => $fileContent) {
            if ($fileIndex % 2 == 0) {
                $previousFiles[$expectedFile] = $expectedFile;
                $filePath = implode(DIRECTORY_SEPARATOR, [$this->outputTempDir, $expectedFile]);
                $this->fs->dumpFile($filePath, $expectedFile);
            }
            ++$fileIndex;
        }
        $this->checkExpectedFiles($previousFiles);
        $generator = new MailTemplateGenerator($this->createRendererMock(1, 2));
        $this->assertNotNull($generator);

        $generator->generateTemplates($this->theme, $this->createLanguageMock('fr'), $this->coreTempDir, $this->modulesTempDir);
        $this->checkExpectedFiles(array_merge($expectedFiles, $previousFiles));

        //Now check overwriting
        $generator = new MailTemplateGenerator($this->createRendererMock(3, 3));
        $this->assertNotNull($generator);

        $generator->generateTemplates($this->theme, $this->createLanguageMock('fr'), $this->coreTempDir, $this->modulesTempDir, true);
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
     * @param int|null $expectedHtmlRendered
     * @param int|null $expectedTxtRendered
     *
     * @return MockObject|MailTemplateRendererInterface
     */
    private function createRendererMock($expectedHtmlRendered = null, $expectedTxtRendered = null)
    {
        $renderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (null === $expectedHtmlRendered) {
            $expectedHtmlRendered = $this->theme->getLayouts()->count();
        }
        $renderer
            ->expects($this->exactly($expectedHtmlRendered))
            ->method('renderHtml')
            ->will($this->returnCallback(function (LayoutInterface $layout, LanguageInterface $language) {
                return implode('_', [$layout->getName(), 'html', $layout->getModuleName(), $language->getIsoCode()]);
            }))
        ;

        if (null === $expectedTxtRendered) {
            $expectedTxtRendered = $this->theme->getLayouts()->count();
        }
        $renderer
            ->expects($this->exactly($expectedTxtRendered))
            ->method('renderTxt')
            ->will($this->returnCallback(function (LayoutInterface $layout, LanguageInterface $language) {
                return implode('_', [$layout->getName(), 'txt', $layout->getModuleName(), $language->getIsoCode()]);
            }))
        ;

        return $renderer;
    }

    /**
     * @param string|null $isoCode
     *
     * @return MockObject|LanguageInterface
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
