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

declare(strict_types=1);

namespace PrestaShopBundle\Command;

use Exception;
use Language;
use Module;
use PrestaShop\PrestaShop\Core\Translation\Export\TranslationCatalogueExporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Throwable;
use ZipArchive;

/**
 * Command to export module translations from command line
 */
#[AsCommand(
    name: 'prestashop:translation:export-module',
    description: 'Export module translations to XLF files'
)]
class ExportModuleTranslationsCommand extends Command
{
    public function __construct(
        private readonly TranslationCatalogueExporter $translationCatalogueExporter,
        private readonly Filesystem $filesystem,
        private readonly string $moduleDir,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to export module translations to XLF files that can be distributed with a module package. If no locale is specified, all available locales will be exported.')
            ->addArgument(
                'module',
                InputArgument::REQUIRED,
                'Technical name of the module to export translations for'
            )
            ->addArgument(
                'locale',
                InputArgument::OPTIONAL,
                'Locale code or ISO code for the translations (e.g., en-US, fr-FR, pl-PL, or just fr, en, pl). If not specified, all available locales will be exported.'
            )
            ->addOption(
                'auto-install',
                'a',
                InputOption::VALUE_NONE,
                'Automatically install the exported translations to the module\'s translations directory'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $moduleName = $input->getArgument('module');
        $localeOrIso = $input->getArgument('locale');
        $autoInstall = $input->getOption('auto-install');

        /** @var FormatterHelper $formatter */
        $formatter = $this->getHelper('formatter');

        try {
            $this->validateModule($moduleName);

            if (empty($localeOrIso)) {
                return $this->exportAllLocales($moduleName, $autoInstall, $output, $formatter);
            }

            return $this->exportSingleLocale($moduleName, $localeOrIso, $autoInstall, $output, $formatter);
        } catch (Exception $e) {
            $errorMsg = sprintf('An error occurred during export: %s', $e->getMessage());
            $formattedBlock = $formatter->formatBlock($errorMsg, 'error', true);
            $output->writeln($formattedBlock);

            return Command::FAILURE;
        }
    }

    private function exportAllLocales(string $moduleName, bool $autoInstall, OutputInterface $output, FormatterHelper $formatter): int
    {
        $availableLocales = $this->getAvailableLocales();

        $output->writeln(sprintf(
            '<info>Exporting translations for module "%s" in %d locales: %s</info>',
            $moduleName,
            count($availableLocales),
            implode(', ', $availableLocales)
        ));

        $exportedFiles = [];
        $failedLocales = [];

        foreach ($availableLocales as $locale) {
            try {
                $result = $this->performExport($moduleName, $locale, $autoInstall);
                $exportedFiles[] = $result;
                $output->writeln(sprintf('<comment>✓ Exported: %s</comment>', basename($result)));
            } catch (Exception $e) {
                $failedLocales[] = sprintf('%s (%s)', $locale, $e->getMessage());
                $output->writeln(sprintf('<error>✗ Failed %s: %s</error>', $locale, $e->getMessage()));
            }
        }

        // Summary
        if (!empty($exportedFiles)) {
            $successMsg = sprintf(
                'Successfully exported %d out of %d locales for module "%s"',
                count($exportedFiles),
                count($availableLocales),
                $moduleName
            );
            $formattedBlock = $formatter->formatBlock($successMsg, 'info', true);
            $output->writeln($formattedBlock);

            if (!empty($failedLocales)) {
                $output->writeln('<comment>Failed locales:</comment>');
                foreach ($failedLocales as $failed) {
                    $output->writeln(sprintf('  - %s', $failed));
                }
            }
        }

        return empty($exportedFiles) ? Command::FAILURE : Command::SUCCESS;
    }

    private function exportSingleLocale(string $moduleName, string $localeOrIso, bool $autoInstall, OutputInterface $output, FormatterHelper $formatter): int
    {
        $locale = $this->resolveLocale($localeOrIso);
        $this->validateLocale($locale);

        $output->writeln(sprintf(
            '<info>Exporting translations for module "%s" in locale "%s"...</info>',
            $moduleName,
            $locale
        ));

        try {
            $customZipFilePath = $this->performExport($moduleName, $locale, $autoInstall);

            $successMsg = sprintf(
                'Module translations have been exported successfully: %s',
                $customZipFilePath
            );
            $formattedBlock = $formatter->formatBlock($successMsg, 'info', true);
            $output->writeln($formattedBlock);

            if ($autoInstall) {
                $output->writeln('<info>Translations have been installed to the module directory.</info>');
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Performs the core export logic for a single locale
     *
     * @param string $moduleName
     * @param string $locale
     * @param bool $autoInstall
     *
     * @return string The path to the final exported file
     *
     * @throws Exception
     */
    private function performExport(string $moduleName, string $locale, bool $autoInstall): string
    {
        // Export the translations
        $zipFilePath = $this->exportModuleTranslations($moduleName, $locale);

        // Rename the file to include module name for better identification
        $customZipFilePath = $this->renameExportedFile($zipFilePath, $moduleName, $locale);

        // Auto-install if requested
        if ($autoInstall) {
            $this->autoInstallTranslations($customZipFilePath, $moduleName, $locale);
        }

        return $customZipFilePath;
    }

    private function exportModuleTranslations(string $moduleName, string $locale): string
    {
        $selections = [
            [
                'type' => 'modules',
                'selected' => $moduleName,
            ],
        ];

        return $this->translationCatalogueExporter->export($selections, $locale);
    }

    private function getAvailableLocales(): array
    {
        $languages = Language::getLanguages(false);
        $locales = [];

        foreach ($languages as $language) {
            if (!empty($language['locale'])) {
                $locales[] = $language['locale'];
            }
        }

        return $locales;
    }

    /**
     * Renames the exported zip file to include the module name for better identification
     *
     * @param string $originalPath
     * @param string $moduleName
     * @param string $locale
     *
     * @return string The new file path
     *
     * @throws Exception
     */
    private function renameExportedFile(string $originalPath, string $moduleName, string $locale): string
    {
        $directory = dirname($originalPath);
        $newFilename = sprintf('translations_export_%s_%s.zip', $moduleName, $locale);
        $newPath = $directory . DIRECTORY_SEPARATOR . $newFilename;

        if (!$this->filesystem->exists($originalPath)) {
            throw new Exception(sprintf('Original export file not found: %s', $originalPath));
        }

        $this->filesystem->rename($originalPath, $newPath, true);

        return $newPath;
    }

    /**
     * Validates that the specified module exists and uses the new translation system
     *
     * @param string $moduleName
     *
     * @throws Exception
     */
    private function validateModule(string $moduleName): void
    {
        $modulePath = $this->moduleDir . $moduleName;

        if (!$this->filesystem->exists($modulePath)) {
            throw new Exception(sprintf('Module "%s" does not exist in %s', $moduleName, $this->moduleDir));
        }

        // Check if module uses the new translation system (XLF files)
        try {
            $module = Module::getInstanceByName($moduleName);
            if (!$module || !$module->isUsingNewTranslationSystem()) {
                throw new Exception(sprintf(
                    'Module "%s" does not use the new translation system (XLF files). ' .
                    'Only modules using the new translation system can be exported with this command.',
                    $moduleName
                ));
            }
        } catch (Throwable $e) {
            throw new Exception(sprintf(
                'Could not validate module "%s": %s',
                $moduleName,
                $e->getMessage()
            ));
        }
    }

    /**
     * Resolves locale from ISO code or returns the locale as-is if it's already a full locale
     *
     * @param string $localeOrIso
     *
     * @return string
     *
     * @throws Exception
     */
    private function resolveLocale(string $localeOrIso): string
    {
        // If it's already a full locale (contains hyphen), return as-is
        if (strpos($localeOrIso, '-') !== false) {
            return $localeOrIso;
        }

        // Try to get locale by ISO code
        $locale = Language::getLocaleByIso($localeOrIso);

        if (!$locale) {
            throw new Exception(sprintf(
                'ISO code "%s" is not available in this PrestaShop installation. Please check that the language is available in your store.',
                $localeOrIso
            ));
        }

        return $locale;
    }

    /**
     * Validates that the specified locale exists in the system
     *
     * @param string $locale
     *
     * @throws Exception
     */
    private function validateLocale(string $locale): void
    {
        // Check if the locale exists in the PrestaShop language system
        $languageId = Language::getIdByLocale($locale, true);

        if (!$languageId) {
            throw new Exception(sprintf(
                'Locale "%s" is not available in this PrestaShop installation. Please check that the language is available in your store.',
                $locale
            ));
        }
    }

    /**
     * Automatically installs the exported translations to the module's directory
     *
     * @param string $zipFilePath
     * @param string $moduleName
     * @param string $locale
     *
     * @throws Exception
     */
    private function autoInstallTranslations(
        string $zipFilePath,
        string $moduleName,
        string $locale
    ): void {
        // Create a temporary directory for extraction
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'prestashop_translation_export_' . uniqid();
        $this->filesystem->mkdir($tempDir);

        try {
            $zip = new ZipArchive();
            if ($zip->open($zipFilePath) !== true) {
                throw new Exception('Failed to open the exported zip file');
            }

            $zip->extractTo($tempDir);
            $zip->close();

            $moduleTranslationsDir = $this->moduleDir . $moduleName . DIRECTORY_SEPARATOR . 'translations' . DIRECTORY_SEPARATOR . $locale;
            $this->filesystem->mkdir($moduleTranslationsDir);

            $finder = new Finder();
            $finder->files()->name('*.xlf')->in($tempDir);

            foreach ($finder as $file) {
                $targetFile = $moduleTranslationsDir . DIRECTORY_SEPARATOR . $file->getFilename();
                $this->filesystem->copy($file->getPathname(), $targetFile, true);
            }
        } finally {
            $this->filesystem->remove($tempDir);
        }
    }
}
