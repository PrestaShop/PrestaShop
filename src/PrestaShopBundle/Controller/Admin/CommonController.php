<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Adapter\Module\AdminModuleDataProvider;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Service\DataProvider\Admin\RecommendedModules;

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
     * @param string $view full|quicknav To change default template used to render the content
     * @return array Template vars
     */
    public function paginationAction(Request $request, $limit = 10, $offset = 0, $total = 0, $view = 'full')
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
        $routeName = $request->attributes->get('caller_route', $request->attributes->get('caller_parameters', ['_route' => false])['_route']);
        $nextPageUrl = (!$routeName || ($offset+$limit >= $total)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => min($total-1, $offset+$limit),
                'limit' => $limit
            )
        ));
        $previousPageUrl = (!$routeName || ($offset == 0)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => max(0, $offset-$limit),
                'limit' => $limit
            )
        ));
        $firstPageUrl = (!$routeName || ($offset == 0)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 0,
                'limit' => $limit
            )
        ));
        $lastPageUrl = (!$routeName || ($offset+$limit >= $total)) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => ($pageCount-1)*$limit,
                'limit' => $limit
            )
        ));
        $changeLimitUrl = (!$routeName) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 0,
                'limit' => '_limit'
            )
        ));
        $jumpPageUrl = (!$routeName) ? false : $this->generateUrl($routeName, array_merge(
            $callerParameters,
            array(
                'offset' => 999999,
                'limit' => $limit
            )
        ));
        $limitChoices = $request->attributes->get('limit_choices', array(20, 50, 100));

        // Template vars injection
        $vars = array(
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
            'last_url' => $lastPageUrl,
            'jump_page_url' => $jumpPageUrl,
            'limit_choices' => $limitChoices,
        );
        if ($view != 'full') {
            return $this->render('PrestaShopBundle:Admin:Common/pagination_'.$view.'.html.twig', $vars);
        }
        return $vars;
    }

    /**
     * This will allow you to retrieve an HTML code with a list of recommended modules depending on the domain.
     *
     * @Template
     * @param string $domain
     * @param integer $limit
     * @param integer $randomize
     * @return array Template vars
     */
    public function recommendedModulesAction($domain, $limit = 0, $randomize = 0)
    {
        $recommendedModules = $this->container->get('prestashop.data_provider.modules.recommended');
        /* @var $recommendedModules RecommendedModules */
        $moduleIdList = $recommendedModules->getRecommendedModuleIdList($domain, ($randomize == 1));

        $modulesProvider = $this->container->get('prestashop.core.admin.data_provider.module_interface');
        /* @var $modulesProvider AdminModuleDataProvider */
        $modulesRepository = ModuleManagerBuilder::getInstance()->buildRepository();

        $modules = array();
        foreach ($moduleIdList as $id) {
            try {
                $module = $modulesRepository->getModule($id);
            } catch (\Exception $e) {
                continue;
            }
            $modules[] = $module;
        }

        if ($randomize == 1) {
            shuffle($modules);
        }

        $modules = $recommendedModules->filterInstalledAndBadModules($modules);
        $modules = $modulesProvider->generateAddonsUrls($modules);

        return array(
            'domain' => $domain,
            'modules' => array_slice($modules, 0, $limit, true),
        );
    }

    /**
     * Render a right sidebar with content from an URL
     */
    public function renderSidebarAction($url, $title = '', $footer = '')
    {
        $tools = $this->container->get('prestashop.adapter.tools');

        return $this->render('PrestaShopBundle:Admin:Common/_partials/_sidebar.html.twig', [
            'footer' => $tools->purifyHTML($footer),
            'title' => $title,
            'url' => urldecode($url),
        ]);
    }
}
