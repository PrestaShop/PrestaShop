<?php
/**
 * 2007-2015 PrestaShop
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
namespace PrestaShopBundle\Controller\Admin;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class ThemeController extends FrameworkBundleAdminController
{
    private function getContext()
    {
        return $this->get('prestashop.adapter.legacy.context')->getContext();
    }

    private function getShop()
    {
        return $this->getContext()->shop;
    }

    private function getThemesList()
    {
        $shop = $this->getShop();
        $conf = $this->get('prestashop.core.admin.configuration_interface');
        $themesDir = $conf->get('_PS_ALL_THEMES_DIR_');
        $themes = [];
        foreach (glob($themesDir.'*') as $entry) {
            if (is_dir($entry)) {
                $directory = basename($entry);
                $themes[] = [
                    'directory' => $directory,
                    'current'   => ($directory === $shop->theme_directory)
                ];
            }
        }

        return $themes;
    }

    public function getPages()
    {
        $theme = $this->getShop()->theme;

        $pages = $this->get('prestashop.adapter.data_provider.meta')->all(
            $this->getContext()
        );

        $availableLayouts = $theme['layouts'];

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

    /**
     * @Template
     */
    public function indexAction(Request $request)
    {
        $translator = $this->get('prestashop.adapter.translator');

        return [
            'layoutTitle' => $translator->trans('Theme Preferences'),
            'themes'      => $this->getThemesList(),
            'pages'       => $this->getPages()
        ];
    }

    public function changeAction(Request $request)
    {
        $themeDirectory = $request->request->get('theme-directory');

        $shop = $this->getShop();
        $shop->theme_directory = $themeDirectory;
        $shop->save();

        return $this->redirectToRoute('admin_theme');
    }

    public function changeLayoutAction(Request $request)
    {
        $layout = $request->request->get('layout');
        $theme  = $this->getShop()->theme;

        if (!isset($theme['page_preference'])) {
            $theme['page_preference'] = [];
        }

        foreach ($this->getPages() as $page) {
            if (!isset($theme['page_preference'][$page['page']])) {
                $theme['page_preference'][$page['page']] = [];
            }
        }

        foreach ($layout as $page => $layout) {
            $theme['page_preference'][$page]['layout'] = $layout;
        }

        $this->getShop()->setTheme($theme);

        return $this->redirectToRoute('admin_theme');
    }
}
