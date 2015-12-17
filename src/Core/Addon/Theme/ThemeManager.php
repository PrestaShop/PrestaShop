<?php

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use \PrestaShop\PrestaShop\Core\ConfigurationInterface;

class ThemeManager
{
    private $all_themes_dir = '';
    private $configurator = null;
    private $shop = null;
    private $themes = [];

    public function __construct($all_themes_dir)
    {
        $this->all_themes_dir = $all_themes_dir;
        $this->setThemes();
    }

    private function setThemes()
    {
        $themes = [];
        $all_config_files = glob($this->all_themes_dir.'/*/config/theme.json');

        foreach ($all_config_files as $file) {
            $config = json_decode(file_get_contents($file));
            $themes[$config->directory] = $config;
        }

        $this->themes = $themes;
    }

    public function setShop(\Shop $shop)
    {
        $this->shop = $shop;
        return $this;
    }

    public function setConfigurator(ConfigurationInterface $configurator)
    {
        $this->$configurator = $configurator;
        return $this;
    }

    public function getThemes()
    {
        return $this->themes;
    }

    public function getFilteredThemes(array $to_be_removed = [])
    {
        $themes = $this->themes;
        foreach ($to_be_removed as $directory) {
            unset($themes[$directory]);
        }

        return $themes;
    }

    public function switchTheme(string $theme_dir)
    {
        $success = true;
        $theme = $this->themes[$theme_dir];

        $success &= $this->updateConfiguration($theme_dir);
        $success &= $this->updateModules($theme_dir);

        if ($success) {
            $this->shop->theme_directory = $theme_directory;
            ddd($this->shop);
            return $this->shop->save();
        } else {
            return false;
        }
    }

    private function updateConfiguration($theme_dir)
    {
        $success = true;
        $config_file = $this->all_themes_dir.$theme_dir.'/config/configuration.json';

        if (!file_exists($config_file)) {
            return true;
        }

        $theme_config = json_decode(file_get_contents($config_file), true);

        foreach ($theme_config as $key => $value) {
            $success &= $this->configurator->set($key, $value);
        }

        return $success;
    }

    private function updateModules($theme_dir)
    {
        return true;
    }
}
