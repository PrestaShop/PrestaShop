<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\FeatureFlag\Layer;

use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\TypeLayerInterface;
use RuntimeException;

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
    public static function getTypeName(): string
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
        return isset($_ENV['SYMFONY_DOTENV_VARS'])
            && str_contains($_ENV['SYMFONY_DOTENV_VARS'], $this->getVarName($featureFlagName));
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled(string $featureFlagName): bool
    {
        return isset($_ENV[$this->getVarName($featureFlagName)])
            && filter_var($_ENV[$this->getVarName($featureFlagName)], \FILTER_VALIDATE_BOOLEAN);
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
        $filesToCheck = [".env.$env.local", ".env.$env", '.env', '.env.dist'];

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

        throw new RuntimeException(sprintf('Cannot change status of the feature flag %s', $featureFlagName));
    }

    /**
     * Get label in function of boolean.
     */
    private function boolLabel(bool $status): string
    {
        return $status ? 'true' : 'false';
    }
}
