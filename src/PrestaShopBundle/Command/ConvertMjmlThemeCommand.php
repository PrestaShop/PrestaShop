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

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Adapter\MailTemplate\Mjml\TwigTemplateConverter;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ConvertMjmlThemeCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestashop:mail:convert-mjml')
            ->setDescription('Convert an MJML theme to a twig theme')
            ->addArgument('mjmlTheme', InputArgument::REQUIRED, 'MJML theme to convert.')
            ->addArgument('twigTheme', InputArgument::REQUIRED, 'Target twig theme where files are converted.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mjmlTheme = $input->getArgument('mjmlTheme');
        $twigTheme = $input->getArgument('twigTheme');

        $mailThemesDir = $this->getContainer()->getParameter('mail_themes_dir');

        $mjmlThemeFolder = $mailThemesDir.'/'.$mjmlTheme;
        if (!is_dir($mjmlThemeFolder)) {
            throw new FileNotFoundException(sprintf('Could not find theme folder %s', $mjmlThemeFolder));
        }

        /** @var TwigTemplateConverter $converter */
        $converter = $this->getContainer()->get('prestashop.adapter.mail_template.mjml.twig_template_converter');

        $fileSystem = new Filesystem();
        $finder = new Finder();
        $finder->files()->name('*.mjml.twig')->in($mjmlThemeFolder);
        /** @var SplFileInfo $mjmlFile */
        foreach ($finder as $mjmlFile) {
            //Ignore components file for now
            if (preg_match('/^components/', $mjmlFile->getRelativePathname())) {
                if ('components/layout.mjml.twig' == $mjmlFile->getRelativePathname()) {
                    $output->writeln('Converting layout '.$mjmlFile->getRelativePathname());
                    $twigTemplate = $converter->convertLayoutTemplate($mjmlFile->getRealPath(), $mjmlTheme, $twigTheme);
                } else {
                    $output->writeln('Converting component '.$mjmlFile->getRelativePathname());
                    $twigTemplate = $converter->convertComponentTemplate($mjmlFile->getRealPath(), $mjmlTheme);
                }
            } else {
                $output->writeln('Converting template '.$mjmlFile->getRelativePathname());
                $twigTemplate = $converter->convertChildTemplate($mjmlFile->getRealPath(), $twigTheme);
            }

            $twigTemplatePath = $mailThemesDir.'/'.$twigTheme.'/'.$mjmlFile->getRelativePathname();
            $twigTemplatePath = preg_replace('/mjml\.twig/', 'html.twig', $twigTemplatePath);
            $twigTemplateFolder = dirname($twigTemplatePath);
            if (!$fileSystem->exists($twigTemplateFolder)) {
                $fileSystem->mkdir($twigTemplateFolder);
            }

            file_put_contents($twigTemplatePath, $twigTemplate);
        }
    }
}
