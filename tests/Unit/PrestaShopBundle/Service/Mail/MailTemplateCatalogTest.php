<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 2018-12-21
 * Time: 17:45
 */

namespace Tests\Unit\PrestaShopBundle\Service\Mail;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Mail\MailTemplateCatalog;
use Symfony\Component\Filesystem\Filesystem;

class MailTemplateCatalogTest extends TestCase
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
     * @var array
     */
    private $expectedThemes;

    /**
     * @var array
     */
    private $coreTemplates;

    /**
     * @var array
     */
    private $moduleTemplates;

    public function setUp()
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_templates';
        $this->fs = new Filesystem();
        $this->fs->remove($this->tempDir);
        $this->createThemesFiles();
    }

    public function testConstructor()
    {
        $catalog = new MailTemplateCatalog($this->tempDir);
        $this->assertNotNull($catalog);
    }

    public function testListThemes()
    {
        $catalog = new MailTemplateCatalog($this->tempDir);
        $listedThemes = $catalog->listThemes();
        $this->assertEquals($this->expectedThemes, $listedThemes);
    }

    public function testListCoreTemplates()
    {
        $expectedTemplates = $this->convertToTemplatesList($this->coreTemplates);

        $catalog = new MailTemplateCatalog($this->tempDir);
        $listedTemplates = $catalog->listCoreTemplates('classic');

        $this->assertEquals($expectedTemplates, $listedTemplates);
    }

    public function testListModuleTemplates()
    {
        $expectedTemplates = [];
        foreach ($this->moduleTemplates as $moduleName => $moduleFiles) {
            if (count($moduleFiles) > 0) {
                $expectedTemplates[$moduleName] = $this->convertToTemplatesList($moduleFiles);
            }
        }

        $catalog = new MailTemplateCatalog($this->tempDir);
        $listedTemplates = $catalog->listModuleTemplates('modern');

        $this->assertEquals($expectedTemplates, $listedTemplates);
    }

    private function createThemesFiles()
    {
        $this->expectedThemes = [
            'classic',
            'modern',
        ];
        $this->coreTemplates = [
            'account.twig',
            'password.twig',
            'order_conf.twig',
            'bank_wire.twig',
        ];
        $this->moduleTemplates = [
            'followup' => [
                'followup_1.twig',
                'followup_2.twig',
            ],
            'ps_emailalerts' => [
                'return_slip.twig',
                'productoutofstock.twig',
            ],
            'empty_module' => [
            ]
        ];

        foreach ($this->expectedThemes as $theme) {
            //Insert core files
            $themeFolder = $this->tempDir . DIRECTORY_SEPARATOR . $theme;
            $coreFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateCatalog::CORE_TEMPLATES;
            $this->fs->mkdir($coreFolder);
            foreach ($this->coreTemplates as $template) {
                $this->fs->touch($coreFolder . DIRECTORY_SEPARATOR . $template);
            }

            //Insert modules files
            $modulesFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateCatalog::MODULES_TEMPLATES;
            foreach ($this->moduleTemplates as $moduleName => $moduleTemplates) {
                $moduleFolder = $modulesFolder . DIRECTORY_SEPARATOR . $moduleName;
                $this->fs->mkdir($moduleFolder);
                foreach ($moduleTemplates as $moduleTemplate) {
                    $this->fs->touch($moduleFolder . DIRECTORY_SEPARATOR . $moduleTemplate);
                }
            }

            //Insert components files used in templates
            $componentsFolder = $themeFolder . DIRECTORY_SEPARATOR . 'components';
            $this->fs->mkdir($componentsFolder);
            $this->fs->touch($componentsFolder . DIRECTORY_SEPARATOR . 'title.twig');
            $this->fs->touch($componentsFolder . DIRECTORY_SEPARATOR . 'image.twig');
        }
        $this->fs->mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'empty_dir');
        $this->fs->touch($this->tempDir . DIRECTORY_SEPARATOR . 'useless_file');
    }

    /**
     * @param array $filesList
     * @return array
     */
    private function convertToTemplatesList(array $filesList)
    {
        $templates = array_map(function($fileName) {
            return substr($fileName, 0, strrpos($fileName, '.'));
        },  $filesList);
        sort($templates);

        return $templates;
    }
}
