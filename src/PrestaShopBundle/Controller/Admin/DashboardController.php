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
namespace PrestaShopBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller responsible of display of Back Office Dashboard pages.
 */
class DashboardController extends FrameworkBundleAdminController
{
    /**
     * @var Request
     * @return array
     * @Template("@PrestaShop/Admin/dashboard.html.twig")
     */
    public function indexAction(Request $request)
    {
        return [
            'layoutHeaderToolbarBtn' => $this->getDemoModeButton(),
            'layoutTitle' => $this->trans('Dashboard','Admin.Navigation.Menu'),
            'requireAddonsSearch' => true,
            'requireBulkActions' => false,
            'showContentHeader' => true,
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink('AdminDashboard'),
            'requireFilterStatus' => false,
            'warning' => false,
            'newVersionUrl' => '',
            'langIso' => 'EN',
            'params' => [
                'date_from' => new \DateTime(),
                'date_to' => new \DateTime()
            ],
        ];
    }

    /**
     * Displays the main configuration form for Dashboard.
     * @param Request $request
     * @return Response
     */
    public function configurationAction(Request $request)
    {
        return new Response('Dashboard configuration page');
    }

    /**
     * Process the main configuration form for Dashboard.
     * @param Request $request
     * @return RedirectResponse
     */
    public function processConfigurationAction(Request $request)
    {
        $this->addFlash('success', 'Update done');

        return $this->redirectToRoute('admin_dashboard');
    }

    public function processDemoModeButtonAction(Request $request)
    {
        $this->addFlash('success', 'Update on Demo button done');

        return $this->redirectToRoute('admin_dashboard');
    }

    /**
     * Returns the Demo mode button
     * @return array
     */
    private function getDemoModeButton()
    {
        $isDashboardSimulation = $this->get('prestashop.adapter.legacy.configuration')
            ->getBoolean('PS_DASHBOARD_SIMULATION')
        ;

        return [
            'switch_demo' => [
                'desc' => $this->trans('Demo mode', 'Admin.Dashboard.Feature'),
                'icon' => 'toggle_'. ($isDashboardSimulation ? 'on' : 'off'),
                'help' => $this->trans('This mode displays sample data so you can try your dashboard without real numbers.', 'Admin.Dashboard.Help')
            ]
        ];
    }
}
