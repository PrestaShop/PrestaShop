<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

/**
 * Interface MailTemplateInterface is used to contain the basic info about a mail template:
 */
interface MailTemplateInterface
{
    const CORE_CATEGORY = 'core';
    const MODULES_CATEGORY = 'modules';

    const HTML_TYPE = 'html';
    const RAW_TYPE = 'raw';

    /**
     * Whether the template is used by the core or modules
     *
     * @return string
     */
    public function getCategory();

    /**
     * There are two types of mail templates, either HTML or RAW ones
     *
     * @return string
     */
    public function getType();

    /**
     * Returns the extension of the generated file, either txt or html
     * @return string
     */
    public function getExtension();

    /**
     * Name of the template to describe its purpose
     *
     * @return string
     */
    public function getName();

    /**
     * Which theme this template is associated to
     *
     * @return string
     */
    public function getTheme();

    /**
     * Absolute path of the template file
     *
     * @return string
     */
    public function getPath();

    /**
     * Which module this template is associated to (if any)
     *
     * @return string|null
     */
    public function getModule();
}
