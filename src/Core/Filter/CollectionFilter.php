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

namespace PrestaShop\PrestaShop\Core\Filter;

/**
 * Iterates over a collection, filtering each element using a queue of filters
 */
class CollectionFilter implements FilterInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

    /**
     * Sets process queue
     *
     * @param FilterInterface[] $filters
     *
     * @return $this
     *
     * @throws FilterException
     */
    public function queue(array $filters)
    {
        foreach ($filters as $filter) {
            if (!$filter instanceof FilterInterface) {
                throw new FilterException(
                    sprintf("The provided filter is not valid filter: \"%s\"", print_r($filter, true))
                );
            }
        }

        $this->filters = $filters;

        return $this;
    }

    /**
     * Filters the provided subject
     *
     * @param array $subject Collection to filter
     *
     * @return array
     *
     * @throws FilterException
     */
    public function filter($subject)
    {
        if (!is_array($subject)) {
            throw new FilterException(
                sprintf("Invalid subject: %s", print_r($subject, true))
            );
        }

        foreach ($subject as $k => $value) {
            foreach ($this->filters as $filter) {
                $value = $filter->filter($value);
            }
            $subject[$k] = $value;
        }

        return $subject;
    }
}
