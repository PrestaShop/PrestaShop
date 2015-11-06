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

    public function getPagesSettings(
        PrestaShop\PrestaShop\Adapter\Meta\MetaDataProvider $meta,
        Context $context
    ) {
        $theme = $context->shop->theme;
        $availableLayouts = $theme['layouts'];
        $pages = $meta->all($context);

        $pagesWithLayout = array_map(function (array $page) use ($availableLayouts, $theme) {

            $page['layout'] = [];

            foreach ($availableLayouts as $layout) {

                $current = isset($theme['page_preference'][$page['page']]) &&
                    $theme['page_preference'][$page['page']]['layout'] === $layout['name']
                ;

                $page['layout'][$layout['name']] = [
                    'description' => $layout['description'],
                    'current'     => $current
                ];
            }

            return $page;
        }, $pages);

        // Sort pages by alphabetical order of title,
        // and by alphabetical order of page name for pages
        // that don't have a title
        usort($pagesWithLayout, function (array $a, array $b) {
            if ($a['title'] && $b['title']) {
                return $b['title'] < $a['title'] ? 1 : -1;
            } else if (!$a['title'] && !$b['title']) {
                return $b['page'] < $a['page'] ? 1 : -1;
            } else if ($b['title']) {
                return 1;
            } else {
                return -1;
            }
        });

        return $pagesWithLayout;
    }

    public function updateLayoutPreferences(
        PrestaShop\PrestaShop\Adapter\Meta\MetaDataProvider $meta,
        Context $context,
        array $layout
    ) {
        $theme  = $context->shop->theme;

        if (!isset($theme['page_preference'])) {
            $theme['page_preference'] = [];
        }

        foreach ($meta->all($context) as $page) {
            if (!isset($theme['page_preference'][$page['page']])) {
                $theme['page_preference'][$page['page']] = [];
            }
        }

        foreach ($layout as $page => $layout) {
            $theme['page_preference'][$page]['layout'] = $layout;
        }

        $context->shop->setTheme($theme);
    }
}
