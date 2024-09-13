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

namespace PrestaShop\PrestaShop\Core\Module;

use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModuleOverrideChecker
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @var string
     */
    private $psOverrideDir;

    public function __construct(TranslatorInterface $translator, string $psOverrideDir)
    {
        $this->translator = $translator;
        $this->psOverrideDir = $psOverrideDir;
    }

    public function hasOverrideConflict(string $moduleOverridePath): bool
    {
        // if module doesn't have an override folder, there is no conflict
        if (!is_dir($moduleOverridePath)) {
            return false;
        }

        $finder = new Finder();
        $finder->files()->in($moduleOverridePath)->name('*.php');

        // The module's override folder doesn't contain any override, return false
        if (!$finder->hasResults()) {
            return false;
        }

        $fileList = [];
        foreach ($finder as $file) {
            $fileList[] = $file->getRelativePathname();
        }

        // module has overrides, let's check override files one by one
        foreach ($fileList as $file) {
            $moduleOverrideFile = $moduleOverridePath . DIRECTORY_SEPARATOR . $file;
            $existingOverrideFile = $this->psOverrideDir . $file;

            if (file_exists($existingOverrideFile)) {
                if ($this->hasConflictingMethod($moduleOverrideFile, $existingOverrideFile)) {
                    $this->errors[] = $this->translator->trans(
                        'The override file %1$s conflicts with an existing override in %2$s.',
                        [$moduleOverrideFile, $existingOverrideFile],
                        'Admin.Modules.Notification'
                    );
                }
            }
        }

        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    /*
     * Checks if a module override class has a method that is already overridden by another module
     */
    private function hasConflictingMethod(string $moduleOverridePath, string $existingOverridePath): bool
    {
        $moduleMethods = $this->getClassMethodsFromContent(file_get_contents($moduleOverridePath));
        $existingOverrideMethods = $this->getClassMethodsFromContent(file_get_contents($existingOverridePath));

        foreach ($moduleMethods as $method) {
            if (in_array($method, $existingOverrideMethods)) {
                return true;
            }
        }

        return false;
    }

    /*
     * This function parses php file content and gets a list of methods from its content.
     */
    private function getClassMethodsFromContent(string $content): array
    {
        // Get the list of tokens, see https://www.php.net/manual/en/function.token-get-all.php
        $tokens = token_get_all($content);
        $methods = [];
        foreach ($tokens as $index => $token) {
            $methodName = null;
            // filter method definitions
            if (is_array($token) && $token[0] === T_FUNCTION) {
                // This loop is necessary in case some additional spaces exist before the function name
                for ($i = $index + 1; $i < count($tokens); ++$i) {
                    if (is_array($tokens[$i]) && $tokens[$i][0] === T_STRING) {
                        $methodName = $tokens[$i][1];
                        break;
                    }
                }

                // Collect method name
                if ($methodName) {
                    $methods[] = $methodName; // Save method name
                }
            }
        }

        return $methods;
    }
}
