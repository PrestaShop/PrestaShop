<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
     *  @var int Option defines that emails should be sent using configured SMTP settings
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
     * Class should not be initialized as its responsibility is to hold mail method options.
     */
    private function __construct()
    {
    }
}
