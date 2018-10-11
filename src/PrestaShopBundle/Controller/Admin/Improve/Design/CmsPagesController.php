<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Controller\Admin\Improve\Design;

use PrestaShop\PrestaShop\Core\Search\Filters\CmsCategoryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CmsPagesController is responsible for handling the logic in "Improve -> Design -> pages" page.
 */
class CmsPagesController extends FrameworkBundleAdminController
{
    /**
     * @Template("@PrestaShop/Admin/Improve/Design/Cms/index.html.twig")
     *
     * @param CmsCategoryFilters $filters
     *
     * @return array
     */
    public function indexAction(CmsCategoryFilters $filters)
    {
        $cmsCategoryGridFactory = $this->get('prestashop.core.grid.factory.cms_pages_category');
        $cmsCategoryGrid = $cmsCategoryGridFactory->getGrid($filters);
        
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'cmsCategoryGrid' => $gridPresenter->present($cmsCategoryGrid),
        ];
    }

    /**
     * Implements filtering for the cms page category list.
     *
     * @param int $cmsCategoryParentId
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchCategory($cmsCategoryParentId, Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.cms_pages_category');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];
        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_cms_pages_index', compact('cmsCategoryParentId', 'filters'));
    }

    public function editCmsCategoryAction()
    {
    }
}
