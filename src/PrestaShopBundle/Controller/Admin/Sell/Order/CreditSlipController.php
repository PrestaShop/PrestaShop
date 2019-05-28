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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use DateTime;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CreditSlipGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\PDF\Exception\MissingDataException;
use PrestaShop\PrestaShop\Core\Search\Filters\CreditSlipFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip\GeneratePdfByDateType;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Orders > Credit slips page
 */
class CreditSlipController extends FrameworkBundleAdminController
{
    /**
     * Show manufacturers listing page.
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     * @param CreditSlipFilters $creditSlipFilters
     *
     * @return Response
     */
    public function indexAction(
        Request $request,
        CreditSlipFilters $creditSlipFilters
    ) {
        $creditSlipGridFactory = $this->get('prestashop.core.grid.factory.credit_slip');
        $creditSlipGrid = $creditSlipGridFactory->getGrid($creditSlipFilters);
        $pdfByDateForm = $this->createForm(GeneratePdfByDateType::class, [], [
            'method' => Request::METHOD_GET,
        ]);
        $pdfByDateForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Sell/Order/CreditSlip/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'creditSlipGrid' => $this->presentGrid($creditSlipGrid),
            'pdfByDateForm' => $pdfByDateForm->createView(),
        ]);
    }

    /**
     * Provides filters functionality
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        /** @var ResponseBuilder $responseBuilder */
        $responseBuilder = $this->get('prestashop.bundle.grid.response_builder');

        return $responseBuilder->buildSearchResponse(
            $this->get('prestashop.core.grid.definition.factory.credit_slip'),
            $request,
            CreditSlipGridDefinitionFactory::GRID_ID,
            'admin_credit_slips_index'
        );
    }

    /**
     * @param int $creditSlipId
     *
     * @throws MissingDataException
     */
    public function generatePdfAction($creditSlipId)
    {
        try {
            die($this->get('prestashop.adapter.pdf.credit_slip_pdf_generator')->generatePDF([(int) $creditSlipId]));
        } catch (MissingDataException $e) {
            $this->addFlash(
                'error',
                $this->trans('The object cannot be loaded (or found)', 'Admin.Notifications.Error')
            );
        }

        return $this->redirectToRoute('admin_credit_slips_index');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function generatePdfByDateAction(Request $request)
    {
        $pdfByDateForm = $this->createForm(GeneratePdfByDateType::class, [], ['method' => Request::METHOD_GET]);
        $pdfByDateForm->handleRequest($request);

        if ($pdfByDateForm->isSubmitted() && $pdfByDateForm->isValid()) {
            $dateRange = $pdfByDateForm->getData();
            $creditSlipDataProvider = $this->get('prestashop.adapter.data_provider.credit_slip');

            $slipIds = $creditSlipDataProvider->getIdsByDateInterval(
                new DateTime($dateRange['from']),
                new DateTime($dateRange['to'])
            );

            try {
                $this->get('prestashop.adapter.pdf.credit_slip_pdf_generator')->generatePDF($slipIds);
//                die;
            } catch (MissingDataException $e) {
                $this->addFlash(
                    'error',
                    $this->trans('No credit slips found in this date range', 'Admin.Orderscustomers.Feature')
                );
            }
        }

        return $this->redirectToRoute('admin_credit_slips_index', [
            $pdfByDateForm->getName() => $pdfByDateForm->getData(),
        ]);
    }
}
