<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminFilter.
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="admin_filter_search_id_idx",columns={"employee", "shop", "controller", "action", "filter_id"})})
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AdminFilterRepository")
 */
class AdminFilter
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="employee", type="integer")
     */
    private $employee;

    /**
     * @var int
     *
     * @ORM\Column(name="shop", type="integer")
     */
    private $shop;

    /**
     * @var string
     *
     * @ORM\Column(name="controller", type="string", length=60)
     */
    private $controller;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=100)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="filter", type="text")
     */
    private $filter;

    /**
     * @var string
     *
     * @ORM\Column(name="filter_id", type="string", length=255)
     */
    private $filterId = '';

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set employee.
     *
     * @param int $employee
     *
     * @return AdminFilter
     */
    public function setEmployee($employee)
    {
        $this->employee = $employee;

        return $this;
    }

    /**
     * Get employee.
     *
     * @return int
     */
    public function getEmployee()
    {
        return $this->employee;
    }

    /**
     * Set shop.
     *
     * @param int $shop
     *
     * @return AdminFilter
     */
    public function setShop($shop)
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * Get shop.
     *
     * @return int
     */
    public function getShop()
    {
        return $this->shop;
    }

    /**
     * Set controller.
     *
     * @param string $controller
     *
     * @return AdminFilter
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller.
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action.
     *
     * @param string $action
     *
     * @return AdminFilter
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set filter.
     *
     * @param string $filter
     *
     * @return AdminFilter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get filter.
     *
     * @return string
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return string
     */
    public function getFilterId()
    {
        return $this->filterId;
    }

    /**
     * @param string $filterId
     *
     * @return AdminFilter
     */
    public function setFilterId($filterId)
    {
        $this->filterId = $filterId;

        return $this;
    }

    /**
     * Gets an array with each filter key needed by Product catalog page.
     *
     * Values are filled with empty strings.
     *
     * @return array
     */
    public static function getProductCatalogEmptyFilter()
    {
        return array(
            'filter_category' => '',
            'filter_column_id_product' => '',
            'filter_column_name' => '',
            'filter_column_reference' => '',
            'filter_column_name_category' => '',
            'filter_column_price' => '',
            'filter_column_sav_quantity' => '',
            'filter_column_active' => '',
            'last_offset' => 0,
            'last_limit' => 20,
            'last_orderBy' => 'id_product',
            'last_sortOrder' => 'desc',
        );
    }

    /**
     * Gets an array with filters needed by Product catalog page.
     *
     * The data is decoded and filled with empty strings if there is no value on each entry
     * .
     *
     * @return array
     */
    public function getProductCatalogFilter()
    {
        $decoded = json_decode($this->getFilter(), true);

        return array_merge(
            $this->getProductCatalogEmptyFilter(),
            $decoded
        );
    }

    /**
     * Set the filters for Product catalog page into $this->filter.
     *
     * Filters input data to keep only Product catalog filters, and encode it.
     *
     * @param $filter
     *
     * @return AdminFilter tis object for fluent chaining
     */
    public function setProductCatalogFilter($filter)
    {
        $filter = array_intersect_key(
            $filter,
            $this->getProductCatalogEmptyFilter()
        );
        $filter = self::sanitizeFilterParameters($filter);

        return $this->setFilter(json_encode($filter));
    }

    /**
     * Sanitize filter parameters.
     *
     * @param $filter
     *
     * @return mixed
     */
    public static function sanitizeFilterParameters(array $filter)
    {
        $filterMinMax = function ($filter) {
            return function ($subject) use ($filter) {
                $operator = null;

                if (false !== strpos($subject, '<=')) {
                    $operator = '<=';
                }

                if (false !== strpos($subject, '>=')) {
                    $operator = '>=';
                }

                if (null === $operator) {
                    $pattern = '#BETWEEN (?P<min>\d+\.?\d*) AND (?P<max>\d+\.?\d*)#';
                    if (0 === preg_match($pattern, $subject, $matches)) {
                        return '';
                    }

                    return sprintf('BETWEEN %f AND %f', $matches['min'], $matches['max']);
                } else {
                    $subjectWithoutOperator = str_replace($operator, '', $subject);

                    $flag = FILTER_DEFAULT;
                    if ($filter === FILTER_SANITIZE_NUMBER_FLOAT) {
                        $flag = FILTER_FLAG_ALLOW_FRACTION;
                    }

                    $filteredSubjectWithoutOperator = filter_var($subjectWithoutOperator, $filter, $flag);
                    if (!$filteredSubjectWithoutOperator) {
                        $filteredSubjectWithoutOperator = 0;
                    }

                    return $operator . $filteredSubjectWithoutOperator;
                }
            };
        };

        return filter_var_array($filter, array(
            'filter_category' => FILTER_SANITIZE_NUMBER_INT,
            'filter_column_id_product' => array(
                'filter' => FILTER_CALLBACK,
                'options' => $filterMinMax(FILTER_SANITIZE_NUMBER_INT),
            ),
            'filter_column_name' => FILTER_SANITIZE_STRING,
            'filter_column_reference' => FILTER_SANITIZE_STRING,
            'filter_column_name_category' => FILTER_SANITIZE_STRING,
            'filter_column_price' => array(
                'filter' => FILTER_CALLBACK,
                'options' => $filterMinMax(FILTER_SANITIZE_NUMBER_FLOAT),
            ),
            'filter_column_sav_quantity' => array(
                'filter' => FILTER_CALLBACK,
                'options' => $filterMinMax(FILTER_SANITIZE_NUMBER_INT),
            ),
            'filter_column_active' => FILTER_SANITIZE_NUMBER_INT,
            'last_offset' => FILTER_SANITIZE_NUMBER_INT,
            'last_limit' => FILTER_SANITIZE_NUMBER_INT,
            'last_orderBy' => FILTER_SANITIZE_STRING,
            'last_sortOrder' => FILTER_SANITIZE_STRING,
        ));
    }
}
