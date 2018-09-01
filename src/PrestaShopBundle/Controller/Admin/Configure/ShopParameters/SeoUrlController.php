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

namespace PrestaShopBundle\Controller\Admin\Configure\ShopParameters;

use PrestaShop\PrestaShop\Core\Search\Filters\SeoUrlsFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SeoUrlController is responsible for page display and all actions used in Configure -> Shop parameters ->
 * Traffic & Seo -> Seo & Urls tab
 */
class SeoUrlController extends FrameworkBundleAdminController
{
    /**
     * responsible for displaying page content
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @Template("@PrestaShop/Admin/Configure/ShopParameters/seo_urls.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request, SeoUrlsFilters $filters)
    {
        $seoUrlsGridFactory = $this->get('prestashop.core.grid.factory.seo_urls');
        $grid = $seoUrlsGridFactory->getGrid($filters);

        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');
        $presentedGrid = $gridPresenter->present($grid);

        return [
            'grid' => $presentedGrid,
        ];
    }
}
