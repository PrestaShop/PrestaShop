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

        $corePath = $this->mailsDir . '/' . $isoCode . '/';
        if (is_dir($corePath)) {
            $templates = array_merge($templates, $this->scanDirectory($corePath, '', true));
        }

        $templates = array_merge($templates, $this->scanModuleTemplates($isoCode));

        return $templates;
    }

    private function scanDirectory(string $path, string $moduleName, bool $isCore = false): array
    {
        $templates = [];

        if (!is_dir($path)) {
            return $templates;
        }

        $finder = new Finder();
        $finder->files()->in($path)->depth(0)->name('*.html');

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $templateName = $file->getBasename('.html');

            if ($templateName[0] === '.') {
                continue;
            }

            $txtFile = $path . $templateName . '.txt';

            if (file_exists($txtFile)) {
                $templates[$templateName] = [
                    'module' => $moduleName,
                    'is_core' => $isCore,
                    'html_path' => $file->getPathname(),
                    'txt_path' => $txtFile,
                ];
            }
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
