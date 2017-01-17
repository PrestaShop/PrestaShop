<?php
/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\Process\Exception\LogicException;
use ContextCore as OldContext;
use Language;

/**
 * This adapter will complete the new architecture Context with legacy values.
 * A merge is done, but the legacy values will be transfered to the new Context
 * during legacy refactoring.
 */
class LegacyContext
{
    /**
     * To be used only in Adapters. Should not been called by Core classes. Prefer to use Core\context class,
     * that will contains all you need in the Core architecture
     *
     * @throws LogicException If legacy context is not set properly
     *
     * @return \Context The Legacy context, for Adapter use only.
     */
    public function getContext()
    {
        static $legacyContext = null;

        if (is_null($legacyContext)) {
            $legacyContext = OldContext::getContext();

            if ($legacyContext && !empty($legacyContext->shop) && !isset($legacyContext->controller) && isset($legacyContext->employee)) {
                //init real legacy shop context
                $adminController = new \AdminControllerCore();
                $adminController->initShopContext();
            }
        }

        return $legacyContext;
    }

    /**
     * Gets the Admin base url (actually random directory name).
     * @return string
     */
    public function getAdminBaseUrl()
    {
        return __PS_BASE_URI__.basename(_PS_ADMIN_DIR_).'/';
    }

    /**
     * Adapter to get Admin HTTP link.
     *
     * @param string $controller the controller name
     * @param bool $withToken
     * @param array[string] $extraParams
     * @return string
     */
    public function getAdminLink($controller, $withToken = true, $extraParams = array())
    {
        $id_lang = OldContext::getContext()->language->id;
        $params = $extraParams;
        if ($withToken) {
            $params['token'] = \ToolsCore::getAdminTokenLite($controller);
        }

        $link = new \LinkCore();
        return $link->getBaseLink().basename(_PS_ADMIN_DIR_).'/'.\DispatcherCore::getInstance()->createUrl($controller, $id_lang, $params, false);
    }

    /**
     * Adapter to get Front controller HTTP link.
     *
     * @param string $controller the controller name
     */
    public function getFrontUrl($controller)
    {
        $legacyContext = $this->getContext();
        return $legacyContext->link->getPageLink($controller);
    }

    /**
     * Adapter to get Root Url
     *
     * @return string The lagacy root URL
     */
    public function getRootUrl()
    {
        return __PS_BASE_URI__;
    }

    /**
     * This fix is used to have a ready translation in the smarty 'l' function.
     * Called by AutoResponseFormatTrait in beforeActionSuggestResponseFormat().
     * So if you do not use this Trait, you must call this method by yourself in the action.
     *
     * @param string $legacyController
     */
    public function setupLegacyTranslationContext($legacyController = 'AdminTab')
    {
        OldContext::getContext()->override_controller_name_for_translations = $legacyController;
    }

    /**
     * Adapter to get admin legacy layout into old controller context
     *
     * @param string $controllerName The legacy controller name
     * @param string $title The page title to override default one
     * @param array $headerToolbarBtn The header toolbar to override
     * @param string $displayType The legacy display type variable
     * @param bool $showContentHeader Can force header toolbar (buttons and title) to be hidden with false value.
     * @param bool $enableSidebar Allow to use right sidebar to display docs for instance
     * @param string $helpLink If specified, will be used instead of legacy one
     *
     * @return string The html layout
     */
    public function getLegacyLayout($controllerName = "", $title = "", $headerToolbarBtn = [], $displayType = "", $showContentHeader = true, $headerTabContent = '', $enableSidebar, $helpLink = '')
    {
        $originCtrl = new \AdminLegacyLayoutControllerCore($controllerName, $title, $headerToolbarBtn, $displayType, $showContentHeader, $headerTabContent, $enableSidebar, $helpLink);
        $originCtrl->run();

        return $originCtrl->outPutHtml;
    }

    /**
     * Returns available languages. The first one is the employee default one.
     *
     * @param bool     $active   Select only active languages
     * @param int|bool $id_shop  Shop ID
     * @param bool     $ids_only If true, returns an array of language IDs
     *
     * @return array Languages
     */
    public function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        $languages = \LanguageCore::getLanguages($active, $id_shop, $ids_only);
        $defaultLanguageFirst = $this->getContext()->employee->id_lang;
        usort($languages, function ($a, $b) use ($defaultLanguageFirst) {
            if ($a['id_lang'] == $defaultLanguageFirst) {
                return -1; // $a is the default one.
            }
            if ($b['id_lang'] == $defaultLanguageFirst) {
                return 1; // $b is the default one.
            }
            return 0;
        });
        return $languages;
    }

    /**
     * Returns language ISO code set for the current employee

     * @return array Languages
     */
    public function getEmployeeLanguageIso()
    {
        return \LanguageCore::getIsoById($this->getContext()->employee->id_lang);
    }

    /**
     * Returns Currency set for the current employee
     */
    public function getEmployeeCurrency()
    {
        static $employeeCurrency;

        if(null === $employeeCurrency) {
            $employeeCurrency = $this->getContext()->currency->sign;
        }
        return $employeeCurrency;
    }

    /**
     * @return Language
     */
    public function getLanguage()
    {
        $context = $this->getContext();

        if ($context->language instanceof Language) {
            return $context->language;
        }

        return new Language;
    }
}
