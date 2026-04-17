<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Theme;

use PrestaShop\PrestaShop\Core\Util\ArrayFinder;
use Symfony\Component\Yaml\Parser;

class ConfigReader implements ConfigReaderInterface
{
    public const DEFAULT_CONFIGURATION_THEME = [
        'display_name' => 'N/A',
        'version' => 'N/A',
        'preview' => 'themes/preview-fallback.png',
    ];

    /**
     * @var string
     */
    protected $themesDirectoryPath;

    public function __construct(string $themesDirectoryPath)
    {
        $this->themesDirectoryPath = $themesDirectoryPath;
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $name): ?ArrayFinder
    {
        $configFile = $this->findConfigFile($name);
        if ($configFile === null) {
            return null;
        }

        $themeData = (new Parser())->parse(file_get_contents($configFile));

        if (file_exists($this->themesDirectoryPath . $name . '/preview.png')) {
            $themeData['preview'] = 'themes/' . $name . '/preview.png';
        }
        $themeData = array_merge(self::DEFAULT_CONFIGURATION_THEME, $themeData);

        return new ArrayFinder($themeData);
    }

    /**
     * Find config file depending on the iso code.
     *
     * @param string $name The module name
     *
     * @return string|null
     */
    protected function findConfigFile(string $name): ?string
    {
        $configFile = $this->themesDirectoryPath . $name . '/config/theme.yml';

        if (!file_exists($configFile)) {
            return null;
        }

        return $configFile;
    }
}
