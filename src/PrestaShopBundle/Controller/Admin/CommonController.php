<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use PrestaShopBundle\TransitionalBehavior\AdminPagePreferenceInterface;
use PrestaShopBundle\Service\DataProvider\Admin\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Admin controller for the common actions across the whole admin interface.
 *
 */
class CommonController extends FrameworkBundleAdminController
{
    /**
     * This will allow you to retrieve an HTML code with a ready and linked paginator.
     *
     * To be able to use this paginator, the current route must have these standard parameters:
     * - offset
     * - limit
     * Both will be automatically manipulated by the paginator.
     * The navigator links (previous/next page...) will never tranfer POST and/or GET parameters
     * (only route parameters that are in the URL).
     *
     * You must add a JS file to the list of JS for view rendering: pagination.js
     *
     * The final way to render a paginator is the following:
     * {% render controller('PrestaShopBundle\\Controller\\Admin\\CommonController::paginationAction',
     *   {'limit': limit, 'offset': offset, 'total': product_count, 'caller_parameters': pagination_parameters}) %}
     *
     * @Template
     * @param Request $request
     * @param integer $limit
     * @param integer $offset
     * @param integer $total
     * @return array Template vars
     */
    public function paginationAction(Request $request, $limit = 10, $offset = 0, $total = 0)
    {
        // base elements
        if ($limit <= 0) {
            $limit = 10;
        }
        $currentPage = floor($offset/$limit)+1;
        $pageCount = ceil($total/$limit);
        $from = $offset;
        $to = $offset+$limit-1;

        // urls from route
        $callerParameters = $request->attributes->get('caller_parameters', array());
        foreach ($callerParameters as $k => $v) {
            if (strpos($k, '_') === 0) {
                unset($callerParameters[$k]);
            }
        }
        $routeName = $request->attributes->get('caller_route', $request->attributes->get('caller_parameters')['_route']);
        $nextPageUrl = ($offset+$limit >= $total) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => min($total-1, $offset+$limit),
                'limit' => $limit
            )
        ));
        $previousPageUrl = ($offset == 0) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => max(0, $offset-$limit),
                'limit' => $limit
            )
        ));
        $firstPageUrl = ($offset == 0) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 0,
                'limit' => $limit
            )
        ));
        $lastPageUrl = ($offset+$limit >= $total) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => ($pageCount-1)*$limit,
                'limit' => $limit
            )
        ));
        $changeLimitUrl = $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 0,
                'limit' => '_limit'
            )
        ));

        // Template vars injection
        return array(
            'limit' => $limit,
            'changeLimitUrl' => $changeLimitUrl,
            'first_url' => $firstPageUrl,
            'previous_url' => $previousPageUrl,
            'from' => $from,
            'to' => $to,
            'total' => $total,
            'current_page' => $currentPage,
            'page_count' => $pageCount,
            'next_url' => $nextPageUrl,
            'last_url' => $lastPageUrl
        );
    }
}
