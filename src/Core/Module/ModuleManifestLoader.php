<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Module;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ModuleManifestLoader
{
    /**
     * @var string
     */
    private $moduleDirectory;

    /**
     * @var bool
     */
    private $loaded = false;

    /**
     * @var array
     */
    private $manifestValues = [];

    public function __construct(string $moduleDirectory)
    {
        $this->moduleDirectory = $moduleDirectory;

        $this->load();
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function getValues(): array
    {
        return [
            'bundles' => $this->manifestValues['bundles'],
        ];
    }

    private function load(): void
    {
        $manifestFile = static::file_exists_ci($this->moduleDirectory . DIRECTORY_SEPARATOR . 'manifest.php');
        if (false === $manifestFile) {
            return;
        }

        $manifestValues = include $manifestFile;
        if (false === $this->validateManifest($manifestValues)) {
            return;
        }

        $this->manifestValues['bundles'] = $manifestValues['bundles'];

        $this->loaded = true;
    }

    private function validateManifest(array $manifestValues): bool
    {
        if (!array_key_exists('bundles', $manifestValues)) {
            return false;
        }

        foreach ($manifestValues['bundles'] as $bundle) {
            if (!$bundle instanceof Bundle) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $file
     *
     * @return bool|string
     */
    private static function file_exists_ci(string $file)
    {
        if (file_exists($file)) {
            return $file;
        }

        $lowerfile = strtolower($file);

        foreach (glob(dirname($file) . '/*')  as $file) {
            if (strtolower($file) == $lowerfile) {
                return $file;
            }
        }

        return false;
    }
}
