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

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use Smarty;
use Tools;

/**
 * Class MailPartialTemplateRenderer renders partial mail templates (especially for order). This
 * feature was moved in this service so that it can be shared between PaymentModule and MailPreviewVariablesBuilder.
 */
class MailPartialTemplateRenderer
{
    /** @var Smarty */
    private $smarty;

    /**
     * @param Smarty $smarty
     */
    public function __construct(Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * Fetch the content of $partialTemplateName inside the folder
     * current_theme/mails/current_iso_lang/ if found, otherwise in
     * mails/current_iso_lang.
     *
     * @param string $partialTemplateName template name with extension
     * @param LanguageInterface $language
     * @param array $variables sent to smarty as 'list'
     * @param bool $cleanComments
     *
     * @return string
     */
    public function render($partialTemplateName, LanguageInterface $language, array $variables = [], $cleanComments = false)
    {
        $potentialPaths = [
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . $language->getIsoCode() . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_MAIL_DIR_ . $language->getIsoCode() . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_THEME_DIR_ . 'mails' . DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_MAIL_DIR_ . 'en' . DIRECTORY_SEPARATOR . $partialTemplateName,
            _PS_MAIL_DIR_ . '_partials' . DIRECTORY_SEPARATOR . $partialTemplateName,
        ];

        foreach ($potentialPaths as $path) {
            if (Tools::file_exists_cache($path)) {
                $this->smarty->assign('list', $variables);
                $content = $this->smarty->fetch($path);
                if ($cleanComments) {
                    $content = preg_replace('/\s?<!--.*?-->\s?/s', '', $content);
                }

                return $content;
            }
        }

        return '';
    }
}
