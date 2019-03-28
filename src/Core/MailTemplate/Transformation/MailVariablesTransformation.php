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
     * @inheritDoc
     */
    public function apply($templateContent, array $templateVariables)
    {
        $templateVars = $this->getTemplateVars();
        $templateVars['{firstname}'] = 'John';
        $templateVars['{lastname}'] = 'Doe';
        $templateVars['{email}'] = 'john.doe@unknown.com';

        foreach ($templateVars as $placeholder => $value) {
            $templateContent = preg_replace('/'.$placeholder.'/', $value, $templateContent);
        }

        return $templateContent;
    }

    /**
     * @return array
     */
    private function getTemplateVars()
    {
        $templateVars = [];

        $imageDir = $this->configuration->get('_PS_IMG_DIR_');
        $baseUrl = $this->legacyContext->getContext()->link->getBaseLink();

        //Logo url
        if (!empty($this->configuration->get('PS_LOGO_MAIL'))
            && file_exists($imageDir . $this->configuration->get('PS_LOGO_MAIL'))
        ) {
            $templateVars['{shop_logo}'] = $baseUrl . 'img/' . $this->configuration->get('PS_LOGO_MAIL');
        } else {
            if (!empty($this->configuration->get('PS_LOGO'))
                && file_exists($imageDir . $this->configuration->get('PS_LOGO'))) {
                $templateVars['{shop_logo}'] = $baseUrl . 'img/' . $this->configuration->get('PS_LOGO');
            } else {
                $templateVars['{shop_logo}'] = '';
            }
        }

        $templateVars['{shop_name}'] = $this->legacyContext->getContext()->shop->name;
        $templateVars['{shop_url}'] = $this->legacyContext->getContext()->link->getPageLink('index',true);
        $templateVars['{my_account_url}'] = $this->legacyContext->getContext()->link->getPageLink('my-account',true);
        $templateVars['{guest_tracking_url}'] = $this->legacyContext->getContext()->link->getPageLink('guest-tracking', true);
        $templateVars['{history_url}'] = $this->legacyContext->getContext()->link->getPageLink('history', true);
        $templateVars['{color}'] = $this->configuration->get('PS_MAIL_COLOR');

        return $templateVars;
    }
}
