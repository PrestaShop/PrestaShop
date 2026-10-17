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
     * Key under which the JSON copy of theme.yml records what it was made from: the checksum of
     * the yml, so a yml that has changed since can be recognized, and the page layouts as the yml
     * declared them, so a value the merchant has since changed can be told apart from a default.
     *
     * ThemeManager::saveTheme() writes the whole attribute set back, so this entry survives a page
     * layout customization.
     */
    private const SOURCE_KEY = '_source';

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

        $ymlConf = $dir . '/config/theme.yml';
        $ymlChecksum = $this->filesystem->exists($ymlConf) ? (md5_file($ymlConf) ?: null) : null;
        $data = null;
        $customizedLayouts = [];

        if ($this->filesystem->exists($jsonConf)) {
            $cachedData = $this->getConfigFromFile($jsonConf);

            // The JSON file is a copy of theme.yml kept to skip the parsing, but the page layout
            // customization is saved into it as well, so it cannot just be dropped when it goes out
            // of date. Whatever replaced the theme's files - an update, a re-imported archive, a
            // deployment - left this copy behind, still advertising the version and the settings of
            // the theme that was installed. Rewrite it from theme.yml, and carry the customization
            // over.
            if (null === $ymlChecksum
                || (isset($cachedData[self::SOURCE_KEY]['checksum']) && $cachedData[self::SOURCE_KEY]['checksum'] === $ymlChecksum)
            ) {
                $data = $cachedData;
            } else {
                $customizedLayouts = $this->getCustomizedLayouts($cachedData);
            }
        }

        if (null === $data) {
            $data = $this->getConfigFromFile($ymlConf);
            $themeLayouts = $data['theme_settings']['layouts'] ?? [];

            // A layout the new theme.yml no longer offers cannot be rendered - getLayoutPath()
            // builds the template path from the name with no fallback - so the theme's own value is
            // kept for those pages.
            $availableLayouts = $data['meta']['available_layouts'] ?? [];
            foreach ($customizedLayouts as $page => $layout) {
                if (isset($availableLayouts[$layout])) {
                    $data['theme_settings']['layouts'][$page] = $layout;
                }
            }

            $data[self::SOURCE_KEY] = ['checksum' => $ymlChecksum, 'layouts' => $themeLayouts];

            // Write parsed yml data into json conf (faster parsing next time)
            $this->filesystem->dumpFile($jsonConf, json_encode($data));
        }

        $data['directory'] = $dir;

        return new Theme($data);
    }

    /**
     * Page layouts the merchant has changed, which is what the saved copy holds on top of the
     * layouts theme.yml declared when that copy was written. A copy written before this was
     * recorded has no baseline to compare against, so all of its layouts are kept.
     *
     * @param array<string, mixed> $cachedData
     *
     * @return array<string, string>
     */
    private function getCustomizedLayouts(array $cachedData): array
    {
        return array_diff_assoc(
            $cachedData['theme_settings']['layouts'] ?? [],
            $cachedData[self::SOURCE_KEY]['layouts'] ?? []
        );
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
