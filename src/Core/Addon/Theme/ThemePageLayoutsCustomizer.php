<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;

/**
 * Class PagesLayoutCustomizer customizes pages layout for shop's Front Office theme.
 */
final class ThemePageLayoutsCustomizer implements ThemePageLayoutsCustomizerInterface
{
    /**
     * @var Theme
     */
    private $theme;

    /**
     * @var ThemeManager
     */
    private $themeManager;

    /**
     * @var CacheClearerInterface
     */
    private $smartyCacheClearer;

    /**
     * @param Theme $theme
     * @param ThemeManager $themeManager
     * @param CacheClearerInterface $smartyCacheClearer
     */
    public function __construct(Theme $theme, ThemeManager $themeManager, CacheClearerInterface $smartyCacheClearer)
    {
        $this->theme = $theme;
        $this->themeManager = $themeManager;
        $this->smartyCacheClearer = $smartyCacheClearer;
    }

    /**
     * {@inheritdoc}
     */
    public function customize(array $pageLayouts)
    {
        $this->theme->setPageLayouts($pageLayouts);
        $this->themeManager->saveTheme($this->theme);

        $this->smartyCacheClearer->clear();
    }
}
