<?php


class ReleaseCreator
{
    /** @var array */
    protected $packages = [
        'cs' => ['cs'],
        'de' => ['de'],
        'es' => ['ca', 'es'],
        'fa' => ['fa'],
        'fr' => ['fr', 'qc'],
        'hu' => ['hu'],
        'id' => ['id'],
        'it' => ['it'],
        'pl' => ['pl'],
        'pt' => ['br', 'pt'],
        'ru' => ['ru'],
        'zh' => ['tw', 'zh'],
    ];

    /** @var array */
    protected $createdFiles = ['CONTRIBUTORS.md', 'docs/CHANGELOG.txt', 'translations/en.gzip', 'translations/fr.gzip'];

    /** @var array */
    protected $commitMessagesFlags = ['project', 'installer', 'security', 'fo', 'bo', 'classes', 'core', 'deprecated', 'install', 'in', 'performances', 'mo', 'ws', 'pdf', 'tr', 'lo'];

    /** @var array */
    protected $itemsToRename = ['/admin-dev' => '/admin', '/install-dev' => '/install'];

    /** @var array */
    protected $filesRemoveList = ['.DS_Store', '.gitignore', '.gitmodules', '.travis.yml'];

    /** @var array */
    // !! DEV MODE TO REMOVE !!
    protected $foldersRemoveList = ['build', 'admin-dev', 'app', 'bin', 'cache', 'classes', 'controllers', 'modules', 'override', 'translations', 'vendor'];

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
    ];

    /** @var array */
    protected $filesList = [];

    /** @var string */
    protected $projectPath;

    /** @var string */
    protected $version;

    /** @var string */
    protected $branch;

    /**
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version = $version;
        $this->branch = exec('git symbolic-ref --short HEAD');
        $this->projectPath = realpath(__DIR__ . '/..');
        $this->setFilesConstants()
            ->generateLicensesFile()
            ->updateComposerJsonFile()
            ->runComposerInstall()
            ->createAppFolders()
            ->createPackages()
            ->storePublication();
    }

    /**
     * @return self
     */
    protected function setFilesConstants()
    {
        $this->setConfigDefinesConstants()
            ->setConfigAutoloadConstants()
            ->setInstallDevConfigurationConstants()
            ->setInstallDevInstallVersionConstants();

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

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function updateComposerJsonFile()
    {
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

        return $this;
    }

    /**
     * @return $this
     * @throws BuildException
     */
    protected function runComposerInstall()
    {
        var_dump('composer install');
        $command = "cd $this->projectPath && export SYMFONY_ENV=prod && composer install --no-dev --optimize-autoloader --classmap-authoritative --no-interaction 2>&1";
        exec($command, $output, $returnCode);

        if ($returnCode != 0) {
            throw new BuildException('Unable to run composer install.');
        }
        var_dump('end of composer install');

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

//        foreach ($this->packages as $iso_code => $langs) {
//            $this->filesList = get_directory_structure($this->projectPath);
//            $this->cleanFilesList($this->filesList, $this->filesRemoveList, $this->foldersRemoveList, $this->patternsRemoveList, $this->projectPath);
//            $this->createZipArchive($iso_code);
//            $this->generateXMLChecksum($iso_code);
//        }

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
        var_dump('begin clean files');
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
        var_dump('end clean files');

        return $this;
    }

    /**
     * @param bool $lang
     * @return $this
     */
    protected function createZipArchive($lang = false)
    {
        $zipZile = "prestashop_$this->version.zip";

        if ($lang !== false) {
            $zipZile = $this->projectPath."/prestashop_".$this->version."_".$lang.".zip";
        }
        $subZip = 'prestashop.zip';
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
                    var_dump($filesAdded);
                    var_dump('Close and re-open ZIP');
                    $zip->close();
                    $zip->open($subZip);
                }
            } catch (Exception $e) {
                echo $e->getMessage();
                exit(1);
            }
        }

        // we close the zip and we open another one
//        $PSdezipper = new PSDezipper($this->github);
//
//        if (!$PSdezipper->isReady('index.php')) {
//            $PSdezipper->prepare();
//            $PSdezipper->compile();
//        }
        $zip->close();
        $zip->open($zipZile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Add prestashop.zip
        $zip->addFile($subZip);

        // Add index.php (the dezipper)
//        $zip->addFile($PSdezipper->getStoragePath().'index.php', 'index.php');
        $docPath = "$this->projectPath/public/doc/17";

        if (!file_exists($docPath)) {
            mkdir($docPath, 0777, true);
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($docPath, FilesystemIterator::CURRENT_AS_SELF | FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            $fileName = str_replace($docPath, null, $name);
            $zip->addFile($file->getRealPath(), ltrim($fileName, '/'));
        }
        $zip->close();
        unlink($subZip);

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
     * @param bool $lang
     * @return self
     * @throws BuildException
     */
    protected function generateXMLChecksum($lang = false)
    {
        var_dump('begin XML checksum');
        $xml_path = "$this->projectPath/build/prestashop_$this->version.xml";

        if ($lang !== false) {
            $xml_path = "$this->projectPath/build/prestashio_$this->version" . '_' . "$lang.xml";
        }
        $content = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".PHP_EOL;
        $content .= "<checksum_list>".PHP_EOL;
        $content .= "\t<ps_root_dir version=\"$this->version\">".PHP_EOL;
        $content .= $this->generateXMLDirectoryChecksum($this->filesList);
        $content .= "\t".'</ps_root_dir>'.PHP_EOL;
        $content .= '</checksum_list>'.PHP_EOL;


        if (!file_put_contents($xml_path, $content)) {
            throw new BuildException('Unable to generate XML checksum.');
        }
        var_dump('end XML checksum');

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

    /**
     * @return $this
     */
    protected function storePublication()
    {
        $reference = $this->version . "_" . date("Ymd_His");

//        if (!file_exists("$this->projectPath/build/publications")) {
//            mkdir("$this->projectPath/build/publications", 0777, true);
//        }
        mkdir("$this->projectPath/build/release/$reference", 0777, true);
        rename(
            "$this->projectPath/build/prestashop_$this->version.zip",
            "$this->projectPath/build/release/$reference/prestashop_$this->version.zip"
        );
//        rename(
//            "$this->projectPath/build/release/prestashop",
//            "$this->projectPath/build/publications/$reference/prestashop"
//        );
//        rename(
//            "$this->projectPath/build/release/prestashop_$this->version.xml",
//            "$this->projectPath/build/publications/$reference/prestashop_$this->version.xml"
//        );
//        rename(
//            "$this->projectPath/build/release/prestashop_$this->version.zip",
//            "$this->projectPath/build/publications/$reference/prestashop_$this->version.zip"
//        );
//
//        foreach ($this->packages as $iso_code => $lang) {
//            rename(
//                "$this->projectPath/build/release/prestashop_".$this->version."_".$iso_code.".xml",
//                "$this->projectPath/build/publications/$reference/prestashop_".$this->version."_".$iso_code.".xml"
//            );
//            rename(
//                "$this->projectPath/build/release/prestashop_".$this->version."_".$iso_code.".zip",
//                "$this->projectPath/build/publications/$reference/prestashop_".$this->version."_".$iso_code.".zip"
//            );
//        }

        return $this;
    }
}