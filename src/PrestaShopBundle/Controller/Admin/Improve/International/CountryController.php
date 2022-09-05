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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Context;
use PrestaShop\PrestaShop\Core\Search\Filters\CountryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CountryController is responsible for handling "Improve > International > Locations > Countries"
 */
class CountryController extends FrameworkBundleAdminController
{
    /**
     * Show countries listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CountryFilters $filters
     *
     * @return Response
     */
    public function indexAction(Request $request, CountryFilters $filters): Response
    {
        $countryGridFactory = $this->get('prestashop.core.grid.factory.country');
        $countryGrid = $countryGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/Country/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'countryGrid' => $this->presentGrid($countryGrid),
            'enableSidebar' => true,
        ]);
    }

    /**
     * Display country edit form
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))")
     *
     * @param int $countryId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $countryId, Request $request): Response
    {
        //todo: complete edit action migration to symfony
        return $this->redirect(
            Context::getContext()->link->getAdminLink(
                'AdminCountries',
                true,
                [],
                [
                    'updatecountry' => '',
                    'id_country' => $countryId,
                ]
            )
        );
    }
}
