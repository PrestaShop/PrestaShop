<?php

class ReleaseCreator
{
    /** @var array */
    protected $itemsToRename = ['/admin-dev' => '/admin', '/install-dev' => '/install'];

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
        $this->setFilesConstants()
            ->generateLicensesFile()
            ->updateComposerJsonFile()
            ->runComposerInstall()
            ->createAppFolders()
            ->createPackages();
    }

    /**
     * @return self
     */
    protected function setFilesConstants()
    {
        echo "\e[33mSetting files constants...\e[m\n";
        $this->setConfigDefinesConstants()
            ->setConfigAutoloadConstants()
            ->setInstallDevConfigurationConstants()
            ->setInstallDevInstallVersionConstants();
        echo "\e[32mFiles constants set\e[m\n";

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
        echo "\e[33mGenerating licences file...\e[m\n";
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
        echo "\e[32mLicences file successfully generated...\e[m\n";

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function updateComposerJsonFile()
    {
        echo "\e[33mUpdating composer.json...\e[m\n";
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
        echo "\e[32mcomposer.json successfully updated...\e[m\n";

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function runComposerInstall()
    {
        echo "\e[33mRunning composer install...\e[m\n";
        $command = "cd $this->projectPath && export SYMFONY_ENV=prod && composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode != 0) {
            throw new BuildException('Unable to run composer install.');
        }
        echo "\e[32mcomposer install successfully run...\e[m\n";

        return $this;
    }

    /**
     * @return $this
     */
    protected function createAppFolders()
    {
        if (!file_exists($this->projectPath . '/app/cache/')) {
            mkdir($this->projectPath . '/app/cache', 0777, true);
        }

        if (!file_exists($this->projectPath . '/app/logs/')) {
            mkdir($this->projectPath . '/app/logs', 0777, true);
        }

        return $this;
    }

    /**
     * @return self
     */
    protected function createPackages()
    {
        echo "\e[33mCreating package...\e[m\n";
        $this->filesList = get_directory_structure($this->projectPath);
        $this->cleanFilesList(
            $this->filesList,
            $this->filesRemoveList,
            $this->foldersRemoveList,
            $this->patternsRemoveList,
            $this->projectPath
        );
        $this->generateLicensesFile();
        $this->createZipArchive();
        $this->generateXMLChecksum();
        unlink("$this->projectPath/tools/build/tmp/prestashop_$this->version.xml");
        echo "\e[32mPackage successfully created...\e[m\n";

        return $this;
    }

    /**
     * @param array $filesList
     * @param array $filesRemoveList
     * @param array $foldersRemoveList
     * @param array $patternsRemoveList
     * @param string $folder
     * @return self
     */
    protected function cleanFilesList(
        array &$filesList,
        array &$filesRemoveList,
        array &$foldersRemoveList,
        array &$patternsRemoveList,
        $folder
    ) {
        foreach ($filesList as $key => $value) {
            if (is_numeric($key)) {
                // Remove files.
                foreach ($filesRemoveList as $file_to_remove) {

                    if ($folder.'/'.$file_to_remove == $value) {
                        unset($filesList[$key]);
                        continue 2;
                    }
                }

                // Remove folders.
                foreach ($foldersRemoveList as $folder_to_remove) {
                    if ($folder.'/'.$folder_to_remove == $value) {
                        unset($filesList[$key]);
                        continue 2;
                    }
                }

                // Pattern to remove.
                foreach ($patternsRemoveList as $pattern_to_remove) {
                    if (preg_match('#'.$pattern_to_remove.'#', $value) == 1) {
                        unset($filesList[$key]);
                        continue 2;
                    }
                }
            } else {
                // Remove folders.
                foreach ($foldersRemoveList as $folder_to_remove) {
                    if ($folder.'/'.$folder_to_remove == $key) {
                        unset($filesList[$key]);
                        continue 2;
                    }
                }

                // Pattern to remove.
                foreach ($patternsRemoveList as $pattern_to_remove) {
                    if (preg_match('#'.$pattern_to_remove.'#', $key) == 1) {
                        unset($filesList[$key]);
                        continue 2;
                    }
                }

                $this->cleanFilesList($filesList[$key], $filesRemoveList, $foldersRemoveList, $patternsRemoveList, $folder);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function createZipArchive()
    {
        echo "\e[33m---Creating zip archive...\e[m\n";
        $tmpPath = "$this->projectPath/tools/build/tmp";

        if (!file_exists($tmpPath)) {
            mkdir($tmpPath, 0777, true);
        }
        $zipZile = "$this->projectPath/tools/build/tmp/prestashop_$this->version.zip";
        $subZip = "$this->projectPath/tools/build/tmp/prestashop.zip";
        $subDir = '';
        $zip = new ZipArchive();
        $zip->open($subZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $filesAdded = 0;

        foreach ($this->arrayFlatten($this->filesList) as $file) {
            $fileName = str_replace($this->projectPath, null, $file);
            $fileName = str_replace(array_keys($this->itemsToRename), $this->itemsToRename, $fileName);

            try {
                $zip->addFile($file, $subDir."/".ltrim($fileName, '/'));
                $filesAdded++;

                if ($filesAdded % 100 === 0) {
                    $zip->close();
                    $zip->open($subZip);
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                exit(1);
            }
        }
        $zip->close();
        $zip->open($zipZile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($subZip);
        $zip->close();
        unlink($subZip);
        $reference = $this->version . "_" . date("Ymd_His");
        mkdir("$this->projectPath/tools/build/releases/$reference", 0777, true);
        rename(
            "$this->projectPath/tools/build/tmp/prestashop_$this->version.zip",
            "$this->projectPath/tools/build/releases/$reference/prestashop_$this->version.zip"
        );
        echo "\e[32m---Zip archive successfully created...\e[m\n";

        return $this;
    }

    /**
     * @param array|null $array
     * @return array
     */
    protected function arrayFlatten($array = null)
    {
        $result = array();

        if (!is_array($array)) {
            $array = func_get_args();
        }

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $this->arrayFlatten($value));
            } else {
                $result = array_merge($result, array($key => $value));
            }
        }

        return $result;
    }

    /**
     * @return self
     * @throws BuildException
     */
    protected function generateXMLChecksum()
    {
        echo "\e[33m---Generating XML checksum...\e[m\n";
        $xml_path = "$this->projectPath/tools/build/tmp/prestashop_$this->version.xml";
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".PHP_EOL;
        $content .= "<checksum_list>".PHP_EOL;
        $content .= "\t<ps_root_dir version=\"$this->version\">".PHP_EOL;
        $content .= $this->generateXMLDirectoryChecksum($this->filesList);
        $content .= "\t".'</ps_root_dir>'.PHP_EOL;
        $content .= '</checksum_list>'.PHP_EOL;

        if (!file_put_contents($xml_path, $content)) {
            throw new BuildException('Unable to generate XML checksum.');
        }
        echo "\e[32m---XML checksum successfully generated...\e[m\n";

        return $this;
    }

    /**
     * @param array $files
     * @return string
     */
    protected function generateXMLDirectoryChecksum(array $files)
    {
        $content = null;
        $subCount = substr_count($this->projectPath, DIRECTORY_SEPARATOR);

        foreach ($files as $key => $value) {
            if (is_numeric($key)) {
                $md5 = md5_file($value);
                $count = substr_count($value, DIRECTORY_SEPARATOR) - $subCount + 1;
                $file_name = str_replace($this->projectPath, null, $value);
                $file_name = str_replace(array_keys($this->itemsToRename), $this->itemsToRename, $file_name);
                $file_name = pathinfo($file_name, PATHINFO_BASENAME);
                $content .= str_repeat("\t", $count) . "<md5file name=\"$file_name\">$md5</md5file>" . PHP_EOL;
            } else {
                $count = substr_count($key, DIRECTORY_SEPARATOR) - $subCount + 1;
                $dir_name = str_replace($this->projectPath, null, $key);
                $dir_name = str_replace(array_keys($this->itemsToRename), $this->itemsToRename, $dir_name);
                $dir_name = pathinfo($dir_name, PATHINFO_BASENAME);
                $content .= str_repeat("\t", $count) . "<dir name=\"$dir_name\">" . PHP_EOL;
                $content .= $this->generateXMLDirectoryChecksum($value);
                $content .= str_repeat("\t", $count) . "</dir>" . PHP_EOL;
            }
        }

        return $content;
    }
}
