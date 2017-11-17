<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @version  Release: $Revision: 6844 $
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
class ReleaseCreator
{
    /** @var string */
    const TEMP_PROJECT_PATH = '/tmp/PrestaShop-release-tmp';

    /** @var array */
    protected $filesRemoveList = ['.DS_Store', '.gitignore', '.gitmodules', '.travis.yml'];

    /** @var array */
    protected $foldersRemoveList = [];

    /** @var array */
    protected $patternsRemoveList = [
        'tests$',
        'tools/contrib$',
        'travis\-scripts$',
        'CONTRIBUTING\.md$',
        'composer\.json$',
        'diff\-hooks\.php',
        '((?<!_dev\/)package\.json)$',
        '(.*)?\.composer$',
        '(.*)?\.git(.*)?$',
        '.*\.map$',
        '.*\.psd$',
        '.*\.md$',
        '.*\.rst$',
        '.*phpunit(.*)?',
        '(.*)?\.travis\.',
        '.*\.DS_Store$',
        '.*\.eslintrc$',
        '.*\.editorconfig$',
        'web/.*$',
        'app/config/parameters\.yml$',
        'app/config/parameters\.php$',
        'config/settings\.inc\.php$',
        'app/cache/..*$',
        '\.t9n\.yml$',
        '\.scrutinizer\.yml$',
        'admin\-dev/(.*/)?webpack\.config\.js$',
        'admin\-dev/(.*/)?package\.json$',
        'admin\-dev/(.*/)?bower\.json$',
        'admin\-dev/(.*/)?config\.rb$',
        'admin\-dev/themes/default/sass$',
        'admin\-dev/themes/new\-theme/js$',
        'admin\-dev/themes/new\-theme/scss$',
        'themes/_core$',
        'themes/webpack\.config\.js$',
        'themes/package\.json$',
        'vendor\/[a-zA-Z0-0_-]+\/[a-zA-Z0-0_-]+\/[Tt]ests?$',
        'vendor/tecnickcom/tcpdf/examples$',
        'app/cache/..*$',
        '.idea',
        'tools/build$',
        '.*node_modules.*',
    ];

    /** @var array */
    protected $filesList = [];

    /** @var string */
    protected $projectPath;

    /** @var string */
    protected $version;

    /**
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version = $version;
        $this->projectPath = realpath(__DIR__ . '/../../..');
    }

    /**
     * @return $this
     */
    public function createRelease()
    {
        $startTime = date('H:i:s');
        echo "\e[32m--- Script started at {$startTime} \e[m\n\n";
        $this->setFilesConstants()
            ->generateLicensesFile()
            ->updateComposerJsonFile()
            ->runComposerInstall()
            ->createPackages();
        $endTime = date('H:i:s');
        echo "\n\e[32m--- Script ended at {$endTime} \e[m\n";

        return $this;
    }

