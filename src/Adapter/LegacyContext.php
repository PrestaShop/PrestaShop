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

namespace PrestaShop\PrestaShop\Adapter;

use AdminController;
use AdminLegacyLayoutControllerCore;
use Context;
use Currency;
use Employee;
use Language;
use RuntimeException;
use Smarty;
use Symfony\Component\Process\Exception\LogicException;
use Tab;

/**
 * This adapter will complete the new architecture Context with legacy values.
 * A merge is done, but the legacy values will be transferred to the new Context
 * during legacy refactoring.
 */
class LegacyContext
{
    /** @var Context */
    protected static $instance = null;

    /** @var Currency|null */
    private $employeeCurrency;

    /** @var string Contains the base uri for mail themes (by default https://domain.com/mails/themes/). Used for mails assets. */
    private $mailThemesUri;

    /** @var Tools */
    private $tools;

    /**
     * @param string|null $mailThemesUri
     * @param Tools|null $tools
     */
    public function __construct(
        $mailThemesUri = null,
        Tools $tools = null
    ) {
        $this->mailThemesUri = $mailThemesUri;
        $this->tools = null !== $tools ? $tools : new Tools();
    }

    /**
     * To be used only in Adapters. Should not been called by Core classes. Prefer to use Core\context class,
     * that will contains all you need in the Core architecture.
     *
     * @throws LogicException If legacy context is not set properly
     *
     * @return Context the Legacy context, for Adapter use only
     */
    public function getContext()
    {
        if (null === static::$instance) {
            $legacyContext = Context::getContext();

            if ($legacyContext && !empty($legacyContext->shop) && !isset($legacyContext->controller) && isset($legacyContext->employee)) {
                //init real legacy shop context
                $adminController = new AdminController();
                $adminController->initShopContext();
            }
            static::$instance = $legacyContext;
        }

        return static::$instance;
    }

    /**
     * Get smarty instance from legacy context.
     *
     * @return Smarty
     */
    public function getSmarty()
    {
        return $this->getContext()->smarty;
    }

    /**
     * Gets the Admin base url (actually random directory name).
     *
     * @return string
     */
    public function getAdminBaseUrl()
    {
        return __PS_BASE_URI__ . basename(_PS_ADMIN_DIR_) . '/';
    }

    public function getCountryId(): int
    {
        return $this->getContext()->country->id;
    }

    /**
     * Adapter to get Admin HTTP link.
     *
     * @param string $controller the controller name
     * @param bool $withToken
     * @param array<string> $extraParams
     *
     * @return string
     */
    public function getAdminLink($controller, $withToken = true, $extraParams = [])
    {
        return $this->getContext()->link->getAdminLink($controller, $withToken, $extraParams, $extraParams);
    }

    /**
     * Returns the controller link in its legacy form, without trying to convert it in symfony url.
     *
     * @param string $controller
     * @param bool $withToken
     * @param array $extraParams
     *
     * @return string
     */
    public function getLegacyAdminLink($controller, $withToken = true, $extraParams = [])
    {
        return $this->getContext()->link->getLegacyAdminLink($controller, $withToken, $extraParams);
    }

    /**
     * Adapter to get Front controller HTTP link.
     *
     * @param string $controller the controller name
     *
     * @return string
     */
    public function getFrontUrl($controller)
    {
        $legacyContext = $this->getContext();

        return $legacyContext->link->getPageLink($controller);
    }

    /**
     * Adapter to get Root Url.
     *
     * @return string The lagacy root URL
     */
    public function getRootUrl()
    {
        return __PS_BASE_URI__;
    }

    /**
     * Adapter to get upload directory
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return _PS_UPLOAD_DIR_;
    }

    /**
     * Url to the mail themes folder
     *
     * @return string
     */
    public function getMailThemesUrl()
    {
        return $this->tools->getShopDomainSsl(true) . __PS_BASE_URI__ . $this->mailThemesUri;
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
        Context::getContext()->override_controller_name_for_translations = $legacyController;
    }

