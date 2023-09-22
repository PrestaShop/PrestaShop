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

namespace PrestaShop\PrestaShop\Core\FeatureFlag\Layer;

use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\TypeLayerInterface;

class DotEnvLayer implements TypeLayerInterface
{
    public function __construct(
        private EnvironmentInterface $environment,
        private string $rootDir,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isReadonly(): bool
    {
        // It's always editable via DotEnv layer!
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeName(): string
    {
        return FeatureFlagSettings::TYPE_DOTENV;
    }

    /**
     * Retrieve the variable name of this feature flag.
     */
    public function getVarName(string $featureFlagName): string
    {
        return FeatureFlagSettings::PREFIX . strtoupper($featureFlagName);
    }

    /**
     * {@inheritdoc}
     */
    public function canBeUsed(string $featureFlagName): bool
    {
        return isset($_ENV['SYMFONY_DOTENV_VARS']) &&
            str_contains($_ENV['SYMFONY_DOTENV_VARS'], $this->getVarName($featureFlagName));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(string $featureFlagName): bool
    {
        return isset($_ENV[$this->getVarName($featureFlagName)]) &&
            filter_var($_ENV[$this->getVarName($featureFlagName)], \FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * {@inheritdoc}
     */
    public function enable(string $featureFlagName): void
    {
        $this->setStatus($featureFlagName, true);
    }

    /**
     * {@inheritdoc}
     */
    public function disable(string $featureFlagName): void
    {
        $this->setStatus($featureFlagName, false);
    }

    /**
     * Retrieve dotenv file used with this feature flag set.
     */
    private function locateDotEnvFile(string $featureFlagName): string
    {
        $env = $this->environment->getName();
        $filesToCheck = [".env.$env.local", ".env.$env", '.env'];

        foreach ($filesToCheck as $file) {
            $path = $this->rootDir . '/' . $file;
            if (file_exists($path) && str_contains(file_get_contents($path), $this->getVarName($featureFlagName))) {
                return $path;
            }
        }

        return '';
    }

    /**
     * Set feature flag status in the good dotenv file.
     */
    private function setStatus(string $featureFlagName, bool $status): void
    {
        if ($pathDotenv = $this->locateDotEnvFile($featureFlagName)) {
            file_put_contents(
                $pathDotenv,
                preg_replace(
                    "/({$this->getVarName($featureFlagName)})=(.*)/",
                    "$1={$this->boolLabel($status)}",
                    file_get_contents($pathDotenv)
                )
            );

            return;
        }

        throw new \RuntimeException(sprintf('Cannot change status of the feature flag %s', $featureFlagName));
    }

    /**
     * Get label in function of boolean.
     */
    private function boolLabel(bool $status): string
    {
        return $status ? 'true' : 'false';
    }
}
