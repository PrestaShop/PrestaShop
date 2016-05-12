<?php
/**
 * 2007-2015 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Addon\AddonListFilter;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterType;
use PrestaShop\PrestaShop\Core\Addon\AddonListFilterStatus;
use PrestaShop\PrestaShop\Core\Addon\AddonRepositoryInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\Yaml\Parser;
use Shop;

class ThemeRepository implements AddonRepositoryInterface
{
    private $appConfiguration;
    private $shop;

    public function __construct(ConfigurationInterface $configuration, Shop $shop)
    {
        $this->appConfiguration = $configuration;
        $this->shop = $shop;
    }

    public function getInstanceByName($name)
    {
        $dir = $this->appConfiguration->get('_PS_ALL_THEMES_DIR_').$name;

        $jsonConfiguration = $this->appConfiguration->get('_PS_CONFIG_DIR_').'themes/'.$name.'/shop'.$this->shop->id.'.json';
        if (file_exists($jsonConfiguration)) {
            $data = $this->getConfigFromFile(
                $jsonConfiguration,
                $name
            );
        } else {
            $data = $this->getConfigFromFile(
                $dir.'/config/theme.yml',
                $name
            );
        }

        $data['directory'] = $dir;
        $data['physical_uri'] = $this->shop->physical_uri;

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
        $suffix = 'preview.png';
        $themeDirectories = glob($this->appConfiguration->get('_PS_ALL_THEMES_DIR_').'*/'.$suffix);

        $themes = [];
        foreach ($themeDirectories as $directory) {
            $name = basename(substr($directory, 0, -strlen($suffix)));
            $theme = $this->getInstanceByName($name);
            if (isset($theme)) {
                $themes[$name] = $theme;
            }
        }

        return $themes;
    }

    private function getConfigFromFile($file, $name)
    {
        if (!file_exists($file)) {
            throw new \PrestaShopException(sprintf('[ThemeRepository] Theme configuration file not found for theme `%s`.', $name));
        }

        $content = file_get_contents($file);

        if (preg_match('/.\.(yml|yaml)$/', $file)) {
            return (new Parser())->parse($content);
        } elseif (preg_match('/.\.json$/', $file)) {
            return json_decode($content, true);
        }
    }
}
