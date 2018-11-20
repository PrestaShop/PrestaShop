<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonRepositoryInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;
use Shop;
use PrestaShopException;

class ThemeRepository implements AddonRepositoryInterface
{
    private $appConfiguration;
    private $filesystem;
    private $shop;

    public function __construct(ConfigurationInterface $configuration, Filesystem $filesystem, Shop $shop = null)
    {
        $this->appConfiguration = $configuration;
        $this->filesystem = $filesystem;
        $this->shop = $shop;
    }

    public function getInstanceByName($name)
    {
        $dir = $this->appConfiguration->get('_PS_ALL_THEMES_DIR_').$name;

        $jsonConf = '';
        if ($this->shop) {
            $jsonConf = $this->appConfiguration->get(
                    '_PS_CACHE_DIR_'
                ) . 'themes/' . $name . '/shop' . $this->shop->id . '.json';
        }

        if ($this->filesystem->exists($jsonConf)) {
            $data = $this->getConfigFromFile($jsonConf);
        } else {
            $data = $this->getConfigFromFile($dir.'/config/theme.yml');
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

    public function getListExcluding(array $exclude)
    {
        $filter = (new AddonListFilter())
            ->setExclude($exclude);

        return $this->getFilteredList($filter);
    }

    public function getFilteredList(AddonListFilter $filter)
    {
        $filter->setType(AddonListFilterType::THEME);

        if (!isset($filter->status)) {
            $filter->setStatus(AddonListFilterStatus::ALL);
        }

        $themes = $this->getThemesOnDisk();

        if (count($filter->exclude) > 0) {
            foreach ($filter->exclude as $name) {
                unset($themes[$name]);
            }
        }

        return $themes;
    }

    private function getThemesOnDisk()
    {
        $suffix = 'config/theme.yml';
        $themeDirectories = glob($this->appConfiguration->get('_PS_ALL_THEMES_DIR_').'*/'.$suffix);

        $themes = array();
        foreach ($themeDirectories as $directory) {
            $name = basename(substr($directory, 0, -strlen($suffix)));
            $theme = $this->getInstanceByName($name);
            if (isset($theme)) {
                $themes[$name] = $theme;
            }
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
