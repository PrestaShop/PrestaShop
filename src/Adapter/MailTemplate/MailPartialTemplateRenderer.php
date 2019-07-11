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

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use Context as LegacyContext;
use Tools;

/**
 * Class MailPartialTemplateRenderer renders partial mail templates (especially for order). This
 * feature was moved in this service so that it can be shared between PaymentModule and MailPreviewVariablesBuilder.
 */
class MailPartialTemplateRenderer
{
    /** @var LegacyContext */
    private $context;

    /**
     * @param LegacyContext $context
     */
    public function __construct(LegacyContext $context)
    {
        $this->context = $context;
    }

    /**
     * Fetch the content of $partialTemplateName inside the folder
     * current_theme/mails/current_iso_lang/ if found, otherwise in
     * mails/current_iso_lang.
     *
     * @param string $partialTemplateName template name with extension
     * @param array $variables sent to smarty as 'list'
     *
     * @return string
     */
    public function render($partialTemplateName, array $variables)
    {
        $potentialPaths = array(
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $this->context->language->iso_code . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_MAIL_DIR_ . $this->context->language->iso_code . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_MAIL_DIR_ . 'en' . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_MAIL_DIR_ . '_partials' . DIRECTORY_SEPARATOR . $partialTemplateName,
        );

        foreach ($potentialPaths as $path) {
            if (Tools::file_exists_cache($path)) {
                $this->context->smarty->assign('list', $variables);

                return $this->context->smarty->fetch($path);
            }
        }

        return '';
    }
}
