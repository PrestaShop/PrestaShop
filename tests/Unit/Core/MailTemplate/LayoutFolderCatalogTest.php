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
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\FolderLayoutCatalog;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTheme;
use PrestaShop\PrestaShop\Core\MailTemplate\MailThemeCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\MailThemeCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailThemeInterface;
use Symfony\Component\Filesystem\Filesystem;

class LayoutFolderCatalogTest extends TestCase
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
     * @var MailThemeCollectionInterface
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
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $catalog = new FolderLayoutCatalog($this->tempDir, $dispatcherMock);
        $this->assertNotNull($catalog);
    }

    public function testListThemes()
    {
        /** @var HookDispatcherInterface $dispatcherMock */
        $dispatcherMock = $this->getMockBuilder(HookDispatcherInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $dispatcherMock
            ->expects($this->once())
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(LayoutCatalogInterface::LIST_MAIL_THEMES_HOOK),
                $this->callback(function (array $hookParams) {
                    $this->assertInstanceOf(MailThemeCollectionInterface::class, $hookParams['mailThemes']);
                    $this->assertCount(count($this->expectedThemes), $hookParams['mailThemes']);

                    return true;
                })
            )
        ;

        $catalog = new FolderLayoutCatalog($this->tempDir, $dispatcherMock);
        $listedThemes = $catalog->listThemes();
        $this->assertEquals($this->expectedThemes, $listedThemes);
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
        $catalog = new FolderLayoutCatalog($fakeFolder, $dispatcherMock);
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

    public function testListLayouts()
    {
        $catalog = new FolderLayoutCatalog($this->tempDir, $this->createHookDispatcherMock($this->tempDir, 8));
        $layoutCollection = $catalog->listLayouts('classic');
        $this->assertNotNull($layoutCollection);
        $this->assertInstanceOf(LayoutCollectionInterface::class, $layoutCollection);
        $this->assertEquals(8, $layoutCollection->count());

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

    public function testListLayoutsWithoutCoreFolder()
    {
        $catalog = new FolderLayoutCatalog($this->tempDir, $this->createHookDispatcherMock($this->tempDir, 4));
        //No bug occurs if the folder does not exist
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::CORE_CATEGORY]));
        /** @var LayoutCollectionInterface $layouts */
        $layouts = $catalog->listLayouts('classic');
        $this->assertEquals(4, $layouts->count());
    }

    public function testListLayoutsWithoutModulesFolder()
    {
        $catalog = new FolderLayoutCatalog($this->tempDir, $this->createHookDispatcherMock($this->tempDir, 4));
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::MODULES_CATEGORY]));
        /** @var LayoutCollectionInterface $layouts */
        $layouts = $catalog->listLayouts('classic');
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
                $this->equalTo(FolderLayoutCatalog::GET_MAIL_THEME_FOLDER_HOOK),
                $this->equalTo([
                    'mailTheme' => 'classic',
                    'mailThemeFolder' => $mailThemeFolder,
                ])
            )
        ;

        $dispatcherMock
            ->expects($this->at(1))
            ->method('dispatchWithParameters')
            ->with(
                $this->equalTo(LayoutCatalogInterface::LIST_MAIL_THEME_LAYOUTS_HOOK),
                $this->callback(function (array $hookParameters) use ($layoutsCount) {
                    $this->assertEquals('classic', $hookParameters['mailTheme']);
                    $this->assertInstanceOf(LayoutCollectionInterface::class, $hookParameters['mailThemeLayouts']);
                    $this->assertCount($layoutsCount, $hookParameters['mailThemeLayouts']);

                    return true;
                })
            )
        ;

        return $dispatcherMock;
    }

    private function createThemesFiles()
    {
        $this->expectedThemes = new MailThemeCollection([
            new MailTheme('classic'),
            new MailTheme('modern'),
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

        /** @var MailThemeInterface $theme */
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