    /**
     * Adapter to get admin legacy layout into legacy controller context.
     *
     * @param string $controllerName The legacy controller name
     * @param string $title The page title to override default one
     * @param array $headerToolbarBtn The header toolbar to override
     * @param string $displayType The legacy display type variable
     * @param bool $showContentHeader can force header toolbar (buttons and title) to be hidden with false value
     * @param string $headerTabContent
     * @param bool $enableSidebar Allow to use right sidebar to display docs for instance
     * @param string $helpLink If specified, will be used instead of legacy one
     * @param string[] $jsRouterMetadata array to provide base_url and security token for JS Router
     * @param string $metaTitle
     * @param bool $useRegularH1Structure allows complex <h1> structure if set to false
     * @param string $baseLayout
     *
     * @return string The html layout
     */
    public function getLegacyLayout(
        $controllerName,
        $title,
        $headerToolbarBtn,
        $displayType,
        $showContentHeader,
        $headerTabContent,
        $enableSidebar,
        $helpLink = '',
        $jsRouterMetadata = [],
        $metaTitle = '',
        $useRegularH1Structure = true,
        $baseLayout = 'layout.tpl'
    ) {
        $originCtrl = new AdminLegacyLayoutControllerCore(
            $controllerName,
            $title,
            $headerToolbarBtn,
            $displayType,
            $showContentHeader,
            $headerTabContent,
            $enableSidebar,
            $helpLink,
            $jsRouterMetadata,
            $metaTitle,
            $useRegularH1Structure
        );
        $originCtrl->layout = $baseLayout;
        $originCtrl->run();

        return $originCtrl->outPutHtml;
    }

    /**
     * Returns available languages. The first one is the employee default one.
     *
     * @param bool $active Select only active languages
     * @param int|bool $id_shop Shop ID
     * @param bool $ids_only If true, returns an array of language IDs
     *
     * @return array<int|array> Languages
     */
    public function getLanguages($active = true, $id_shop = false, $ids_only = false)
    {
        $languages = $this->getLegacyLanguages($active, $id_shop, $ids_only);
        $defaultLanguageFirst = $this->getLanguage();
        usort($languages, function ($a, $b) use ($defaultLanguageFirst) {
            if ($a['id_lang'] == $defaultLanguageFirst->id) {
                return -1; // $a is the default one.
            }
            if ($b['id_lang'] == $defaultLanguageFirst->id) {
                return 1; // $b is the default one.
            }

            return 0;
        });

        return $languages;
    }

    /**
     * Returns language ISO code set for the current employee.
     *
     * @return string Languages
     */
    public function getEmployeeLanguageIso()
    {
        return Language::getIsoById($this->getContext()->employee->id_lang);
    }

    /**
     * Returns Currency set for the current employee.
     *
     * @return Currency|null
     */
    public function getEmployeeCurrency()
    {
        if (null === $this->employeeCurrency && $this->getContext()->currency) {
            $this->employeeCurrency = $this->getContext()->currency;
        }

        return $this->employeeCurrency;
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

        return new Language();
    }

    /**
     * Get employee's default tab name.
     *
     * @return string Default tab name for employee
     *
     * @throws RuntimeException Throws exception if employee does not exist in context
     */
    public function getDefaultEmployeeTab()
    {
        $employee = $this->getContext()->employee;

        if (!$employee instanceof Employee) {
            throw new RuntimeException('Cannot retrieve default employee tab. Employee does not exist in context!');
        }

        $idTab = $employee->default_tab;
        $tab = new Tab($idTab);

        return $tab->class_name;
    }

    /**
     * @return string
     */
    public function getMailThemesUri()
    {
        return $this->mailThemesUri;
    }

    /**
     * @return array Returns both enabled and disabled languages
     */
    public function getAvailableLanguages()
    {
        return $this->getLegacyLanguages(false);
    }

    /**
     * @param bool $active
     * @param bool|int $id_shop
     * @param bool $ids_only
     *
     * @return array
     */
    private function getLegacyLanguages(bool $active = true, $id_shop = false, bool $ids_only = false): array
    {
        return Language::getLanguages($active, $id_shop, $ids_only);
    }

    /**
     * @param Context $testInstance
     *                              Unit testing purpose only
     */
    public static function setInstanceForTesting(Context $testInstance)
    {
        static::$instance = $testInstance;
    }
}
