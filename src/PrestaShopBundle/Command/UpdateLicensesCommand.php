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

namespace PrestaShopBundle\Command;

use PhpParser\Node\Stmt;
use PhpParser\ParserFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpdateLicensesCommand extends Command
{
    private $text = '/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the {licenseName}
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * {licenseLink}
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
 * @license   {licenseLink} {licenseName}
 */';

    /**
     * @var string
     */
    private $license;

    private $aflLicense = [
        'themes/classic/',
        'themes/StarterTheme/',
        'modules/',
    ];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:licenses:update')
            ->setDescription('Rewrite your licenses to be up-to-date');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->text = str_replace('{currentYear}', date('Y'), $this->text);

        $extensions = [
            'php',
            'js',
            'css',
            'tpl',
            'html.twig',
            'json',
            'vue',
        ];

        foreach ($extensions as $extension) {
            $this->findAndCheckExtension($output, $extension);
        }

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @param string $ext
     */
    private function findAndCheckExtension(OutputInterface $output, $ext)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.' . $ext)
            ->in(_PS_ROOT_DIR_)
            // Ignore folders
            ->exclude([
                // versioning folders
                '.git',
                '.github',
                '.composer',
                // admin folders
                'admin-dev/filemanager',
                'admin-dev/themes/default/public/',
                'admin-dev/themes/new-theme/public/',
                // js dependencies
                'js/tiny_mce',
                'js/jquery',
                'js/cropper',
                // mails folder
                'mails/themes/classic/',
                'mails/themes/modern/',
                // tools dependencies
                'tools/htmlpurifier',
                // dependencies
                'vendor',
                'node_modules',
                // themes assets
                'themes/classic/assets/',
                'themes/starterTheme/assets/',
                // tests folders
                'tests/Resources/modules/',
                'tests/Resources/modules_tests/override/',
                'tests/Resources/themes/',
                'tests/Resources/translations/',
                'tests/Resources/ModulesOverrideInstallUninstallTest/',
                'tests/E2E/',
                'tests/Unit/Resources/config/',
                'tests/Unit/Resources/assets/',
                'tests/Unit/Resources/twig/',
                'tests/UI/',
            ])
            // Ignore specific files
            ->notPath([
                // install
                'install-dev/theme/js/sprintf.min.js',
                'install-dev/theme/js/zxcvbn.js',
            ])
            ->ignoreDotFiles(false);
        $parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);

        $output->writeln('Updating license in ' . strtoupper($ext) . ' files ...');
        $progress = new ProgressBar($output, count($finder));
        $progress->start();
        $progress->setRedrawFrequency(20);

        $filesToIgnore = [
            'composer.json',
            'package.json',
            'admin-dev/themes/default/css/font.css',
            'admin-dev/themes/new-theme/package.json',
            'tools/build/Library/InstallUnpacker/content/js-runner.js',
            'themes/classic/_dev/package.json',
            'tools/build/composer.json',
        ];

        foreach ($finder as $file) {
            $this->license = $this->text;
            $this->makeGoodLicense($file);

            if (in_array($file->getRelativePathName(), $filesToIgnore)) {
                continue;
            }

            switch ($file->getExtension()) {
                case 'php':
                    try {
                        $nodes = $parser->parse($file->getContents());
                        if (count($nodes)) {
                            $this->addLicenseToNode($nodes[0], $file);
                        }
                    } catch (\PhpParser\Error $exception) {
                        $output->writeln('Syntax error on file ' . $file->getRelativePathname() . '. Continue ...');
                    }

                    break;
                case 'js':
                case 'css':
                    $this->addLicenseToFile($file);

                    break;
                case 'tpl':
                    $this->addLicenseToSmartyTemplate($file);

                    break;
                case 'twig':
                    $this->addLicenseToTwigTemplate($file);

                    break;
                case 'json':
                    $this->addLicenseToJsonFile($file);

                    break;
                case 'vue':
                    $this->addLicenseToHtmlFile($file);

                    break;
            }
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
    }

    /**
     * @param SplFileInfo $file
     */
    private function makeGoodLicense(SplFileInfo $file)
    {
        if ($this->isAFLLicense($file->getRelativePathname())) {
            $this->makeAFLLicense();
        } else {
            $this->makeOSLLicense();
        }
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    private function isAFLLicense($fileName)
    {
        foreach ($this->aflLicense as $afl) {
            if (str_starts_with($fileName, $afl)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace for OSL licenses.
     */
    private function makeOSLLicense()
    {
        $this->license = str_replace('{licenseName}', 'Open Software License (OSL 3.0)', $this->license);
        $this->license = str_replace('{licenseLink}', 'https://opensource.org/licenses/OSL-3.0', $this->license);
    }

    /**
     * Replace for AFL licenses.
     */
    private function makeAFLLicense()
    {
        $this->license = str_replace('{licenseName}', 'Academic Free License 3.0 (AFL-3.0)', $this->license);
        $this->license = str_replace('{licenseLink}', 'https://opensource.org/licenses/AFL-3.0', $this->license);
    }

    /**
     * @param SplFileInfo $file
     * @param string $startDelimiter
     * @param string $endDelimiter
     */
    private function addLicenseToFile($file, $startDelimiter = '\/', $endDelimiter = '\/')
    {
        $content = $file->getContents();
        // Regular expression found thanks to Stephen Ostermiller's Blog. http://blog.ostermiller.org/find-comment
        $regex = '%' . $startDelimiter . '\*([^*]|[\r\n]|(\*+([^*' . $endDelimiter . ']|[\r\n])))*\*+' . $endDelimiter . '%';
        $matches = [];
        $text = $this->license;
        if ($startDelimiter != '\/') {
            $text = $startDelimiter . ltrim($text, '/');
        }
        if ($endDelimiter != '\/') {
            $text = rtrim($text, '/') . $endDelimiter;
        }

        // Try to find an existing license
        preg_match($regex, $content, $matches);

        if (count($matches)) {
            // Found - Replace it if prestashop one
            foreach ($matches as $match) {
                if (stripos($match, 'prestashop') !== false) {
                    $content = str_replace($match, $text, $content);
                }
            }
        } else {
            // Not found - Add it at the beginning of the file
            $content = $text . "\n" . $content;
        }

        file_put_contents($file->getRelativePathname(), $content);
    }

    /**
     * @param Stmt $node
     * @param SplFileInfo $file
     */
    private function addLicenseToNode($node, SplFileInfo $file)
    {
        if (!$node->hasAttribute('comments')) {
            $needle = '<?php';
            $replace = "<?php\n" . $this->license . "\n";
            $haystack = $file->getContents();

            $pos = strpos($haystack, $needle);
            // Important, if the <?php is in the middle of the file, continue
            if ($pos === 0) {
                $newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
                file_put_contents($file->getRelativePathname(), $newstring);
            }

            return;
        }

        $comments = $node->getAttribute('comments');
        foreach ($comments as $comment) {
            if ($comment instanceof \PhpParser\Comment
                && str_contains($comment->getText(), 'prestashop')) {
                file_put_contents($file->getRelativePathname(), str_replace($comment->getText(), $this->license, $file->getContents()));
            }
        }
    }

    /**
     * @param SplFileInfo $file
     */
    private function addLicenseToSmartyTemplate(SplFileInfo $file)
    {
        $this->addLicenseToFile($file, '{', '}');
    }

    /**
     * @param SplFileInfo $file
     */
    private function addLicenseToTwigTemplate(SplFileInfo $file)
    {
        if (strrpos($file->getRelativePathName(), 'html.twig') !== false) {
            $this->addLicenseToFile($file, '{#', '#}');
        }
    }

    /**
     * @param SplFileInfo $file
     */
    private function addLicenseToHtmlFile(SplFileInfo $file)
    {
        $this->addLicenseToFile($file, '<!--', '-->');
    }

    /**
     * @param SplFileInfo $file
     *
     * @return bool
     */
    private function addLicenseToJsonFile(SplFileInfo $file)
    {
        if (!in_array($file->getFilename(), ['composer.json', 'package.json'])) {
            return false;
        }

        $content = (array) json_decode($file->getContents());
        $content['author'] = 'PrestaShop';
        $content['license'] = $this->isAFLLicense($file->getRelativePathname()) ? 'AFL-3.0' : 'OSL-3.0';

        return file_put_contents($file->getRelativePathname(), json_encode($content, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}
