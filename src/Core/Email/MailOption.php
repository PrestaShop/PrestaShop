<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Email;

/**
 * Class MailMethodOption defines available email sending method options.
 */
final class MailOption
{
    /**
     * @var int Option defines that emails should be sent using native mail() function
     */
    public const METHOD_NATIVE = 1;

    /**
     * @var int Option defines that emails should be sent using configured SMTP settings
     */
    public const METHOD_SMTP = 2;

    /**
     * @var int Option defines that emails should not be sent
     */
    public const METHOD_NONE = 3;

    /**
     * @var int Option defines that emails should be sent in HTML format only
     */
    public const TYPE_HTML = 1;

    /**
     * @var int Option defines that emails should be sent in TXT format only
     */
    public const TYPE_TXT = 2;

    /**
     * @var int Option defines that emails should be sent in both HTML and TXT formats
     */
    public const TYPE_BOTH = 3;

    /**
     * @var int Option defines that emails should be sent in both HTML and TXT formats,
     *          with TXT format being automatically generated from HTML content
     */
    public const TYPE_BOTH_AUTOMATIC_TEXT = 4;

    /**
     * Class should not be initialized as its responsibility is to hold mail method options.
     */
    private function __construct()
    {
    }
}
