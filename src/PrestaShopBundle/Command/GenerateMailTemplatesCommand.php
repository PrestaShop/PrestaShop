<?php
/**
 * 2007-2018 PrestaShop.
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\Exception\InvalidException;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Language;

class GenerateMailTemplatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestashop:mail:generate')
            ->setDescription('Generate mail templates for a specified theme')
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme to use for mail templates.')
            ->addArgument('locale', InputArgument::REQUIRED, 'Which locale to use for the templates.')
            ->addArgument('outputFolder', InputArgument::REQUIRED, 'Output folder to export templates.')
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
        $theme = $input->getArgument('theme');
        $outputFolder = $input->getArgument('outputFolder');
        if (!file_exists($outputFolder)) {
            $outputFolder = implode(DIRECTORY_SEPARATOR, [getcwd(), $outputFolder]);
            if (file_exists($outputFolder)) {
                $outputFolder = realpath($outputFolder);
            }
        }

        $this->initContext();

        $locale = $input->getArgument('locale');
        $language = $this->getLanguage($locale);

        $output->writeln(sprintf('Exporting mail with theme %s for language %s to %s', $theme, $language->name, $outputFolder));
        /** @var MailTemplateGenerator $catalog */
        $generator = $this->getContainer()->get('prestashop.core.mail_template.generator');
        $generator->generateThemeTemplates($theme, $language, $outputFolder);
    }

    /**
     * Initialize PrestaShop Context
     */
    private function initContext()
    {
        require $this->getContainer()->get('kernel')->getRootDir() . '/../config/config.inc.php';
    }

    /**
     * @param string $locale
     *
     * @return Language
     * @throws InvalidException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function getLanguage($locale)
    {
        $iso = Language::getIsoByLocale($locale);
        if (false === $iso) {
            $localeParts = explode('-', $locale);
            $iso = $localeParts[0];
        }
        $languageId = Language::getIdByIso($iso);
        if (false === $languageId) {
            throw new InvalidException(sprintf('Could not find Language for locale: %s', $locale));
        }

        return new Language($languageId);
    }
}
