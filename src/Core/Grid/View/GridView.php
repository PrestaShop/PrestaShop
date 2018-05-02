<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\View;

use Symfony\Component\Form\FormView;

/**
 * Class GridView is responsible for storing final grid data that is passed to template for rendering
 */
final class GridView
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FormView
     */
    private $filterFormView;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $bulkActions = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $pagination = [];

    /**
     * @var array
     */
    private $sorting = [];

    /**
     * Constructor accepts all required parameters for grid view
     *
     * @param string   $identifier  Grid identifier should be unique per grid and will act as ID on html table element
     * @param string   $name        Grid name
     * @param array    $columnViews Grid columns
     * @param FormView $formView  Filters form view
     */
    public function __construct($identifier, $name, array $columnViews, FormView $formView)
    {
        $this->columns = $columnViews;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->filterFormView = $formView;
    }

    /**
     * @param array $bulkActions
     */
    public function setBulkActions(array $bulkActions)
    {
        $this->bulkActions = $bulkActions;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getBulkActions()
    {
        return $this->bulkActions;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return FormView
     */
    public function getFilterForm()
    {
        return $this->filterFormView;
    }

    /**
     * Check if bulk actions are available
     */
    public function isBulkActionsAvailable()
    {
        return !empty($this->bulkActions) &&
            isset($this->data['rows_total']) &&
            count($this->data['rows_total']) > 1
        ;
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @param array $pagination
     */
    public function setPagination(array $pagination)
    {
        $this->pagination = $pagination;
    }

    /**
     * @return array
     */
    public function getSorting(): array
    {
        return $this->sorting;
    }

    /**
     * @param array $sorting
     */
    public function setSorting(array $sorting)
    {
        $this->sorting = $sorting;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }
}
