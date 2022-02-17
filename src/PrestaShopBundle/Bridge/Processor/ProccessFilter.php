<?php

namespace PrestaShopBundle\Bridge\Processor;

use Context;
use ObjectModel;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\Controller\ControllerConfiguration;
use PrestaShopBundle\Bridge\Helper\HelperListConfiguration;
use Symfony\Component\HttpFoundation\Request;
use Tools;
use Validate;

class ProccessFilter
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(LegacyContext $legacyContext)
    {
        $this->context = $legacyContext->getContext();
    }

    public function processFilter(
        Request $request,
        HelperListConfiguration $helperListConfiguration,
        ControllerConfiguration $controllerConfiguration
    ): void {
        //Hook::exec('action' . $this->controller_name . 'ListingFieldsModifier', [
        //    'fields' => &$this->fields_list,
        //]);

        $prefix = $this->getCookieFilterPrefix($controllerConfiguration);

        if (isset($helperListConfiguration->listId)) {
            foreach ($request->request->all() as $key => $value) {
                if ($value === '') {
                    unset($this->context->cookie->{$prefix . $key});
                } elseif (stripos($key, $helperListConfiguration->listId . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
            }

            foreach ($request->query->all() as $key => $value) {
                if (stripos($key, $helperListConfiguration->listId . 'Filter_') === 0) {
                    $this->context->cookie->{$prefix . $key} = !is_array($value) ? $value : json_encode($value);
                } elseif (stripos($key, 'submitFilter') === 0) {
                    $this->context->cookie->$key = !is_array($value) ? $value : json_encode($value);
                }
                if (stripos($key, $helperListConfiguration->listId . 'Orderby') === 0 && Validate::isOrderBy($value)) {
                    if ($value === '' || $value == $helperListConfiguration->defaultOrderBy) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                } elseif (stripos($key, $helperListConfiguration->listId . 'Orderway') === 0 && Validate::isOrderWay($value)) {
                    if ($value === '' || $value == $helperListConfiguration->defaultOrderWay) {
                        unset($this->context->cookie->{$prefix . $key});
                    } else {
                        $this->context->cookie->{$prefix . $key} = $value;
                    }
                }
            }
        }

        $filters = $this->context->cookie->getFamily($prefix . $helperListConfiguration->listId . 'Filter_');
        $definition = false;
        if (isset($this->className) && $this->className) {
            $definition = ObjectModel::getDefinition($this->className);
        }

        foreach ($filters as $key => $value) {
            /* Extracting filters from $_POST on key filter_ */
            if ($value != null && !strncmp($key, $prefix . $helperListConfiguration->listId . 'Filter_', 7 + Tools::strlen($prefix . $helperListConfiguration->listId))) {
                $key = Tools::substr($key, 7 + Tools::strlen($prefix . $helperListConfiguration->listId));
                /* Table alias could be specified using a ! eg. alias!field */
                $tmp_tab = explode('!', $key);
                $filter = count($tmp_tab) > 1 ? $tmp_tab[1] : $tmp_tab[0];

                if ($field = $this->filterToField($key, $filter)) {
                    $type = (array_key_exists('filter_type', $field) ? $field['filter_type'] : (array_key_exists('type', $field) ? $field['type'] : false));
                    if (($type == 'date' || $type == 'datetime') && is_string($value)) {
                        $value = json_decode($value, true);
                    }
                    $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';

                    // Assignment by reference
                    if (array_key_exists('havingFilter', $field)) {
                        $sql_filter = $helperListConfiguration->filterHaving;
                    } else {
                        $sql_filter = &$helperListConfiguration->filter;
                    }

                    /* Only for date filtering (from, to) */
                    if (is_array($value)) {
                        if (isset($value[0]) && !empty($value[0])) {
                            if (Validate::isDate($value[0])) {
                                $sql_filter .= ' AND ' . pSQL($key) . ' >= \'' . pSQL(Tools::dateFrom($value[0])) . '\'';
                            }
                        }

                        if (isset($value[1]) && !empty($value[1])) {
                            if (Validate::isDate($value[1])) {
                                $sql_filter .= ' AND ' . pSQL($key) . ' <= \'' . pSQL(Tools::dateTo($value[1])) . '\'';
                            }
                        }
                    } else {
                        $sql_filter .= ' AND ';
                        $check_key = ($key == $helperListConfiguration->identifier || $key == '`' . $helperListConfiguration->identifier . '`');
                        $alias = ($definition && !empty($definition['fields'][$filter]['shop'])) ? 'sa' : 'a';

                        if ($type == 'int' || $type == 'bool') {
                            $sql_filter .= (($check_key || $key == '`active`') ? $alias . '.' : '') . pSQL($key) . ' = ' . (int) $value . ' ';
                        } elseif ($type == 'decimal') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . (float) $value . ' ';
                        } elseif ($type == 'select') {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = \'' . pSQL($value) . '\' ';
                        } elseif ($type == 'price') {
                            $value = (float) str_replace(',', '.', $value);
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' = ' . $value . ' ';
                        } else {
                            $sql_filter .= ($check_key ? $alias . '.' : '') . pSQL($key) . ' LIKE \'%' . pSQL(trim($value)) . '%\' ';
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $key
     * @param string $filter
     *
     * @return array|false
     */
    private function filterToField($key, $filter)
    {
        if (!isset($this->fields_list)) {
            return false;
        }

        foreach ($this->fields_list as $field) {
            if (array_key_exists('filter_key', $field) && $field['filter_key'] == $key) {
                return $field;
            }
        }
        if (array_key_exists($filter, $this->fields_list)) {
            return $this->fields_list[$filter];
        }

        return false;
    }

    /**
     * Set the filters used for the list display.
     */
    private function getCookieFilterPrefix(ControllerConfiguration $controllerConfiguration)
    {
        return str_replace(['admin', 'controller'], '', Tools::strtolower($controllerConfiguration->controllerNameLegacy));
    }
}
