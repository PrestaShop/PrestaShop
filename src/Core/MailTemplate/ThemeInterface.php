<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;

/**
 * Interface MailThemeInterface is used to define mail templates
 * themes. It is very simple for now (only a name) but it could evolve in
 * the future (include a config, a parent theme, ...)
 */
interface ThemeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return LayoutCollectionInterface
     */
    public function getLayouts();
}
