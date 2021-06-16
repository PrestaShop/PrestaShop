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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Dashboard;

use Hook;
use Module;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Dashboard\DashboardHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Validate as LegacyValidate;

/**
 * Provides helper functions for dashboard controller.
 */
class DashboardHelper implements DashboardHelperInterface
{
    private const DOCUMENTATION_LINKS = [
        'fr' => 'https://www.prestashop.com/fr/contact?utm_source=back-office&utm_medium=links&utm_campaign=help-center-fr&utm_content=download17',
        'en' => 'https://www.prestashop.com/en/contact?utm_source=back-office&utm_medium=links&utm_campaign=help-center-en&utm_content=download17',
        'es' => 'https://www.prestashop.com/es/contacto?utm_source=back-office&utm_medium=links&utm_campaign=help-center-es&utm_content=download17',
        'de' => 'https://www.prestashop.com/de/kontakt?utm_source=back-office&utm_medium=links&utm_campaign=help-center-de&utm_content=download17',
        'it' => 'https://www.prestashop.com/it/contatti?utm_source=back-office&utm_medium=links&utm_campaign=help-center-it&utm_content=download17',
        'nl' => 'https://www.prestashop.com/nl/contacteer-ons?utm_source=back-office&utm_medium=links&utm_campaign=help-center-nl&utm_content=download17',
        'pt' => 'https://www.prestashop.com/pt/contato?utm_source=back-office&utm_medium=links&utm_campaign=help-center-pt&utm_content=download17',
        'pl' => 'https://www.prestashop.com/pl/kontakt?utm_source=back-office&utm_medium=links&utm_campaign=help-center-pl&utm_content=download17',
    ];

    /**
     * @var LegacyContext
     */
    private $context;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var Validate
     */
    private $validate;

    /**
     * @param LegacyContext $context
     * @param ModuleManager $moduleManager
     * @param Validate $validate
     */
    public function __construct(
        LegacyContext $context,
        ModuleManager $moduleManager,
        Validate $validate
    ) {
        $this->context = $context;
        $this->moduleManager = $moduleManager;
        $this->validate = $validate;
    }

    /**
     * {@inheritdoc}
     */
    public function getNewVersionUrl(Request $request): string
    {
        $schema = ($request->isSecure() ? 'https' : 'http') . '://';
        $autoupgrade = (int) $this->moduleManager->isInstalled('autoupgrade') && $this->moduleManager->isEnabled('autoupgrade');
        $isoCode = $this->context->getContext()->language->iso_code;

        return $schema . _PS_API_DOMAIN_ . '/version/check_version.php?v=' . _PS_VERSION_ . '&lang=' . $isoCode . '&autoupgrade=' . $autoupgrade . '&hosted_mode=' . (int) defined('_PS_HOST_MODE_');
    }

    /**
     * {@inheritdoc}
     */
    public function getDocumentationUrl(string $languageCode): string
    {
        return self::DOCUMENTATION_LINKS[$languageCode] ?? self::DOCUMENTATION_LINKS['en'];
    }

    /**
     * {@inheritdoc}
     */
    public function isHostMode(): bool
    {
        return defined('_PS_HOST_MODE_');
    }

    /**
     * {@inheritdoc}
     */
    public function getDashboardData(?string $module, bool $usePush, int $extra): array
    {
        $moduleId = null;
        if ($module !== null) {
            $moduleId = $this->moduleManager->getModuleIdByName($module);
        }

        $context = $this->context->getContext();
        $params = [
            'date_from' => $context->employee->stats_date_from,
            'date_to' => $context->employee->stats_date_to,
            'compare_from' => $context->employee->stats_compare_from,
            'compare_to' => $context->employee->stats_compare_to,
            'dashboard_use_push' => $usePush,
            'extra' => $extra,
        ];

        return Hook::exec('dashboardData', $params, $moduleId, true, true, $usePush);
    }

    /**
     * {@inheritdoc}
     */
    public function getD3JavascriptPath(): string
    {
        return _PS_JS_DIR_ . 'vendor/d3.v3.min.js';
    }

    /**
     * {@inheritdoc}
     */
    public function handleModuleConfigurationSave(Request $request): array
    {
        $result = ['errors' => [], 'has_errors' => false];
        $module = $request->request->get('module');
        $hook = $request->request->get('hook');
        $context = $this->context->getContext();

        if ($this->validate->isModuleName($module) && $module_obj = Module::getInstanceByName($module)) {
            $result['errors'] = $module_obj->handleDashConfigUpdate($request);
            if (count($result['errors'])) {
                $result['has_errors'] = true;
            }
        }

        $params = [
            'date_from' => $context->employee->stats_date_from,
            'date_to' => $context->employee->stats_date_to,
        ];

        if (LegacyValidate::isHookName($hook) && isset($module_obj) && method_exists($module_obj, $hook)) {
            $result['widget_html'] = $module_obj->$hook($params);
        }

        return $result;
    }
}
