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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;

/**
 * Class VariablesTransformation is used only for preview, it replaces the
 * {variables} present in the mail templates (this replacement is performed
 * by the Mail class in real behavior.
 */
class MailVariablesTransformation extends AbstractTransformation
{
    /** @var ConfigurationInterface */
    private $configuration;

    /** @var LegacyContext */
    private $legacyContext;

    /**
     * @param ConfigurationInterface $configuration
     * @param LegacyContext $legacyContext
     *
     * @throws \PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException
     */
    public function __construct(
        ConfigurationInterface $configuration,
        LegacyContext $legacyContext
    ) {
        parent::__construct(MailTemplateInterface::HTML_TYPE);
        $this->configuration = $configuration;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($templateContent, array $templateVariables)
    {
        $templateVars = $this->getTemplateVars();
        $templateVars['{firstname}'] = 'John';
        $templateVars['{lastname}'] = 'Doe';
        $templateVars['{email}'] = 'john.doe@unknown.com';

        $templateContent = strtr($templateContent, $templateVars);

        return $templateContent;
    }

    /**
     * @return array
     */
    private function getTemplateVars()
    {
        $templateVars = [];

        $context = $this->legacyContext->getContext();
        $imageDir = $this->configuration->get('_PS_IMG_DIR_');
        $baseUrl = $context->link->getBaseLink();

        //Logo url
        $logoMail = $this->configuration->get('PS_LOGO_MAIL');
        $logo = $this->configuration->get('PS_LOGO');
        if (!empty($logoMail) && file_exists($imageDir . $logoMail)) {
            $templateVars['{shop_logo}'] = $baseUrl . 'img/' . $logoMail;
        } else {
            if (!empty($logo) && file_exists($imageDir . $logo)) {
                $templateVars['{shop_logo}'] = $baseUrl . 'img/' . $logo;
            } else {
                $templateVars['{shop_logo}'] = '';
            }
        }

        $templateVars['{shop_name}'] = $context->shop->name;
        $templateVars['{shop_url}'] = $context->link->getPageLink('index', true);
        $templateVars['{my_account_url}'] = $context->link->getPageLink('my-account', true);
        $templateVars['{guest_tracking_url}'] = $context->link->getPageLink('guest-tracking', true);
        $templateVars['{history_url}'] = $context->link->getPageLink('history', true);
        $templateVars['{color}'] = $this->configuration->get('PS_MAIL_COLOR');

        return $templateVars;
    }
}
