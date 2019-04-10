<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\FolderThemeCatalog;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Theme;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
use Symfony\Component\Filesystem\Filesystem;

class FolderThemeCatalogTest extends TestCase
{
    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var ThemeCollectionInterface
     */
    private $expectedThemes;

    /**
     * @var array
     */
    private $coreLayouts;

    /**
     * @var array
     */
    private $moduleLayouts;

    public function setUp()
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_layouts';
        $this->fs = new Filesystem();
        $this->fs->remove($this->tempDir);
        $this->createThemesFiles();
    }

    public function testConstructor()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $catalog = new FolderThemeCatalog($this->tempDir, $dispatcherMock);
        $this->assertNotNull($catalog);
    }

    public function testListThemes()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->createHookDispatcherMock($this->tempDir, 8);

        $catalog = new FolderThemeCatalog($this->tempDir, $dispatcherMock);
        $listedThemes = $catalog->listThemes();
        $this->assertEquals($this->expectedThemes->count(), $listedThemes->count());
        /** @var ThemeInterface $theme */
        $theme = $listedThemes[0];
        $this->assertEquals('classic', $theme->getName());

        $layoutCollection = $theme->getLayouts();
        $this->assertCount(8, $layoutCollection);

        //Check core layouts
        $coreLayouts = $this->filterCoreLayouts($layoutCollection);
        $this->assertCount(4, $coreLayouts);

        /** @var LayoutInterface $layout */
        $layout = $coreLayouts[0];
        $this->assertInstanceOf(LayoutInterface::class, $layout);
        $coreFolder = implode(DIRECTORY_SEPARATOR, [
            realpath($this->tempDir),
            'classic',
            MailTemplateInterface::CORE_CATEGORY,
        ]);
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, [$coreFolder, 'account.html.twig']), $layout->getHtmlPath());
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, [$coreFolder, 'account.txt.twig']), $layout->getTxtPath());
        $this->assertEquals('account', $layout->getName());
        $this->assertNotNull($layout->getModuleName());
        $this->assertEmpty($layout->getModuleName());

        //Check module layouts
        $modulesLayouts = $this->filterModulesLayouts($layoutCollection);
        $this->assertCount(4, $modulesLayouts);

        $moduleLayoutsCount = [];
        /** @var LayoutInterface $moduleLayout */
        foreach ($modulesLayouts as $moduleLayout) {
            $this->assertNotEmpty($moduleLayout->getModuleName());
            if (!isset($moduleLayoutsCount[$moduleLayout->getModuleName()])) {
                $moduleLayoutsCount[$moduleLayout->getModuleName()] = [$moduleLayout->getName()];
            } else {
                $moduleLayoutsCount[$moduleLayout->getModuleName()][] = $moduleLayout->getName();
            }
        }
        $this->assertCount(2, $moduleLayoutsCount['followup']);
        $this->assertEquals(['followup_1', 'followup_2'], $moduleLayoutsCount['followup']);
        $this->assertCount(2, $moduleLayoutsCount['ps_emailalerts']);
        $this->assertEquals(['productoutofstock', 'return_slip'], $moduleLayoutsCount['ps_emailalerts']);
    }

    public function testGetByName()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $catalog = new FolderThemeCatalog($this->tempDir, $dispatcherMock);
        $this->assertNotNull($catalog);

        $theme = $catalog->getByName('classic');
        $this->assertNotNull($theme);
        $this->assertEquals('classic', $theme->getName());

        $theme = $catalog->getByName('modern');
        $this->assertNotNull($theme);
        $this->assertEquals('modern', $theme->getName());
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException
     * @expectedExceptionMessage Invalid requested theme "unknown", only available themes are: classic, modern
     */
    public function testInvalidTheme()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $catalog = new FolderThemeCatalog($this->tempDir, $dispatcherMock);
        $this->assertNotNull($catalog);

        $catalog->getByName('unknown');
    }

    public function testListThemesWithoutThemesFolder()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $fakeFolder = implode(
            DIRECTORY_SEPARATOR,
            [$this->tempDir, 'invisible']
        );
        $catalog = new FolderThemeCatalog($fakeFolder, $dispatcherMock);
        $this->assertNotNull($catalog);

        $caughtException = null;
        try {
            $catalog->listThemes();
        } catch (FileNotFoundException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertContains('Invalid mail themes folder', $caughtException->getMessage());
        $this->assertContains(': no such directory', $caughtException->getMessage());
    }

    public function testListThemesWithoutCoreFolder()
    {
        $catalog = new FolderThemeCatalog($this->tempDir, $this->createHookDispatcherMock($this->tempDir, 4));
        //No bug occurs if the folder does not exist
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::CORE_CATEGORY]));

        /** @var ThemeCollectionInterface $themeList */
        $themes = $catalog->listThemes();
        /** @var ThemeInterface $theme */
        $theme = $themes[0];
        /** @var LayoutCollectionInterface $layouts */
        $layouts = $theme->getLayouts();
        $this->assertEquals(4, $layouts->count());
    }

    public function testListThemesWithoutModulesFolder()
    {
        $catalog = new FolderThemeCatalog($this->tempDir, $this->createHookDispatcherMock($this->tempDir, 4));
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::MODULES_CATEGORY]));
        /** @var ThemeCollectionInterface $themeList */
        $themes = $catalog->listThemes();
        /** @var ThemeInterface $theme */
        $theme = $themes[0];
        /** @var LayoutCollectionInterface $layouts */
        $layouts = $theme->getLayouts();
        $this->assertEquals(4, $layouts->count());
    }

    /**
     * @param LayoutCollectionInterface $collection
     *
     * @return LayoutInterface[]
     */
    private function filterCoreLayouts(LayoutCollectionInterface $collection)
    {
        $layouts = [];
        /** @var LayoutInterface $layout */
        foreach ($collection as $layout) {
            if (empty($layout->getModuleName())) {
                $layouts[] = $layout;
            }
        }

        return $layouts;
    }

    /**
     * @param LayoutCollectionInterface $collection
     *
     * @return LayoutInterface[]
     */
    private function filterModulesLayouts(LayoutCollectionInterface $collection)
    {
        $layouts = [];
        /** @var LayoutInterface $layout */
        foreach ($collection as $layout) {
            if (!empty($layout->getModuleName())) {
                $layouts[] = $layout;
            }
        }

        return $layouts;
    }

    /**
     * @param string $tempDir
     * @param int $layoutsCount
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|HookDispatcherInterface
     */
    private function createHookDispatcherMock($tempDir, $layoutsCount)
    {
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $mailThemeFolder = implode(DIRECTORY_SEPARATOR, [$tempDir, 'classic']);
        $dispatcherMock
            ->expects($this->at(0))
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(FolderThemeCatalog::GET_MAIL_THEME_FOLDER_HOOK),
                $this->equalTo([
                    'mailTheme' => 'classic',
                    'mailThemeFolder' => $mailThemeFolder,
                ])
            )
        ;

        $dispatcherMock
            ->expects($this->at(2))
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(ThemeCatalogInterface::LIST_MAIL_THEMES_HOOK),
                $this->callback(function (array $parameters) use ($layoutsCount) {
                    $this->assertInstanceOf(ThemeCollectionInterface::class, $parameters['mailThemes']);
                    /** @var ThemeInterface $theme */
                    $theme = $parameters['mailThemes'][0];
                    $this->assertEquals('classic', $theme->getName());
                    $this->assertCount($layoutsCount, $theme->getLayouts());

                    return true;
                })
            )
        ;

        return $dispatcherMock;
    }

    private function createThemesFiles()
    {
        $this->expectedThemes = new ThemeCollection([
            new Theme('classic'),
            new Theme('modern'),
        ]);
        $this->coreLayouts = [
            'account.html.twig',
            'password.html.twig',
            'account.txt.twig',
            'password.txt.twig',
            'order_conf.twig',
            'bank_wire.twig',
        ];
        $this->moduleLayouts = [
            'followup' => [
                'followup_1.html.twig',
                'followup_1.txt.twig',
                'followup_2.twig',
            ],
            'ps_emailalerts' => [
                'return_slip.twig',
                'productoutofstock.twig',
            ],
            'empty_module' => [
            ],
        ];

        /** @var ThemeInterface $theme */
        foreach ($this->expectedThemes as $theme) {
            //Insert core files
            $themeFolder = $this->tempDir . DIRECTORY_SEPARATOR . $theme->getName();
            $coreFolder = implode(DIRECTORY_SEPARATOR, [$themeFolder, MailTemplateInterface::CORE_CATEGORY]);
            $this->fs->mkdir($coreFolder);
            foreach ($this->coreLayouts as $layout) {
                $this->fs->touch(implode(DIRECTORY_SEPARATOR, [$coreFolder, $layout]));
            }

            //Insert modules files
            $modulesFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateInterface::MODULES_CATEGORY;
            foreach ($this->moduleLayouts as $moduleName => $moduleLayouts) {
                $moduleFolder = $modulesFolder . DIRECTORY_SEPARATOR . $moduleName;
                $this->fs->mkdir($moduleFolder);
                foreach ($moduleLayouts as $layout) {
                    $this->fs->touch(implode(DIRECTORY_SEPARATOR, [$moduleFolder, $layout]));
                }
            }

            //Insert components files used in layoutss
            $componentsFolder = $themeFolder . DIRECTORY_SEPARATOR . 'components';
            $this->fs->mkdir($componentsFolder);
            $this->fs->touch($componentsFolder . DIRECTORY_SEPARATOR . 'title.twig');
            $this->fs->touch($componentsFolder . DIRECTORY_SEPARATOR . 'image.twig');
        }
        $this->fs->mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'empty_dir');
        $this->fs->touch($this->tempDir . DIRECTORY_SEPARATOR . 'useless_file');
    }
}
