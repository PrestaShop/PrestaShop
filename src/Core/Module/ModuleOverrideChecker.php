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

            if (file_exists($existingOverrideFile) && file_exists($moduleOverrideFile)) {
                $moduleOverrideContent = file_get_contents($moduleOverrideFile);
                $existingOverrideContent = file_get_contents($existingOverrideFile);
                if (
                    $this->hasConflictingMethod($moduleOverrideContent, $existingOverrideContent)
                    || $this->hasConflictingProperty($moduleOverrideContent, $existingOverrideContent)
                    || $this->hasConflictingConstant($moduleOverrideContent, $existingOverrideContent)
                ) {
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

    private function hasConflictingProperty(string $moduleOverrideContent, string $existingOverrideContent): bool
    {
        $moduleOverrideProperties = $this->getClassPropertiesFromContent($moduleOverrideContent);
        $existingOverrideProperties = $this->getClassPropertiesFromContent($existingOverrideContent);

        foreach ($moduleOverrideProperties as $property) {
            if (in_array($property, $existingOverrideProperties)) {
                return true;
            }
        }

        return false;
    }

    private function hasConflictingConstant(string $moduleOverrideContent, string $existingOverrideContent): bool
    {
        $moduleOverrideConstants = $this->getClassConstantsFromContent($moduleOverrideContent);
        $existingOverrideConstants = $this->getClassConstantsFromContent($existingOverrideContent);

        foreach ($moduleOverrideConstants as $constants) {
            if (in_array($constants, $existingOverrideConstants)) {
                return true;
            }
        }

        return false;
    }

    /*
     * Checks if a module override class has a method that is already overridden by another module
     */
    private function hasConflictingMethod(string $moduleOverrideContent, string $existingOverrideContent): bool
    {
        $moduleMethods = $this->getClassMethodsFromContent($moduleOverrideContent);
        $existingOverrideMethods = $this->getClassMethodsFromContent($existingOverrideContent);

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
        $methodPattern = '/(public|private|protected)\s+function\s+(\w+)/';

        $methods = [];

        if (preg_match_all($methodPattern, $content, $matches)) {
            $methods = $matches[2];
        }

        return $methods;
    }

    /*
    * This function parses php file content and gets a list of properties from its content.
    */
    private function getClassPropertiesFromContent(string $content): array
    {
        $propertyPattern = '/(public|private|protected)\s+\$(\w+)/';
        $properties = [];

        if (preg_match_all($propertyPattern, $content, $matches)) {
            $properties = $matches[2];
        }

        return $properties;
    }

    /*
    * This function parses php file content and gets a list of properties from its content.
    */
    private function getClassConstantsFromContent(string $content): array
    {
        $constantPattern = '/const\s+(\w+)/';

        $constants = [];

        if (preg_match_all($constantPattern, $content, $matches)) {
            $constants = $matches[1];
        }

        return $constants;
    }
}