    /**
     * @return self
     */
    protected function setFilesConstants()
    {
        echo "\e[33mSetting files constants...\e[m";
        $this->setConfigDefinesConstants()
            ->setConfigAutoloadConstants()
            ->setInstallDevConfigurationConstants()
            ->setInstallDevInstallVersionConstants();
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function setConfigDefinesConstants()
    {
        $configDefinesPath = $this->projectPath . '/config/defines.inc.php';
        $configDefinesContent = file_get_contents($configDefinesPath);
        $configDefinesNewContent = preg_replace('/(.*(define).*)_PS_MODE_DEV_(.*);/Ui', 'define(\'_PS_MODE_DEV_\', false);', $configDefinesContent);
        $configDefinesNewContent = preg_replace('/(.*)_PS_DISPLAY_COMPATIBILITY_WARNING_(.*);/Ui', 'define(\'_PS_DISPLAY_COMPATIBILITY_WARNING_\', false);', $configDefinesNewContent);

        if (!file_put_contents($configDefinesPath, $configDefinesNewContent)) {
            throw new BuildException("Unable to update contents of '$configDefinesPath'");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function setConfigAutoloadConstants()
    {
        $configAutoloadPath = $this->projectPath.'/config/autoload.php';
        $configAutoloadContent = file_get_contents($configAutoloadPath);
        $configAutoloadNewContent = preg_replace('#_PS_VERSION_\', \'(.*)\'\)#', '_PS_VERSION_\', \'' . $this->version . '\')', $configAutoloadContent);

        if (!file_put_contents($configAutoloadPath, $configAutoloadNewContent)) {
            throw new BuildException("Unable to update contents of '$configAutoloadPath'");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function setInstallDevConfigurationConstants()
    {
        $configPath = $this->projectPath.'/install-dev/data/xml/configuration.xml';

        if (file_exists($configPath)) {
            $configPathContent = file_get_contents($configPath);
            $configPathNewContent = preg_replace('/name="PS_SMARTY_FORCE_COMPILE"(.*?)value>([\d]*)/si', 'name="PS_SMARTY_FORCE_COMPILE"$1value>0', $configPathContent);
            $configPathNewContent = preg_replace('/name="PS_SMARTY_CONSOLE"(.*?)value>([\d]*)/si', 'name="PS_SMARTY_CONSOLE"$1value>0', $configPathNewContent);

            if (!file_put_contents($configPath, $configPathNewContent)) {
                throw new BuildException("Unable to update contents of '$configPath'.");
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function setInstallDevInstallVersionConstants()
    {
        $installVersionPath = $this->projectPath . '/install-dev/install_version.php';
        $installVersionContent = file_get_contents($installVersionPath);
        $installVersionNewContent = preg_replace('#_PS_INSTALL_VERSION_\', \'(.*)\'\)#', '_PS_INSTALL_VERSION_\', \'' . $this->version . '\')', $installVersionContent);

        if (!file_put_contents($installVersionPath, $installVersionNewContent)) {
            throw new BuildException("Unable to update contents of '$installVersionPath'.");
        }

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function generateLicensesFile()
    {
        echo "\e[33mGenerating licences file...\e[m";
        $content = null;
        $directory = new \RecursiveDirectoryIterator($this->projectPath);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.*\/.*license(\.txt)?$/i', \RecursiveRegexIterator::GET_MATCH);

        foreach($regex as $file => $value) {
            $content .= file_get_contents($file) . "\r\n\r\n";
        }

        if (!file_put_contents($this->projectPath . '/LICENSES', $content)) {
            throw new BuildException('Unable to create LICENSES file.');
        }
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function updateComposerJsonFile()
    {
        echo "\e[33mUpdating composer.json...\e[m";
        $replacement = '"PrestaShop\\\\\\PrestaShop\\\\\\Core\\\\\\Cldr\\\\\\Composer\\\\\\Hook::init",
            "Sensio\\\\\\Bundle\\\\\\DistributionBundle\\\\\\Composer\\\\\\ScriptHandler::buildBootstrap",
            "Sensio\\\\\\Bundle\\\\\\DistributionBundle\\\\\\Composer\\\\\\ScriptHandler::installRequirementsFile",
            "Sensio\\\\\\Bundle\\\\\\DistributionBundle\\\\\\Composer\\\\\\ScriptHandler::prepareDeploymentTarget"';
        $content = file_get_contents($this->projectPath . '/composer.json');
        $content = preg_replace('/("post-install-cmd": \\[)(.*)("post-update-cmd": \\[)/s', "$1
            ".$replacement."],\r\n    $3", $content);

        if (!file_put_contents($this->projectPath . '/composer.json', $content)) {
            throw new BuildException('Unable to update composer.json');
        }
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function runComposerInstall()
    {
        echo "\e[33mRunning composer install...\e[m";
        $command = "cd $this->projectPath && export SYMFONY_ENV=prod && composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode != 0) {
            throw new BuildException('Unable to run composer install.');
        }
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function createAndRenameFolders()
    {
        if (!file_exists($this->projectPath . '/app/cache/')) {
            mkdir($this->projectPath . '/app/cache', 0777, true);
        }

        if (!file_exists($this->projectPath . '/app/logs/')) {
            mkdir($this->projectPath . '/app/logs', 0777, true);
        }
        $itemsToRename = ['admin-dev' => 'admin', 'install-dev' => 'install'];
        $basePath = self::TEMP_PROJECT_PATH;

        foreach ($itemsToRename as $oldName => $newName) {
            if (file_exists("$basePath/$oldName")) {
                rename("{$basePath}/$oldName", "{$basePath}/$newName");
            } else {
                throw new BuildException("Unable to rename $oldName to $newName, file does not exist.");
            }
        }

        return $this;
    }

    /**
     * @return self
     */
    protected function createPackages()
    {
        echo "\e[33mCreating package...\e[m\n";
        $this->cleanTmpProject();
        $this->generateXMLChecksum();
        $this->createZipArchive();
        echo "\e[32mPackage successfully created...\e[m\n";

        return $this;
    }

    /**
     * @return $this
     */
    protected function cleanTmpProject()
    {
        echo "\e[33m--- Cleaning project...\e[m";
        $destination = self::TEMP_PROJECT_PATH;

        if (file_exists($destination)) {
            exec("rm -rf $destination");
        }
        exec("cp -r {$this->projectPath} $destination");
        $this->createAndRenameFolders();
        $this->filesList = $this->getDirectoryStructure($destination);
        $this->removeUnnecessaryFiles(
            $this->filesList,
            $this->filesRemoveList,
            $this->foldersRemoveList,
            $this->patternsRemoveList,
            self::TEMP_PROJECT_PATH
        );
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @param string $path
     * @return array
     */
    protected function getDirectoryStructure($path)
    {
        $flags = FilesystemIterator::SKIP_DOTS | RecursiveIteratorIterator::CHILD_FIRST;
        $iterator = new RecursiveDirectoryIterator($path, $flags);
        $childrens = iterator_count($iterator);
        $structure = [];

        if ($childrens > 0) {
            $children = $iterator->getChildren();

            for ($index = 0; $index < $childrens; $index += 1) {
                $pathname = $children->getPathname();

                if ($children->hasChildren() === true) {
                    $structure[$pathname] = $this->getDirectoryStructure($pathname);
                } else {
                    $structure[] = $pathname;
                }
                $children->next();
            }
        }
        ksort($structure);

        return $structure;
    }

    /**
     * @param array $filesList
     * @param array $filesRemoveList
     * @param array $foldersRemoveList
     * @param array $patternsRemoveList
     * @param string $folder
     * @return self
     * @throws BuildException
     */
    protected function removeUnnecessaryFiles(
        array &$filesList,
        array &$filesRemoveList,
        array &$foldersRemoveList,
        array &$patternsRemoveList,
        $folder
    ) {
        foreach ($filesList as $key => $value) {
            $pathToTest = $value;

            if (!is_string($pathToTest)) {
                $pathToTest = $key;
            }

            if (substr($pathToTest, 0, 4) != '/tmp') {
                throw new BuildException("Trying to delete a file somewhere else than in /tmp, path: $pathToTest");
            }

            if (is_numeric($key)) {
                // Remove files.
                foreach ($filesRemoveList as $file_to_remove) {
                    if ($folder.'/'.$file_to_remove == $value) {
                        unset($filesList[$key]);
                        exec("rm -f $value");
                        continue 2;
                    }
                }

                // Remove folders.
                foreach ($foldersRemoveList as $folder_to_remove) {
                    if ($folder.'/'.$folder_to_remove == $value) {
                        unset($filesList[$key]);
                        exec("rm -rf $value");
                        continue 2;
                    }
                }

                // Pattern to remove.
                foreach ($patternsRemoveList as $pattern_to_remove) {
                    if (preg_match('#'.$pattern_to_remove.'#', $value) == 1) {
                        unset($filesList[$key]);
                        exec("rm -rf $value");
                        continue 2;
                    }
                }
            } else {
                // Remove folders.
                foreach ($foldersRemoveList as $folder_to_remove) {
                    if ($folder.'/'.$folder_to_remove == $key) {
                        unset($filesList[$key]);
                        exec("rm -rf $key");
                        continue 2;
                    }
                }

                // Pattern to remove.
                foreach ($patternsRemoveList as $pattern_to_remove) {
                    if (preg_match('#'.$pattern_to_remove.'#', $key) == 1) {
                        unset($filesList[$key]);
                        exec("rm -rf $key");
                        continue 2;
                    }
                }
                $this->removeUnnecessaryFiles($filesList[$key], $filesRemoveList, $foldersRemoveList, $patternsRemoveList, $folder);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function createZipArchive()
    {
        echo "\e[33m--- Creating zip archive...\e[m";
        $tempProjectPath = self::TEMP_PROJECT_PATH;
        $zipZile = "prestashop_$this->version.zip";
        // Will be used with the index.php installer
        //$subZip = "prestashop.zip";
        $cmd = "cd $tempProjectPath \
            && zip -rq $zipZile . \
            && cd -";
        exec($cmd);
        // Will be used with the index.php installer
        // $zip = new ZipArchive();
        // $zip->open($zipZile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        // $zip->addFile("$tempProjectPath/$subZip", $subZip);
        // $zip->close();
        $reference = $this->version . "_" . date("Ymd_His");
        mkdir("$this->projectPath/tools/build/releases/$reference", 0777, true);
        rename(
            "$tempProjectPath/$zipZile",
            "$this->projectPath/tools/build/releases/$reference/prestashop_$this->version.zip"
        );
        rename(
            "/tmp/prestashop_$this->version.xml",
            "$this->projectPath/tools/build/releases/$reference/prestashop_$this->version.xml"
        );
        exec("rm -rf $tempProjectPath");
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @return self
     * @throws BuildException
     */
    protected function generateXMLChecksum()
    {
        echo "\e[33m--- Generating XML checksum...\e[m";
        $xmlPath = "/tmp/prestashop_$this->version.xml";
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".PHP_EOL;
        $content .= "<checksum_list>".PHP_EOL;
        $content .= "\t<ps_root_dir version=\"$this->version\">".PHP_EOL;
        $content .= $this->generateXMLDirectoryChecksum($this->filesList);
        $content .= "\t".'</ps_root_dir>'.PHP_EOL;
        $content .= '</checksum_list>'.PHP_EOL;

        if (!file_put_contents($xmlPath, $content)) {
            throw new BuildException('Unable to generate XML checksum.');
        }
        echo "\e[32m DONE\e[m\n";

        return $this;
    }

    /**
     * @param array $files
     * @return string
     */
    protected function generateXMLDirectoryChecksum(array $files)
    {
        $content = null;
        $subCount = substr_count(self::TEMP_PROJECT_PATH, DIRECTORY_SEPARATOR);

        foreach ($files as $key => $value) {
            if (is_numeric($key)) {
                $md5 = md5_file($value);
                $count = substr_count($value, DIRECTORY_SEPARATOR) - $subCount + 1;
                $file_name = str_replace(self::TEMP_PROJECT_PATH, null, $value);
                $file_name = pathinfo($file_name, PATHINFO_BASENAME);
                $content .= str_repeat("\t", $count) . "<md5file name=\"$file_name\">$md5</md5file>" . PHP_EOL;
            } else {
                $count = substr_count($key, DIRECTORY_SEPARATOR) - $subCount + 1;
                $dir_name = str_replace(self::TEMP_PROJECT_PATH, null, $key);
                $dir_name = pathinfo($dir_name, PATHINFO_BASENAME);
                $content .= str_repeat("\t", $count) . "<dir name=\"$dir_name\">" . PHP_EOL;
                $content .= $this->generateXMLDirectoryChecksum($value);
                $content .= str_repeat("\t", $count) . "</dir>" . PHP_EOL;
            }
        }

        return $content;
    }
}
