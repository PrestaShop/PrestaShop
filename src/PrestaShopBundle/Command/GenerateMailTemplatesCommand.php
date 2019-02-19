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

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Employee;

class GenerateMailTemplatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestashop:mail:generate')
            ->setDescription('Generate mail templates for a specified theme')
            ->addArgument('theme', InputArgument::REQUIRED, 'Theme to use for mail templates.')
            ->addArgument('locale', InputArgument::REQUIRED, 'Which locale to use for the templates.')
            ->addArgument('coreOutputFolder', InputArgument::REQUIRED, 'Output folder to export core templates.')
            ->addArgument('modulesOutputFolder', InputArgument::OPTIONAL, 'Output folder to export modules templates (by default same as core).')
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
        $themeName = $input->getArgument('theme');
        $coreOutputFolder = $input->getArgument('coreOutputFolder');
        if (file_exists($coreOutputFolder)) {
            $coreOutputFolder = realpath($coreOutputFolder);
        }
        $modulesOutputFolder = $input->getArgument('modulesOutputFolder');
        if (null !== $modulesOutputFolder && file_exists($modulesOutputFolder)) {
            $modulesOutputFolder = realpath($modulesOutputFolder);
        } else {
            $modulesOutputFolder = $coreOutputFolder;
        }

        $this->initContext();

        $locale = $input->getArgument('locale');
        $language = $this->getLanguage($locale);

        /** @var ThemeCatalogInterface $themeCatalog */
        $themeCatalog = $this->getContainer()->get(ThemeCatalogInterface::class);
        $theme = $themeCatalog->getByName($themeName);

        $output->writeln(sprintf('Exporting mail with theme %s for language %s', $theme->getName(), $language->getName()));
        $output->writeln(sprintf('Core output folder: %s', $coreOutputFolder));
        $output->writeln(sprintf('Modules output folder: %s', $modulesOutputFolder));

        /** @var MailTemplateGenerator $catalog */
        $generator = $this->getContainer()->get('prestashop.core.mail_template.generator');
        $generator->generateTemplates($theme, $language, $coreOutputFolder, $modulesOutputFolder);
    }

    /**
     * Initialize PrestaShop Context
     */
    private function initContext()
    {
        require_once $this->getContainer()->get('kernel')->getRootDir() . '/../config/config.inc.php';
        /** @var LegacyContext $legacyContext */
        $legacyContext = $this->getContainer()->get('prestashop.adapter.legacy.context');
        //We need to have an employee or the module hooks don't work
        //see LegacyHookSubscriber
        if (!$legacyContext->getContext()->employee) {
            //Even a non existing employee is fine
            $legacyContext->getContext()->employee = new Employee(42);
        }
    }

    /**
     * @param string $locale
     *
     * @return LanguageInterface
     *
     * @throws InvalidArgumentException
     */
    private function getLanguage($locale)
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = $this->getContainer()->get('prestashop.core.admin.lang.repository');
        $language = $languageRepository->getByLocale($locale);
        if (!$language) {
            $localeParts = explode('-', $locale);
            $isoCode = strtolower($localeParts[0]);
            $language = $languageRepository->getByIsoCode($isoCode);
        }

        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Could not find Language for locale: %s', $locale));
        }

        return $language;
    }
}
