<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
