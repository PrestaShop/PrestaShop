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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Symfony\Component\Mailer\Exception\ExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Header\IdentificationHeader;

/**
 * Class MailCore.
 */
class MailCore extends ObjectModel
{
    public $id;

    /** @var string Recipient */
    public $recipient;

    /** @var string Template */
    public $template;

    /** @var string Subject */
    public $subject;

    /** @var int Language ID */
    public $id_lang;

    /** @var int Timestamp */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'mail',
        'primary' => 'id_mail',
        'fields' => [
            'recipient' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'copy_post' => false,
                'required' => true,
                'size' => 255,
            ],
            'template' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isTplName',
                'copy_post' => false,
                'required' => true,
                'size' => 62,
            ],
            'subject' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isMailSubject',
                'copy_post' => false,
                'required' => true,
                'size' => 255,
            ],
            'id_lang' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'copy_post' => false,
                'required' => true,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'copy_post' => false,
                'required' => true,
            ],
        ],
    ];

    /**
     * Mail content type.
     */
    public const TYPE_HTML = 1;
    public const TYPE_TEXT = 2;
    public const TYPE_BOTH = 3;

    /**
     * Send mail under SMTP server.
     */
    public const METHOD_SMTP = 2;

    /**
     * Disable mail, will return immediately after calling send method.
     */
    public const METHOD_DISABLE = 3;

    /**
     * Send Email.
     *
     * @param int $idLang Language ID of the email (to translate the template)
     * @param string $template Template: the name of template not be a var but a string !
     * @param string $subject Subject of the email
     * @param array $templateVars Template variables for the email
     * @param string|array<string> $to To email
     * @param string|array<string> $toName To name
     * @param string $from From email
     * @param string $fromName To email
     * @param array $fileAttachment array with three parameters (content, mime and name).
     *                              You can use an array of array to attach multiple files
     * @param bool $mode_smtp SMTP mode (deprecated)
     * @param string $templatePath Template path
     * @param bool $die Die after error
     * @param int $idShop Shop ID
     * @param string|array<string>|null $bcc Bcc recipient address. You can use an array of array to send to multiple recipients
     * @param string|null $replyTo Reply-To recipient address
     * @param string|null $replyToName Reply-To recipient name
     *
     * @return bool|int Whether sending was successful. If not at all, false, otherwise amount of recipients succeeded.
     */
    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = ''
    ) {
        if (!$idShop) {
            $idShop = Context::getContext()->shop->id;
        }

        // An array [module_name => module_output] will be returned
        $hookBeforeEmailResult = Hook::exec(
            'actionEmailSendBefore',
            [
                'idLang' => &$idLang,
                'template' => &$template,
                'subject' => &$subject,
                'templateVars' => &$templateVars,
                'to' => &$to,
                'toName' => &$toName,
                'from' => &$from,
                'fromName' => &$fromName,
                'fileAttachment' => &$fileAttachment,
                'mode_smtp' => &$mode_smtp,
                'templatePath' => &$templatePath,
                'die' => &$die,
                'idShop' => &$idShop,
                'bcc' => &$bcc,
                'replyTo' => &$replyTo,
            ],
            null,
            true
        );

        if ($hookBeforeEmailResult === null) {
            $keepGoing = false;
        } else {
            $keepGoing = array_reduce(
                $hookBeforeEmailResult,
                function ($carry, $item) {
                    return ($item === false) ? false : $carry;
                },
                true
            );
        }

        if (!$keepGoing) {
            return true;
        }

        if (is_numeric($idShop) && $idShop) {
            $shop = new Shop((int) $idShop);
        }

        if (!isset($shop)) {
            self::dieOrLog($die, 'Error: parameter "idShop" is corrupted');

            return false;
        }

        $configuration = Configuration::getMultiple(
            [
                'PS_SHOP_EMAIL',
                'PS_MAIL_METHOD',
                'PS_MAIL_SERVER',
                'PS_MAIL_USER',
                'PS_MAIL_PASSWD',
                'PS_SHOP_NAME',
                'PS_MAIL_SMTP_ENCRYPTION',
                'PS_MAIL_SMTP_PORT',
                'PS_MAIL_TYPE',
                'PS_MAIL_DKIM_ENABLE',
                'PS_MAIL_DKIM_DOMAIN',
                'PS_MAIL_DKIM_SELECTOR',
                'PS_MAIL_DKIM_KEY',
            ],
            null,
            null,
            $idShop
        );

        // Returns immediately if emails are deactivated
        if ($configuration['PS_MAIL_METHOD'] == self::METHOD_DISABLE) {
            return true;
        }

        // Hook to alter template vars
        Hook::exec(
            'sendMailAlterTemplateVars',
            [
                'template' => $template,
                'template_vars' => &$templateVars,
            ]
        );

        if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']) ||
            Tools::strtolower($configuration['PS_MAIL_SMTP_ENCRYPTION']) === 'off'
        ) {
            $isTls = false;
        } else {
            $isTls = true;
        }

        if (!isset($configuration['PS_MAIL_SMTP_PORT'])) {
            $configuration['PS_MAIL_SMTP_PORT'] = 'default';
        }

        /*
         * Sending an e-mail can be of vital importance for the merchant, when his password
         * is lost for example, so we must not die but do our best to send the e-mail.
         */
        if (!isset($from) || !Validate::isEmail($from)) {
            $from = $configuration['PS_SHOP_EMAIL'];
        }

        if (!Validate::isEmail($from)) {
            $from = null;
        }

        // $from_name is not that important, no need to die if it is not valid
        if (!isset($fromName) || !Validate::isMailName($fromName)) {
            $fromName = $configuration['PS_SHOP_NAME'];
        }

        if (!Validate::isMailName($fromName)) {
            $fromName = null;
        }

        /*
         * It would be difficult to send an e-mail if the e-mail is not valid,
         * so this time we can die if there is a problem.
         */
        if (!is_array($to) && !Validate::isEmail($to)) {
            self::dieOrLog($die, 'Error: parameter "to" is corrupted');

            return false;
        }

        // if bcc is not null, make sure it's a valid e-mail
        if (null !== $bcc && !is_array($bcc) && !Validate::isEmail($bcc)) {
            self::dieOrLog($die, 'Error: parameter "bcc" is corrupted');
            $bcc = null;
        }

        if (!is_array($templateVars)) {
            $templateVars = [];
        }

        // Do not crash for this error, that may be a complicated customer name
        if (is_string($toName) && !empty($toName) && !Validate::isMailName($toName)) {
            $toName = null;
        }

        if (!Validate::isTplName($template)) {
            self::dieOrLog($die, 'Error: invalid e-mail template');

            return false;
        }

        if (!Validate::isMailSubject($subject)) {
            self::dieOrLog($die, 'Error: invalid e-mail subject');

            return false;
        }

        $email = new Email();

        /* Construct multiple recipients list if needed */
        if (is_array($to)) {
            foreach ($to as $key => $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    self::dieOrLog($die, 'Error: invalid e-mail address');

                    return false;
                }

                if (is_array($toName) && isset($toName[$key])) {
                    $addrName = $toName[$key];
                } else {
                    $addrName = $toName;
                }

                $addrName = ($addrName == null || $addrName == $addr || !Validate::isGenericName($addrName)) ?
                          '' :
                          self::mimeEncode($addrName);
                $email->addTo(new Address(self::toPunycode($addr), $addrName));
            }
            $toPlugin = $to[0];
        } else {
            /* Simple recipient, one address */
            $toPlugin = $to;
            $toName = (($toName == null || $toName == $to) ? '' : self::mimeEncode($toName));
            $email->addTo(new Address(self::toPunycode($to), $toName));
        }

        if (isset($bcc) && is_array($bcc)) {
            foreach ($bcc as $addr) {
                $addr = trim($addr);
                if (!Validate::isEmail($addr)) {
                    self::dieOrLog($die, 'Error: invalid e-mail address');

                    return false;
                }

                $email->addBcc(new Address(self::toPunycode($addr)));
            }
        } elseif (isset($bcc)) {
            $email->addBcc(new Address(self::toPunycode($bcc)));
        }

        try {
            /* Connect with the appropriate configuration */
            if ($configuration['PS_MAIL_METHOD'] == self::METHOD_SMTP) {
                if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT'])) {
                    self::dieOrLog($die, 'Error: invalid SMTP server or SMTP port');

                    return false;
                }
                $transport = (new EsmtpTransport(
                    $configuration['PS_MAIL_SERVER'],
                    $configuration['PS_MAIL_SMTP_PORT'],
                    $isTls
                ))
                    ->setUsername($configuration['PS_MAIL_USER'])
                    ->setPassword($configuration['PS_MAIL_PASSWD'])
                ;
            } else {
                $transport = new SendmailTransport();
            }

            $mailer = new Mailer($transport);
            /* Get templates content */
            $iso = Language::getIsoById((int) $idLang);
            $isoDefault = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
            $isoArray = [];
            if ($iso) {
                $isoArray[] = $iso;
            }

            if ($isoDefault && $iso !== $isoDefault) {
                $isoArray[] = $isoDefault;
            }

            if (!in_array('en', $isoArray)) {
                $isoArray[] = 'en';
            }

            $moduleName = false;

            // get templatePath
            if (preg_match('#' . $shop->physical_uri . 'modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $templatePath)) &&
                preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/', $templatePath), $res)
            ) {
                $moduleName = $res[1];
            }

            $isoTemplate = '';
            foreach ($isoArray as $isoCode) {
                $isoTemplate = $isoCode . '/' . $template;
                $templatePath = self::getTemplateBasePath($isoTemplate, $moduleName, $shop->theme);

                if (!file_exists($templatePath . $isoTemplate . '.txt') &&
                    (
                        $configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH ||
                        $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT
                    )
                ) {
                    PrestaShopLogger::addLog(
                        Context::getContext()->getTranslator()->trans(
                            'Error - The following e-mail template is missing: %s',
                            [$templatePath . $isoTemplate . '.txt'],
                            'Admin.Advparameters.Notification'
                        )
                    );
                } elseif (!file_exists($templatePath . $isoTemplate . '.html') &&
                          (
                              $configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH ||
                              $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML
                          )
                ) {
                    PrestaShopLogger::addLog(
                        Context::getContext()->getTranslator()->trans(
                            'Error - The following e-mail template is missing: %s',
                            [$templatePath . $isoTemplate . '.html'],
                            'Admin.Advparameters.Notification'
                        )
                    );
                } else {
                    $templatePathExists = true;

                    break;
                }
            }

            if (empty($templatePathExists)) {
                self::dieOrLog($die, 'Error - The following e-mail template is missing: %s', [$template]);

                return false;
            }

            $templateHtml = '';
            $templateTxt = '';

            // An array [module_name => module_output] will be returned (no effect)
            Hook::exec(
                'actionEmailAddBeforeContent',
                [
                    'template' => $template,
                    'template_html' => &$templateHtml,
                    'template_txt' => &$templateTxt,
                    'id_lang' => (int) $idLang,
                ],
                null,
                true
            );
            $templateHtml .= Tools::file_get_contents($templatePath . $isoTemplate . '.html');
            $templateTxt .= strip_tags(
                html_entity_decode(
                    Tools::file_get_contents($templatePath . $isoTemplate . '.txt'),
                    ENT_COMPAT,
                    'utf-8'
                )
            );

            // An array [module_name => module_output] will be returned (no effect)
            Hook::exec(
                'actionEmailAddAfterContent',
                [
                    'template' => $template,
                    'template_html' => &$templateHtml,
                    'template_txt' => &$templateTxt,
                    'id_lang' => (int) $idLang,
                ],
                null,
                true
            );

            /* Create mail and attach differents parts */
            if (Configuration::get('PS_MAIL_SUBJECT_PREFIX')) {
                $subject = '[' . strip_tags($configuration['PS_SHOP_NAME']) . '] ' . $subject;
            }
            $email->subject($subject);

            /* Set Message-ID - getmypid() is blocked on some hosting */
            $email
                ->getHeaders()
                ->add(new IdentificationHeader('Message-ID', Mail::generateId()))
            ;

            if (!($replyTo && Validate::isEmail($replyTo))) {
                $replyTo = $from;
            }

            if (isset($replyTo) && $replyTo) {
                $email->replyTo(new Address($replyTo, (string) $replyToName));
            }

            if (false !== Configuration::get('PS_LOGO_MAIL', null, null, $idShop) &&
                file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $idShop))
            ) {
                $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO_MAIL', null, null, $idShop);
            } else {
                if (file_exists(_PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $idShop))) {
                    $logo = _PS_IMG_DIR_ . Configuration::get('PS_LOGO', null, null, $idShop);
                } else {
                    $templateVars['{shop_logo}'] = '';
                }
            }
            ShopUrl::cacheMainDomainForShop((int) $idShop);
            /* don't attach the logo as */
            if (isset($logo) && $configuration['PS_MAIL_TYPE'] != Mail::TYPE_TEXT) {
                $templateVars['{shop_logo}'] = 'cid:shop_logo';
                $email->embedFromPath($logo, 'shop_logo');
            }

            if (!(Context::getContext()->link instanceof Link)) {
                Context::getContext()->link = new Link();
            }

            $templateVars['{shop_name}'] = Tools::safeOutput($configuration['PS_SHOP_NAME']);
            $templateVars['{shop_url}'] = Context::getContext()->link->getPageLink(
                'index',
                null,
                $idLang,
                null,
                false,
                $idShop
            );
            $templateVars['{my_account_url}'] = Context::getContext()->link->getPageLink(
                'my-account',
                null,
                $idLang,
                null,
                false,
                $idShop
            );
            $templateVars['{guest_tracking_url}'] = Context::getContext()->link->getPageLink(
                'guest-tracking',
                null,
                $idLang,
                null,
                false,
                $idShop
            );
            $templateVars['{history_url}'] = Context::getContext()->link->getPageLink(
                'history',
                null,
                $idLang,
                null,
                false,
                $idShop
            );
            $templateVars['{order_slip_url}'] = Context::getContext()->link->getPageLink(
                'order-slip',
                null,
                $idLang,
                null,
                false,
                $idShop
            );
            $templateVars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $idShop));
            // Get extra template_vars
            $extraTemplateVars = [];

            // An array [module_name => module_output] will be returned (no effect)
            Hook::exec(
                'actionGetExtraMailTemplateVars',
                [
                    'template' => $template,
                    'template_vars' => $templateVars,
                    'extra_template_vars' => &$extraTemplateVars,
                    'id_lang' => (int) $idLang,
                ],
                null,
                true
            );
            $templateVars = array_merge($templateVars, $extraTemplateVars);

            if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH ||
                $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML
            ) {
                $templateHtml = strtr($templateHtml, $templateVars);
                $email->html($templateHtml);
                if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH) {
                    $templateTxt = strtr($templateTxt, $templateVars);
                    $email->text($templateTxt);
                }
            } else {
                $templateTxt = strtr($templateTxt, $templateVars);
                $email->text($templateTxt);
            }

            if (!empty($fileAttachment)) {
                // Multiple attachments?
                if (!is_array(current($fileAttachment))) {
                    $fileAttachment = [$fileAttachment];
                }

                foreach ($fileAttachment as $attachment) {
                    if (isset($attachment['content'], $attachment['name'], $attachment['mime'])) {
                        $email->attach($attachment['content'], $attachment['name'], $attachment['mime']);
                    }
                }
            }
            /* Send mail */
            $email->from(new Address($from, (string) $fromName));

            // Hook to alter Symfony Mailer before sending mail
            Hook::exec('actionMailAlterMessageBeforeSend', [
                'message' => &$email,
            ]);

            /* Create new message and DKIM sign it, if enabled and all data for signature are provided */
            if ((bool) $configuration['PS_MAIL_DKIM_ENABLE'] === true
                && !empty($configuration['PS_MAIL_DKIM_DOMAIN'])
                && !empty($configuration['PS_MAIL_DKIM_SELECTOR'])
                && !empty($configuration['PS_MAIL_DKIM_KEY'])
            ) {
                $signer = new DkimSigner(
                    $configuration['PS_MAIL_DKIM_KEY'],
                    $configuration['PS_MAIL_DKIM_DOMAIN'],
                    $configuration['PS_MAIL_DKIM_SELECTOR']
                );

                $signedEmail = $signer->sign($email);
            }

            $mailer->send($signedEmail ?? $email);
            ShopUrl::resetMainDomainCache();

            if (Configuration::get('PS_LOG_EMAILS')) {
                $mail = new Mail();
                $mail->template = Tools::substr($template, 0, 62);
                $mail->subject = Tools::substr($email->getSubject(), 0, 255);
                $mail->id_lang = (int) $idLang;

                $recipientsTo = self::convertAdressesToArray($email->getTo());
                $recipientsCc = self::convertAdressesToArray($email->getCc());
                $recipientsBcc = self::convertAdressesToArray($email->getBcc());

                if (!is_array($recipientsTo)) {
                    $recipientsTo = [];
                }
                if (!is_array($recipientsCc)) {
                    $recipientsCc = [];
                }
                if (!is_array($recipientsBcc)) {
                    $recipientsBcc = [];
                }
                foreach (array_merge($recipientsTo, $recipientsCc, $recipientsBcc) as $emailAlias => $recipient_name) {
                    $mail->id = null;
                    $mail->recipient = Tools::substr($emailAlias, 0, 255);
                    $mail->add();
                }
            }

            return true;
        } catch (ExceptionInterface $e) {
            PrestaShopLogger::addLog(
                'Mailer Error: ' . $e->getMessage(),
                3,
                null,
                'MailerMessage'
            );

            return false;
        }
    }

    protected static function getTemplateBasePath($isoTemplate, $moduleName, $theme)
    {
        $basePathList = [
            _PS_ROOT_DIR_ . '/themes/' . $theme->getName() . '/',
            _PS_ROOT_DIR_ . '/themes/' . $theme->get('parent') . '/',
            _PS_ROOT_DIR_,
        ];

        if ($moduleName !== false) {
            $templateRelativePath = '/modules/' . $moduleName . '/mails/';
        } else {
            $templateRelativePath = '/mails/';
        }

        foreach ($basePathList as $base) {
            $templatePath = $base . $templateRelativePath;
            if (file_exists($templatePath . $isoTemplate . '.txt') || file_exists($templatePath . $isoTemplate . '.html')) {
                return $templatePath;
            }
        }

        return '';
    }

    /**
     * @param int $idMail Mail ID
     *
     * @return bool Whether removal succeeded
     */
    public static function eraseLog($idMail)
    {
        return Db::getInstance()->delete('mail', 'id_mail = ' . (int) $idMail);
    }

    /**
     * @return bool
     */
    public static function eraseAllLogs()
    {
        return Db::getInstance()->execute('TRUNCATE TABLE ' . _DB_PREFIX_ . 'mail');
    }

    /**
     * Send a test email.
     *
     * @param bool $smtpChecked Is SMTP checked?
     * @param string $smtpServer SMTP Server hostname
     * @param string $content Content of the email
     * @param string $subject Subject of the email
     * @param bool $type Deprecated
     * @param string $to To email address
     * @param string $from From email address
     * @param string $smtpLogin SMTP login name
     * @param string $smtpPassword SMTP password
     * @param int $smtpPort SMTP Port
     * @param bool|string $smtpEncryption Encryption type. "off" or false disable encryption.
     *
     * @return bool|string True if succeeded, otherwise the error message
     */
    public static function sendMailTest(
        $smtpChecked,
        $smtpServer,
        $content,
        $subject,
        $type,
        $to,
        $from,
        $smtpLogin,
        $smtpPassword,
        $smtpPort,
        $smtpEncryption,
        bool $dkimEnable = false,
        string $dkimKey = '',
        string $dkimDomain = '',
        string $dkimSelector = ''
    ) {
        $result = false;

        try {
            if ($smtpChecked) {
                if (Tools::strtolower($smtpEncryption) === 'off') {
                    $isTls = false;
                } else {
                    $isTls = true;
                }
                $transport = (new EsmtpTransport(
                    $smtpServer,
                    $smtpPort,
                    $isTls
                ))
                    ->setUsername($smtpLogin)
                    ->setPassword($smtpPassword)
                ;
            } else {
                $transport = new SendmailTransport();
            }

            $mailer = new Mailer($transport);

            $email = (new Email())
                ->from($from)
                ->to($to)
                ->subject($subject)
                ->text($content)
            ;

            /* Create new message and DKIM sign it, if enabled and all data for signature are provided */
            if ($dkimEnable === true
                && !empty($dkimKey)
                && !empty($dkimDomain)
                && !empty($dkimSelector)
            ) {
                $signer = new DkimSigner(
                    $dkimKey,
                    $dkimDomain,
                    $dkimSelector
                );

                $signedEmail = $signer->sign($email);
            }

            $mailer->send($signedEmail ?? $email);
            $result = true;
        } catch (ExceptionInterface $e) {
            $result = $e->getMessage();
        }

        return $result;
    }

    /**
     * This method is used to get the translation for email Object.
     * For an object is forbidden to use htmlentities,
     * we have to return a sentence with accents.
     *
     * @param string $string raw sentence (write directly in file)
     *
     * @return mixed
     */
    public static function l($string, $idLang = null, Context $context = null)
    {
        global $_LANGMAIL;

        if (!$context) {
            $context = Context::getContext();
        }

        if ($idLang === null) {
            $idLang = (!isset($context->language) || !is_object($context->language)) ?
                    (int) Configuration::get('PS_LANG_DEFAULT') :
                    (int) $context->language->id;
        }

        $isoCode = Language::getIsoById((int) $idLang);

        $file_core = _PS_ROOT_DIR_ . '/mails/' . $isoCode . '/lang.php';
        if (Tools::file_exists_cache($file_core) && empty($_LANGMAIL)) {
            include $file_core;
        }

        $fileTheme = _PS_THEME_DIR_ . 'mails/' . $isoCode . '/lang.php';
        if (Tools::file_exists_cache($fileTheme)) {
            include $fileTheme;
        }

        if (!is_array($_LANGMAIL)) {
            return str_replace('"', '&quot;', $string);
        }

        $key = str_replace('\'', '\\\'', $string);

        return str_replace(
            '"',
            '&quot;',
            (array_key_exists($key, $_LANGMAIL) && !empty($_LANGMAIL[$key])) ? $_LANGMAIL[$key] : $string
        );
    }

    /* Generate a Message-ID */
    protected static function generateId($idstring = null)
    {
        $midparams = [
            'utctime' => date('YmdHis'),
            'randint' => mt_rand(),
            'customstr' => ($idstring !== null && preg_match('/^(?<!\\.)[a-z0-9\\.]+(?!\\.)$/iD', $idstring) ? $idstring : 'mailer'),
            'hostname' => !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : php_uname('n'),
        ];

        return vsprintf('%s.%d.%s@%s', $midparams);
    }

    /**
     * Check if a multibyte character set is used for the data.
     *
     * @param string $data Data
     *
     * @return bool Whether the string uses a multibyte character set
     */
    public static function isMultibyte($data)
    {
        $length = Tools::strlen($data);
        for ($i = 0; $i < $length; ++$i) {
            if (ord(($data[$i])) > 128) {
                return true;
            }
        }

        return false;
    }

    /**
     * MIME encode the string.
     *
     * @param string $string The string to encode
     * @param string $charset The character set to use
     * @param string $newline The newline character(s)
     *
     * @return mixed|string MIME encoded string
     */
    public static function mimeEncode($string, $charset = 'UTF-8', $newline = "\r\n")
    {
        if (!self::isMultibyte($string) && Tools::strlen($string) < 75) {
            return $string;
        }

        $charset = Tools::strtoupper($charset);
        $start = '=?' . $charset . '?B?';
        $end = '?=';
        $sep = $end . $newline . ' ' . $start;
        $length = 75 - Tools::strlen($start) - Tools::strlen($end);
        $length = $length - ($length % 4);

        if ($charset === 'UTF-8') {
            $parts = [];
            $maxchars = floor(($length * 3) / 4);
            $stringLength = Tools::strlen($string);

            while ($stringLength > $maxchars) {
                $i = (int) $maxchars;
                $result = ord($string[$i]);

                while ($result >= 128 && $result <= 191) {
                    $result = ord($string[--$i]);
                }

                $parts[] = base64_encode(Tools::substr($string, 0, $i));
                $string = Tools::substr($string, $i);
                $stringLength = Tools::strlen($string);
            }

            $parts[] = base64_encode($string);
            $string = implode($sep, $parts);
        } else {
            $string = chunk_split(base64_encode($string), $length, $sep);
            $string = preg_replace('/' . preg_quote($sep) . '$/', '', $string);
        }

        return $start . $string . $end;
    }

    /**
     * Automatically convert email to Punycode.
     *
     * Try to use INTL_IDNA_VARIANT_UTS46 only if defined, else use INTL_IDNA_VARIANT_2003
     * See https://wiki.php.net/rfc/deprecate-and-remove-intl_idna_variant_2003
     *
     * @param string $to Email address
     *
     * @return string
     */
    public static function toPunycode($to)
    {
        $address = explode('@', $to);
        if (empty($address[0]) || empty($address[1])) {
            return $to;
        }

        if (defined('INTL_IDNA_VARIANT_UTS46')) {
            return $address[0] . '@' . idn_to_ascii($address[1], 0, INTL_IDNA_VARIANT_UTS46);
        }

        /*
         * INTL_IDNA_VARIANT_2003 const will be removed in PHP 8.
         * See https://wiki.php.net/rfc/deprecate-and-remove-intl_idna_variant_2003
         */
        if (defined('INTL_IDNA_VARIANT_2003')) {
            return $address[0] . '@' . idn_to_ascii($address[1], 0, INTL_IDNA_VARIANT_2003);
        }

        return $address[0] . '@' . idn_to_ascii($address[1]);
    }

    /**
     * Generic function to dieOrLog with translations.
     *
     * @param bool $die Should die
     * @param string $message Message
     * @param array $templates Templates list
     * @param string $domain Translation domain
     */
    protected static function dieOrLog(
        $die,
        $message,
        $templates = [],
        $domain = 'Admin.Advparameters.Notification'
    ) {
        Tools::dieOrLog(
            Context::getContext()->getTranslator()->trans(
                $message,
                $templates,
                $domain
            ),
            $die
        );
    }

    /**
     * @param Address[] $addresses
     *
     * @return array<string, string|null>
     */
    private static function convertAdressesToArray(array $addresses): array
    {
        $recipientsTo = [];

        foreach ($addresses as $address) {
            $recipientsTo[$address->getAddress()] = $address->getName();
        }

        return $recipientsTo;
    }
}
