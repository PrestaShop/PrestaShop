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

use PrestaShopBundle\License\AddLicenseOptions;
use PrestaShopBundle\License\AddLicenses;
use PrestaShopBundle\License\LicenseBuilder\OSLicenseStrategy;
use PrestaShopBundle\License\SymfonyIOAddLicensesLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;

class AddLicensesCommand extends Command
{
    private $updateLicenses;

    private $oldLicenses;

    private $licenseFolderToBrowse;

    public function __construct(AddLicenses $updateLicenses, array $oldLicenses, string $licenseFolderToBrowse)
    {
        parent::__construct();
        $this->updateLicenses = $updateLicenses;
        $this->oldLicenses = $oldLicenses;
        $this->licenseFolderToBrowse = $licenseFolderToBrowse;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('prestashop:licenses:add')
            ->setDescription('Add missing licenses on top of files')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Display missing licenses instead of insert them.')
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Clean up old licences and current license which are not at the beginning.')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->updateLicenses->execute(
            $this->getFinder(),
            new OSLicenseStrategy(),
            $logger = new SymfonyIOAddLicensesLogger($io),
            new AddLicenseOptions(
                $input->getOption('dry-run'),
                $input->getOption('clean'),
                $this->oldLicenses
            )
        );

        if ($input->getOption('clean')) {
            $io->note('tests/Unit/PrestaShopBundle/License/Specification/ folder should be checked manually in clean mode
because it contains licenses as specification and there are not at the beginning but it\'s normal! Then this
folder is excluded from the process');
        }

        if ($input->getOption('dry-run') and $logger->hasDryMessages()) {
            $logger->logDryMessages();

            return 1;
        }

        return 0;
    }

    private function getFinder(): Finder
    {
        return (new Finder())
            ->files()
            ->in($this->licenseFolderToBrowse)
            ->exclude([
                '.git',
                '.github',
                '.composer',
                'admin-dev/filemanager',
                'admin-dev/themes/default/public/',
                'admin-dev/themes/new-theme/public/',
                'js/tiny_mce',
                'js/jquery',
                'js/cropper',
                'mails/themes/classic/',
                'mails/themes/modern/',
                'tools/htmlpurifier',
                'vendor',
                'node_modules',
                'themes/classic/assets/',
                'themes/starterTheme/assets/',
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
                'modules/',
                'var/',
                'tests/Unit/PrestaShopBundle/License/Specification/',
            ])
            ->notPath([
                'install-dev/theme/js/sprintf.min.js',
                'install-dev/theme/js/zxcvbn.js',
                'admin-dev/themes/default/css/font.css',
                'admin-dev/themes/new-theme/package.json',
                'tools/build/Library/InstallUnpacker/content/js-runner.js',
                'themes/classic/_dev/package.json',
                'tools/build/composer.json',
            ])
            ->ignoreDotFiles(false)
            ->notName('composer.json')
            ->notName('package.json')
        ;
    }
}
