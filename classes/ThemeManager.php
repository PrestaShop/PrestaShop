<?php

class ThemeManager
{
    private $all_themes_dir;
    private $themes = [];

    public function __construct($all_themes_dir)
    {
        $this->all_themes_dir = $all_themes_dir;
        $this->setThemes();
    }

    public function setThemes()
    {
        $themes = [];
        $all_config_files = glob($this->all_themes_dir.'/*/config/theme.json');

        foreach ($all_config_files as $file) {
            $config = json_decode(file_get_contents($file));
            $themes[$config->directory] = $config;
        }

        $this->themes = $themes;
    }

    public function getThemes()
    {
        return $this->themes;
    }
}
