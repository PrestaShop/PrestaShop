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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-{currentYear} PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */";

    protected function configure()
    {
        $this
            ->setName('prestashop:licences:update')
            ->setDescription('Rewrite your licences to be up-to-date');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->text = str_replace('{currentYear}', date('Y'), $this->text);

        $this->findAndCheckExtension($output, 'php');
        $this->findAndCheckExtension($output, 'js');
        $this->findAndCheckExtension($output, 'css');
        $this->findAndCheckExtension($output, 'tpl');
        $this->findAndCheckExtension($output, 'html.twig');
    }

    private function findAndCheckExtension(OutputInterface $output, $ext)
    {
        $finder = new Finder();
        $finder
            ->files()
            ->name('*.'.$ext)
            ->in(_PS_ROOT_DIR_)
            ->exclude(array('.git', 'admin-dev/filemanager', 'js/tiny_mce', 'js/jquery', 'js/cropper',
                'modules', 'tests/resources/ModulesOverrideInstallUninstallTest', 'tools/htmlpurifier', 'vendor'))
            ->ignoreDotFiles(false);
        $parser = (new \PhpParser\ParserFactory)->create(\PhpParser\ParserFactory::PREFER_PHP7);

        $output->writeln('Updating license in '. strtoupper($ext).' files ...');
        $progress = new ProgressBar($output, count($finder));
        $progress->start();
        $progress->setRedrawFrequency(20);

        foreach ($finder as $file)
        {
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

    private function addLicenceToFile($file, $startDelimiter = '\/', $endDelimiter = '\/')
    {
        $content = $file->getContents();
        // Regular expression found thanks to Stephen Ostermiller's Blog. http://blog.ostermiller.org/find-comment
        $regex = '%'.$startDelimiter.'\*([^*]|[\r\n]|(\*+([^*'.$endDelimiter.']|[\r\n])))*\*+'.$endDelimiter.'%';
        $matches = array();
        $text = $this->text;
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
            $replace = "<?php\n".$this->text."\n";
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
                file_put_contents($file->getRelativePathname(), str_replace($comment->getText(), $this->text, $file->getContents()));
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
