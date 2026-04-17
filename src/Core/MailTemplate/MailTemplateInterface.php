<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

/**
 * Interface MailTemplateInterface describe a mail template. A mail template
 * is a static file (in html or txt format) used by the Mail class to send
 * transactional emails.
 */
interface MailTemplateInterface
{
    public const CORE_CATEGORY = 'core';
    public const MODULES_CATEGORY = 'modules';

    public const HTML_TYPE = 'html';
    public const TXT_TYPE = 'txt';

    /**
     * Whether the template is used by the core or modules
     *
     * @return string
     */
    public function getCategory();

    /**
     * Whether the template is an html or txt type
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the absolute path to the template file.
     *
     * @return string
     */
    public function getPath();
}
