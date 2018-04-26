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

namespace PrestaShop\PrestaShop\Core\Grid\Search;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\HttpFoundation\Request;

class SearchParametersFactory implements SearchParametersFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromRequest(Request $request, GridDefinitionInterface $definition)
    {
        $limit = $request->query->get('limit', 10);
        $offset = $request->query->get('offset', 0);
        $orderBy = $request->query->get('orderBy', $definition->getDefaultOrderBy());
        $orderWay = $request->query->get('sortOrder', $definition->getDefaultOrderWay());

        $filters = [];
        if ($data = $request->get($definition->getIdentifier())) {
            foreach ($definition->getColumns() as $column) {
                $identifier = $column->getIdentifier();

                if (!empty($value = $data[$identifier])) {
                    $filters[$identifier] = $value;
                }
            }
        }

        return new SearchParameters(
            $limit,
            $offset,
            $orderBy,
            $orderWay,
            $filters
        );
    }
}
