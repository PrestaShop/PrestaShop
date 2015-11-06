<?php

class ThemeManager
{
    private $all_themes_dir;
    private $themes = [];

    public function __construct($all_themes_dir)
    {
        $this->all_themes_dir = $all_themes_dir;
    }

    public function getThemes()
    {
        $themes = [];
        $all_config_files = glob($this->all_themes_dir.'/*/config/theme.json');

        foreach ($all_config_files as $file) {
            $config = json_decode(file_get_contents($file), true);
            $themes[$config['directory']] = $config;
        }

        return $themes;
    }
}
