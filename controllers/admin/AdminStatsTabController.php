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
abstract class AdminStatsTabControllerCore extends AdminController
{
    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        $this->action = 'view';
        $this->display = 'view';
    }

    /**
     * AdminController::initContent() override.
     *
     * @see AdminController::initContent()
     */
    public function initContent()
    {
        if ($this->ajax) {
            return;
        }

        $this->toolbar_title = $this->trans('Stats', [], 'Admin.Stats.Feature');

        if ($this->display == 'view') {
            // Some controllers use the view action without an object
            if ($this->className) {
                $this->loadObject(true);
            }
            $this->content .= $this->renderView();
        }

        $this->content .= $this->displayMenu();
        $this->content .= $this->displayCalendar();
        $this->content .= $this->displayStats();

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['back']);
    }

    public function displayCalendar()
    {
        return AdminStatsTabController::displayCalendarForm([
            'Calendar' => $this->trans('Calendar', [], 'Admin.Global'),
            'Day' => $this->trans('Day', [], 'Admin.Global'),
            'Month' => $this->trans('Month', [], 'Admin.Global'),
            'Year' => $this->trans('Year', [], 'Admin.Global'),
            'From' => $this->trans('From:', [], 'Admin.Global'),
            'To' => $this->trans('To:', [], 'Admin.Global'),
            'Save' => $this->trans('Save', [], 'Admin.Global'),
        ], $this->token);
    }

    public static function displayCalendarForm($translations, $token, $action = null, $table = null, $identifier = null, $id = null)
    {
        $context = Context::getContext();

        $tpl = $context->controller->createTemplate('calendar.tpl');

        $context->controller->addJqueryUI('ui.datepicker');

        if ($identifier === null && Tools::getValue('module')) {
            $identifier = 'module';
            $id = Tools::getValue('module');
        }

        $action = Context::getContext()->link->getAdminLink('AdminStats');
        $action .= ($action && $table ? '&' . Tools::safeOutput($action) : '');
        $action .= ($identifier && $id ? '&' . Tools::safeOutput($identifier) . '=' . (int) $id : '');
        $module = Tools::getValue('module');
        $action .= ($module ? '&module=' . Tools::safeOutput($module) : '');
        $action .= (($id_product = Tools::getValue('id_product')) ? '&id_product=' . Tools::safeOutput($id_product) : '');
        $tpl->assign([
            'current' => self::$currentIndex,
            'token' => $token,
            'action' => $action,
            'table' => $table,
            'identifier' => $identifier,
            'id' => $id,
            'translations' => $translations,
            'datepickerFrom' => Tools::getValue('datepickerFrom', $context->employee->stats_date_from),
            'datepickerTo' => Tools::getValue('datepickerTo', $context->employee->stats_date_to),
        ]);

        return $tpl->fetch();
    }

    /* Not used anymore, but still work */
    protected function displayEngines()
    {
        $tpl = $this->createTemplate('engines.tpl');

        $autoclean_period = [
            'never' => $this->trans('Never', [], 'Admin.Global'),
            'week' => $this->trans('Week', [], 'Admin.Global'),
            'month' => $this->trans('Month', [], 'Admin.Global'),
            'year' => $this->trans('Year', [], 'Admin.Global'),
        ];

        $tpl->assign([
            'current' => self::$currentIndex,
            'token' => $this->token,
            'graph_engine' => Configuration::get('PS_STATS_RENDER'),
            'grid_engine' => Configuration::get('PS_STATS_GRID_RENDER'),
            'auto_clean' => Configuration::get('PS_STATS_OLD_CONNECT_AUTO_CLEAN'),
            'array_graph_engines' => ModuleGraphEngine::getGraphEngines(),
            'array_grid_engines' => ModuleGridEngine::getGridEngines(),
            'array_auto_clean' => $autoclean_period,
        ]);

        return $tpl->fetch();
    }

    public function displayMenu()
    {
        $tpl = $this->createTemplate('menu.tpl');

        $modules = $this->getModules();
        $module_instance = [];
        foreach ($modules as $m => $module) {
            if ($module_instance[$module['name']] = Module::getInstanceByName($module['name'])) {
                $modules[$m]['displayName'] = $module_instance[$module['name']]->displayName;
            } else {
                unset(
                    $module_instance[$module['name']],
                    $modules[$m]
                );
            }
        }

        uasort($modules, [$this, 'checkModulesNames']);

        $tpl->assign([
            'current' => self::$currentIndex,
            'current_module_name' => Tools::getValue('module', 'statsforecast'),
            'token' => $this->token,
            'modules' => $modules,
            'module_instance' => $module_instance,
        ]);

        return $tpl->fetch();
    }

    public function checkModulesNames($a, $b): int
    {
        return strcasecmp($a['displayName'], $b['displayName']);
    }

    protected function getModules()
    {
        $moduleList = Hook::getHookModuleExecList('displayAdminStatsModules');
        if (true === is_array($moduleList)) {
            return array_map(
                function ($moduleArray) {
                    return ['name' => $moduleArray['module']];
                },
                $moduleList
            );
        }

        return [];
    }

    public function displayStats()
    {
        $tpl = $this->createTemplate('stats.tpl');

        if ((!($module_name = Tools::getValue('module')) || !Validate::isModuleName($module_name)) && ($module_instance = Module::getInstanceByName('statsforecast')) && $module_instance->active) {
            $module_name = 'statsforecast';
        }

        if ($module_name) {
            $_GET['module'] = $module_name;

            if (!isset($module_instance)) {
                $module_instance = Module::getInstanceByName($module_name);
            }

            if ($module_instance && $module_instance->active) {
                $hook = Hook::exec('displayAdminStatsModules', [], $module_instance->id);
            }
        }

        $tpl->assign([
            'module_name' => $module_name,
            'module_instance' => isset($module_instance) ? $module_instance : null,
            'hook' => isset($hook) ? $hook : null,
        ]);

        return $tpl->fetch();
    }

    public function postProcess()
    {
        $this->context = Context::getContext();

        $this->processDateRange();

        if (Tools::getValue('submitSettings')) {
            if ($this->access('edit')) {
                self::$currentIndex .= '&module=' . Tools::getValue('module');
                Configuration::updateValue('PS_STATS_RENDER', Tools::getValue('PS_STATS_RENDER', Configuration::get('PS_STATS_RENDER')));
                Configuration::updateValue('PS_STATS_GRID_RENDER', Tools::getValue('PS_STATS_GRID_RENDER', Configuration::get('PS_STATS_GRID_RENDER')));
                Configuration::updateValue('PS_STATS_OLD_CONNECT_AUTO_CLEAN', Tools::getValue('PS_STATS_OLD_CONNECT_AUTO_CLEAN', Configuration::get('PS_STATS_OLD_CONNECT_AUTO_CLEAN')));
            } else {
                $this->errors[] = $this->trans('You do not have permission to edit this.', [], 'Admin.Notifications.Error');
            }
        }
    }

    public function processDateRange()
    {
        if (Tools::isSubmit('submitDatePicker')) {
            if ((!Validate::isDate($from = Tools::getValue('datepickerFrom')) || !Validate::isDate($to = Tools::getValue('datepickerTo'))) || (strtotime($from) > strtotime($to))) {
                $this->errors[] = $this->trans('The specified date is invalid.', [], 'Admin.Stats.Notification');
            }
        }
        if (Tools::isSubmit('submitDateDay')) {
            $from = date('Y-m-d');
            $to = date('Y-m-d');
        }
        if (Tools::isSubmit('submitDateDayPrev')) {
            $yesterday = time() - 60 * 60 * 24;
            $from = date('Y-m-d', $yesterday);
            $to = date('Y-m-d', $yesterday);
        }
        if (Tools::isSubmit('submitDateMonth')) {
            $from = date('Y-m-01');
            $to = date('Y-m-t');
        }
        if (Tools::isSubmit('submitDateMonthPrev')) {
            $m = (date('m') == 1 ? 12 : date('m') - 1);
            $y = ($m == 12 ? date('Y') - 1 : date('Y'));
            $from = $y . '-' . $m . '-01';
            $to = $y . '-' . $m . date('-t', mktime(12, 0, 0, $m, 15, $y));
        }
        if (Tools::isSubmit('submitDateYear')) {
            $from = date('Y-01-01');
            $to = date('Y-12-31');
        }
        if (Tools::isSubmit('submitDateYearPrev')) {
            $from = (date('Y') - 1) . date('-01-01');
            $to = (date('Y') - 1) . date('-12-31');
        }
        if (isset($from, $to) && !count($this->errors)) {
            $this->context->employee->stats_date_from = $from;
            $this->context->employee->stats_date_to = $to;
            $this->context->employee->update();
            if (!$this->isXmlHttpRequest()) {
                Tools::redirectAdmin($_SERVER['REQUEST_URI']);
            }
        }
    }

    public function ajaxProcessSetDashboardDateRange()
    {
        $this->processDateRange();

        if ($this->isXmlHttpRequest()) {
            if (is_array($this->errors) && count($this->errors)) {
                die(json_encode(
                    [
                        'has_errors' => true,
                        'errors' => [$this->errors],
                        'date_from' => $this->context->employee->stats_date_from,
                        'date_to' => $this->context->employee->stats_date_to, ]
                ));
            } else {
                die(json_encode(
                    [
                        'has_errors' => false,
                        'date_from' => $this->context->employee->stats_date_from,
                        'date_to' => $this->context->employee->stats_date_to, ]
                    ));
            }
        }
    }

    protected function getDate()
    {
        $year = isset($this->context->cookie->stats_year) ? $this->context->cookie->stats_year : date('Y');
        $month = isset($this->context->cookie->stats_month) ? sprintf('%02d', $this->context->cookie->stats_month) : '%';
        $day = isset($this->context->cookie->stats_day) ? sprintf('%02d', $this->context->cookie->stats_day) : '%';

        return $year . '-' . $month . '-' . $day;
    }
}
