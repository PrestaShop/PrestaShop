<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Exception\EmailTemplateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\ValueObject\EmailTemplateSource;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Validate;

class EmailBodyTemplateRepository
{
    private readonly Filesystem $filesystem;

    public function __construct(
        private readonly string $coreMailsDir,
        private readonly string $modulesDir,
    ) {
        $this->filesystem = new Filesystem();
    }

    /**
     * @return array<int, array{template_name: string, source: string, module_name: string, has_html: bool, has_txt: bool}>
     */
    public function findAllForLocale(string $locale): array
    {
        $templates = [];

        $templates = array_merge($templates, $this->scanCoreTemplates($locale));
        $templates = array_merge($templates, $this->scanModuleTemplates($locale));

        usort($templates, static fn (array $a, array $b) => strcmp($a['template_name'], $b['template_name']));

        return $templates;
    }

    /**
     * @return array{html_content: string|null, txt_content: string|null}
     */
    public function findOne(string $name, string $locale, EmailTemplateSource $source): array
    {
        $dir = $this->getTemplateDir($locale, $source);

        $htmlPath = $dir . '/' . $name . '.html';
        $txtPath = $dir . '/' . $name . '.txt';

        if (!file_exists($htmlPath) && !file_exists($txtPath)) {
            throw new EmailTemplateNotFoundException(
                sprintf('Email template "%s" not found for locale "%s" in source "%s".', $name, $locale, $source->getSource())
            );
        }

        return [
            'html_content' => file_exists($htmlPath) ? file_get_contents($htmlPath) : null,
            'txt_content' => file_exists($txtPath) ? file_get_contents($txtPath) : null,
        ];
    }

    public function save(string $name, string $locale, EmailTemplateSource $source, string $htmlContent, string $txtContent): void
    {
        if (!Validate::isCleanHtml($htmlContent)) {
            throw new EmailTemplateConstraintException(
                sprintf('HTML content for email template "%s" contains invalid or unsafe content.', $name)
            );
        }

        $dir = $this->getTemplateDir($locale, $source);

        if (!is_dir($dir)) {
            throw new EmailTemplateNotFoundException(
                sprintf('Mail directory "%s" does not exist.', $dir)
            );
        }

        $htmlPath = $dir . '/' . $name . '.html';
        $txtPath = $dir . '/' . $name . '.txt';

        $htmlContent = str_replace("\r\n", PHP_EOL, $htmlContent);
        $txtContent = str_replace("\r\n", PHP_EOL, $txtContent);

        $this->filesystem->dumpFile($htmlPath, $htmlContent);
        $this->filesystem->dumpFile($txtPath, $txtContent);
    }

    private function getTemplateDir(string $locale, EmailTemplateSource $source): string
    {
        if ($source->isCore()) {
            return $this->coreMailsDir . '/' . $locale;
        }

        return $this->modulesDir . '/' . $source->getModuleName() . '/mails/' . $locale;
    }

    /**
     * @return array<int, array{template_name: string, source: string, module_name: string, has_html: bool, has_txt: bool}>
     */
    private function scanCoreTemplates(string $locale): array
    {
        $dir = $this->coreMailsDir . '/' . $locale;

        if (!is_dir($dir)) {
            return [];
        }

        return $this->scanDirectory($dir, EmailTemplateSource::SOURCE_CORE, '');
    }

    /**
     * @return array<int, array{template_name: string, source: string, module_name: string, has_html: bool, has_txt: bool}>
     */
    private function scanModuleTemplates(string $locale): array
    {
        $templates = [];

        if (!is_dir($this->modulesDir)) {
            return [];
        }

        $finder = new Finder();
        $finder->directories()->in($this->modulesDir)->depth(0);

        foreach ($finder as $moduleDir) {
            $mailDir = $moduleDir->getRealPath() . '/mails/' . $locale;

            if (!is_dir($mailDir)) {
                continue;
            }

            $moduleName = $moduleDir->getFilename();
            $templates = array_merge(
                $templates,
                $this->scanDirectory($mailDir, EmailTemplateSource::SOURCE_MODULE, $moduleName)
            );
        }

        return $templates;
    }

    /**
     * @return array<int, array{template_name: string, source: string, module_name: string, has_html: bool, has_txt: bool}>
     */
    private function scanDirectory(string $dir, string $source, string $moduleName): array
    {
        $templateMap = [];

        $finder = new Finder();
        $finder->files()->in($dir)->depth(0)->name(['*.html', '*.txt']);

        foreach ($finder as $file) {
            $extension = $file->getExtension();
            $name = $file->getBasename('.' . $extension);

            if (!isset($templateMap[$name])) {
                $templateMap[$name] = [
                    'template_name' => $name,
                    'source' => $source,
                    'module_name' => $moduleName,
                    'has_html' => false,
                    'has_txt' => false,
                ];
            }

            if ($extension === 'html') {
                $templateMap[$name]['has_html'] = true;
            } elseif ($extension === 'txt') {
                $templateMap[$name]['has_txt'] = true;
            }
        }

        return array_values($templateMap);
    }
}
