<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonRepositoryInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopException;
use Shop;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class ThemeRepository implements AddonRepositoryInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $appConfiguration;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var Shop|null
     */
    private $shop;
    /**
     * @var array|null
     */
    public $themes;

    public function __construct(ConfigurationInterface $configuration, Filesystem $filesystem, ?Shop $shop = null)
    {
        $this->appConfiguration = $configuration;
        $this->filesystem = $filesystem;
        $this->shop = $shop;
    }

    /**
     * @param string $name
     *
     * @return Theme
     *
     * @throws PrestaShopException
     */
    public function getInstanceByName($name)
    {
        $dir = $this->appConfiguration->get('_PS_ALL_THEMES_DIR_') . $name;

        $confDir = $this->appConfiguration->get('_PS_CONFIG_DIR_') . 'themes/' . $name;
        $jsonConf = $confDir . '/theme.json';
        if ($this->shop) {
            $jsonConf = $confDir . '/shop' . $this->shop->id . '.json';
        }

        if ($this->filesystem->exists($jsonConf)) {
            $data = $this->getConfigFromFile($jsonConf);
        } else {
            $data = $this->getConfigFromFile($dir . '/config/theme.yml');

            // Write parsed yml data into json conf (faster parsing next time)
            $this->filesystem->dumpFile($jsonConf, json_encode($data));
        }

        $data['directory'] = $dir;

        return new Theme($data);
    }

    public function getList()
    {
        if (!isset($this->themes)) {
            $this->themes = $this->getFilteredList(new AddonListFilter());
        }

        return $this->themes;
    }

    /**
     * Gets list of themes as a collection.
     *
     * @return ThemeCollection
     */
    public function getListAsCollection()
    {
        $list = $this->getList();

        return ThemeCollection::createFrom($list);
    }

    public function getListExcluding(array $exclude)
    {
        $filter = (new AddonListFilter())
            ->setExclude($exclude);

        return $this->getFilteredList($filter);
    }

    public function getFilteredList(AddonListFilter $filter)
    {
        $filter->setType(AddonListFilterType::THEME);

        if (empty($filter->status)) {
            $filter->setStatus(AddonListFilterStatus::ALL);
        }

        $themes = $this->getThemesOnDisk();

        foreach ($filter->exclude as $name) {
            unset($themes[$name]);
        }

        return $themes;
    }

    private function getThemesOnDisk()
    {
        $suffix = 'config/theme.yml';
        $themeDirectories = glob($this->appConfiguration->get('_PS_ALL_THEMES_DIR_') . '*/' . $suffix, GLOB_NOSORT);

        $themes = [];
        foreach ($themeDirectories as $directory) {
            $name = basename(substr($directory, 0, -strlen($suffix)));
            $themes[$name] = $this->getInstanceByName($name);
        }

        return $themes;
    }

    private function getConfigFromFile($file)
    {
        if (!$this->filesystem->exists($file)) {
            throw new PrestaShopException(sprintf('[ThemeRepository] Theme configuration file not found for theme at `%s`.', $file));
        }

        $content = file_get_contents($file);

        if (preg_match('/.\.(yml|yaml)$/', $file)) {
            return (new Parser())->parse($content);
        } elseif (preg_match('/.\.json$/', $file)) {
            return json_decode($content, true);
        }
    }
}
