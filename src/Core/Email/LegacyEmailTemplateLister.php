<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Email;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Scans and lists legacy email templates (HTML/TXT files) from /mails/{language}/ and /modules/{modulename}/mails/{language}/
 */
class LegacyEmailTemplateLister
{
    private string $mailsDir;
    private string $modulesDir;

    public function __construct(string $mailsDir = _PS_MAIL_DIR_, string $modulesDir = _PS_MODULE_DIR_)
    {
        $this->mailsDir = $mailsDir;
        $this->modulesDir = $modulesDir;
    }

    /**
     * @return array ['template_name' => ['module' => '', 'is_core' => true, ...], ...]
     */
    public function getLegacyTemplates(string $isoCode): array
    {
        $templates = [];

        $corePath = rtrim($this->mailsDir, '/') . '/' . $isoCode . '/';
        if (is_dir($corePath)) {
            $templates = $this->scanDirectory($corePath, '', true);
        }

        // Mail::getTemplateBasePath() searches the theme and core mail directories before it walks
        // the modules, so when a module ships a template of the same name the core one is what gets
        // sent. The union keeps that core entry instead of letting the module overwrite it.
        return $templates + $this->scanModuleTemplates($isoCode);
    }

    private function scanDirectory(string $path, string $moduleName, bool $isCore = false): array
    {
        $templates = [];

        if (!is_dir($path)) {
            return $templates;
        }

        $finder = new Finder();
        $finder->files()->in($path)->depth(0)->name(['*.html', '*.txt']);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $templateName = $file->getBasename('.' . $file->getExtension());

            if ($templateName === '' || $templateName[0] === '.') {
                continue;
            }

            if (isset($templates[$templateName])) {
                continue;
            }

            // A template counts as present when either half is, which is the rule
            // Mail::getTemplateBasePath() already applies; PS_MAIL_TYPE then decides which half
            // is required at send time, and TYPE_BOTH_AUTOMATIC_TEXT needs no .txt at all.
            $htmlFile = $path . $templateName . '.html';
            $txtFile = $path . $templateName . '.txt';

            $templates[$templateName] = [
                'module' => $moduleName,
                'is_core' => $isCore,
                'html_path' => is_file($htmlFile) ? $htmlFile : null,
                'txt_path' => is_file($txtFile) ? $txtFile : null,
            ];
        }

        return $templates;
    }

    private function scanModuleTemplates(string $isoCode): array
    {
        $templates = [];

        if (!is_dir($this->modulesDir)) {
            return $templates;
        }

        $moduleFinder = new Finder();
        $moduleFinder->directories()->in($this->modulesDir)->depth(0);

        /** @var SplFileInfo $moduleDir */
        foreach ($moduleFinder as $moduleDir) {
            $moduleName = $moduleDir->getFilename();
            $modulePath = $moduleDir->getPathname() . '/mails/' . $isoCode . '/';

            if (is_dir($modulePath)) {
                $moduleTemplates = $this->scanDirectory($modulePath, $moduleName, false);
                $templates = array_merge($templates, $moduleTemplates);
            }
        }

        return $templates;
    }
}
