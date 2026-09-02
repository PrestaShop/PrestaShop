<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

/**
 * Interface LayoutInterface is used to contain the basic info about a mail layout.
 */
interface LayoutInterface
{
    /**
     * Name of the layout to describe its purpose
     *
     * @return string
     */
    public function getName();

    /**
     * Absolute path of the html layout file
     *
     * @return string
     */
    public function getHtmlPath();

    /**
     * Absolute path of the html layout file
     *
     * @return string
     */
    public function getTxtPath();

    /**
     * Which module this layout is associated to (if any)
     *
     * @return string|null
     */
    public function getModuleName();
}
