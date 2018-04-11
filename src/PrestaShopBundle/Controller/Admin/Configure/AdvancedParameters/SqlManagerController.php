<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible of "Configure > Advanced Parameters > Database -> SQL Manager" page
 */
class SqlManagerController extends FrameworkBundleAdminController
{
    /**
     * Show list of saved SQL's
     *
     * @Template("@PrestaShop/Admin/Configure/AdvancedParameters/SqlManager/list.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function listAction(Request $request)
    {
        $legacyController = $request->attributes->get('_legacy_controller');

        $filters = $this->get('prestashop.core.admin.search_parameters')->getFiltersFromRequest($request, [
            'limit' => 10,
            'offset' => 0,
            'orderBy' => 'id_request_sql',
            'sortOrder' => 'desc',
            'filters' => [],
        ]);

        $repository = $this->getRepository();
        $requestSqls = $repository->findByFilters($filters);
        $requestSqlsCount = $repository->getCount();

        return [
            'layoutTitle' => $this->trans('SQL Manager', 'Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($legacyController),
            'request_sqls' => $requestSqls,
            'request_sqls_count' => $requestSqlsCount,
        ];
    }

    /**
     * Get request sql repository
     *
     * @return \PrestaShopBundle\Entity\Repository\RequestSqlRepository
     */
    protected function getRepository()
    {
        return $this->get('prestashop.core.admin.request_sql.repository');
    }
}
