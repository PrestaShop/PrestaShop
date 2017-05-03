<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Finder\Finder;

class UpdateLicencesCommand extends Command
{
    private $text = "/**
 * 2007-{currentYear} PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the {licenceName}
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * {licenceLink}
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
 * @copyright 2007-{currentYear} PrestaShop SA
 * @license   {licenceLink} {licenceName}
 * International Registered Trademark & Property of PrestaShop SA
 */";

    private $licence;

    private $aflLicence = array(
        'themes/classic/',
        'themes/starterTheme/',
        'modules/',
    );

    protected function configure()
    {
        $this
            ->setName('prestashop:licences:update')
            ->setDescription('Rewrite your licences to be up-to-date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->text = str_replace('{currentYear}', date('Y'), $this->text);

        $extensions = array(
            'php',
            'js',
            'css',
            'tpl',
            'html.twig',
        );

        foreach ($extensions as $extension) {
            $this->findAndCheckExtension($output, $extension);
        }
    }

    private function findAndCheckExtension(OutputInterface $output, $ext)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.'.$ext)
            ->in(_PS_ROOT_DIR_)
            ->exclude(array(
                '.git',
                '.github',
                '.composer',
                'admin-dev/filemanager',
                'js/tiny_mce',
                'js/jquery',
                'js/cropper',
                'tests/resources/ModulesOverrideInstallUninstallTest',
                'tools/htmlpurifier',
                'vendor',
                'node_modules',
                'themes/classic/assets/',
                'themes/starterTheme/assets/',
                'admin-dev/themes/default/public/',
                'admin-dev/themes/new-theme/public/',
            ))
            ->ignoreDotFiles(false);
        $parser = (new \PhpParser\ParserFactory)->create(\PhpParser\ParserFactory::PREFER_PHP7);

        $output->writeln('Updating license in '. strtoupper($ext).' files ...');
        $progress = new ProgressBar($output, count($finder));
        $progress->start();
        $progress->setRedrawFrequency(20);

        foreach ($finder as $file)
        {
            $this->licence = $this->text;
            $this->makeGoodLicence($file->getRelativePathname());

            switch ($file->getExtension()) {
                case 'php':
                    try {
                        $nodes = $parser->parse($file->getContents());
                        if (count($nodes)) {
                            $this->addLicenceToNode($nodes[0], $file);
                        }
                    } catch (\PhpParser\Error $exception) {
                        $output->writeln("Syntax error on file ". $file->getRelativePathname() .". Continue ...");
                    }
                    break;
                case 'js':
                case 'css':
                    $this->addLicenceToFile($file);
                    break;
                case 'tpl':
                    $this->addLicenceToSmartyTemplate($file);
                    break;
                case 'twig':
                    $this->addLicenceToTwigTemplate($file);
                    break;
            }
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
    }

    /**
     * @param $fileName
     */
    private function makeGoodLicence($fileName)
    {
        if ($this->isAFLLicence($fileName)) {
            $this->makeAFLLicence();
        } else {
            $this->makeOSLLicence();
        }
    }

    /**
     * @param $fileName
     * @return bool
     */
    private function isAFLLicence($fileName)
    {
        foreach ($this->aflLicence as $afl) {
            if (0 === strpos($fileName, $afl)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Replace for OSL licences
     */
    private function makeOSLLicence()
    {
        $this->licence = str_replace('{licenceName}', 'Open Software License (OSL 3.0)', $this->licence);
        $this->licence = str_replace('{licenceLink}', 'https://opensource.org/licenses/OSL-3.0', $this->licence);
    }

    /**
     * Replace for AFL licences
     */
    private function makeAFLLicence()
    {
        $this->licence = str_replace('{licenceName}', 'Academic Free License 3.0 (AFL-3.0)', $this->licence);
        $this->licence = str_replace('{licenceLink}', 'https://opensource.org/licenses/AFL-3.0', $this->licence);
    }

    private function addLicenceToFile($file, $startDelimiter = '\/', $endDelimiter = '\/')
    {
        $content = $file->getContents();
        // Regular expression found thanks to Stephen Ostermiller's Blog. http://blog.ostermiller.org/find-comment
        $regex = '%'.$startDelimiter.'\*([^*]|[\r\n]|(\*+([^*'.$endDelimiter.']|[\r\n])))*\*+'.$endDelimiter.'%';
        $matches = array();
        $text = $this->licence;
        if ($startDelimiter != '\/') {
            $text = $startDelimiter.ltrim($text, '/');
        }
        if ($endDelimiter != '\/') {
            $text = rtrim($text, '/').$endDelimiter;
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
            $content = $text."\n".$content;
        }

        file_put_contents($file->getRelativePathname(), $content);
    }

    private function addLicenceToNode($node, $file)
    {
        if (!$node->hasAttribute('comments')) {
            $needle = "<?php";
            $replace = "<?php\n".$this->licence."\n";
            $haystack = $file->getContents();

            $pos = strpos($haystack, $needle);
            // Important, if the <?php is in the middle of the file, continue
            if ($pos === 0) {
                $newstring = substr_replace($haystack,$replace,$pos,strlen($needle));
                file_put_contents($file->getRelativePathname(), $newstring);
            }

            return;
        }

        $comments = $node->getAttribute('comments');
        foreach ($comments as $comment) {
            if ($comment instanceof \PhpParser\Comment
                && strpos($comment->getText(), 'prestashop') !== false) {
                file_put_contents($file->getRelativePathname(), str_replace($comment->getText(), $this->licence, $file->getContents()));
            }
        }
    }

    private function addLicenceToSmartyTemplate($file)
    {
        $this->addLicenceToFile($file, '{', '}');
    }

    private function addLicenceToTwigTemplate($file)
    {
        if (strrpos($file->getRelativePathName(), 'html.twig') !== false) {
            $this->addLicenceToFile($file, '{#', '#}');
        }
    }
}
