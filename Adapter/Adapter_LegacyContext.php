<?php

/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
use PrestaShop\PrestaShop\Core\Foundation\Exception\DevelopmentErrorException;

class Adapter_LegacyContext
{
    /**
     * To be used only in Adapters. Should not been called by Core classes. Prefer to use Core\Business\context class,
     * that will contains all you need in the Core architecture
     * .
     * @return Context The Legacy context, for Adapter use only.
     */
    public function getContext()
    {
        $legacyContext = \Context::getContext();
        if (!isset($legacyContext->shop) ||
            !isset($legacyContext->language) ||
            !isset($legacyContext->link)
        ) {
            throw new DevelopmentErrorException('Legacy context is not set properly. Cannot use it to merge with Context structure.');
        }
        return $legacyContext;
    }

    public function mergeContextWithLegacy(PrestaShop\PrestaShop\Core\Business\Context &$newContext)
    {
        $legacyContext = $this->getContext();
        foreach (get_object_vars($legacyContext) as $key => $value) { // gets public attributes.
            if ($value !== null) {
                $newContext->$key = $value;
            }
        }
    }

    public function getAdminBaseUrl()
    {
        return __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/';
    }

    public function getAdminLink($controller, $withToken = true, $extraParams = array())
    {
        $id_lang = \Context::getContext()->language->id;
        $params = $extraParams;
        if ($withToken) {
            $params['token'] = \Tools::getAdminTokenLite($controller);
        }
        return \Dispatcher::getInstance()->createUrl($controller, $id_lang, $params, false);
    }

    public function getFrontUrl($controller)
    {
        $legacyContext = $this->getContext();
        return $legacyContext->link->getPageLink($controller);
    }
}
