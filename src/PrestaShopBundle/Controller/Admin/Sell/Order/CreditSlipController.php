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

namespace PrestaShopBundle\Controller\Admin\Sell\Order;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Exception\CreditSlipNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\Query\GetCreditSlipIdsByDateRange;
use PrestaShop\PrestaShop\Core\Domain\CreditSlip\ValueObject\CreditSlipId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\PDF\Exception\MissingDataException;
use PrestaShop\PrestaShop\Core\Search\Filters\CreditSlipFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip\GeneratePdfByDateType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Orders > Credit slips page
 */
class CreditSlipController extends FrameworkBundleAdminController
{
    /**
     * Show credit slips listing page.
     *
     * @param Request $request
     * @param CreditSlipFilters $creditSlipFilters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(
        Request $request,
        CreditSlipFilters $creditSlipFilters
    ) {
        $creditSlipGridFactory = $this->get('prestashop.core.grid.factory.credit_slip');
        $creditSlipGrid = $creditSlipGridFactory->getGrid($creditSlipFilters);

        $creditSlipOptionsFormHandler = $this->getSlipOptionsFormHandler();
        $creditSlipOptionsForm = $creditSlipOptionsFormHandler->getForm();
        $creditSlipOptionsForm->handleRequest($request);

        if ($creditSlipOptionsForm->isSubmitted() && $creditSlipOptionsForm->isValid()) {
            $errors = $creditSlipOptionsFormHandler->save($creditSlipOptionsForm->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Update successful', 'Admin.Notifications.Success'));
            } else {
                $this->flashErrors($errors);
            }

            return $this->redirectToRoute('admin_credit_slips_index');
        }

        $pdfByDateForm = $this->createForm(GeneratePdfByDateType::class, [], [
            'method' => Request::METHOD_GET,
        ]);
        $pdfByDateForm->handleRequest($request);

        return $this->render('@PrestaShop/Admin/Sell/Order/CreditSlip/index.html.twig', [
            'enableSidebar' => true,
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'creditSlipGrid' => $this->presentGrid($creditSlipGrid),
            'pdfByDateForm' => $pdfByDateForm->createView(),
            'creditSlipOptionsForm' => $creditSlipOptionsForm->createView(),
            'layoutTitle' => $this->trans('Credit slips', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Generates PDF of requested credit slip by provided id
     *
     * @param int $creditSlipId
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function generatePdfAction($creditSlipId)
    {
        try {
            $creditSlipId = new CreditSlipId((int) $creditSlipId);

            return new Response($this->get('prestashop.adapter.pdf.credit_slip_pdf_generator')->generatePDF([$creditSlipId]));
        } catch (CoreException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->redirectToRoute('admin_credit_slips_index');
    }

    /**
     * Generates PDF of credit slips found by requested date range
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function generatePdfByDateAction(Request $request)
    {
        $pdfByDateForm = $this->createForm(GeneratePdfByDateType::class, [], ['method' => Request::METHOD_GET]);
        $pdfByDateForm->handleRequest($request);

        if ($pdfByDateForm->isSubmitted() && $pdfByDateForm->isValid()) {
            try {
                $dateRange = $pdfByDateForm->getData();
                $slipIds = $this->getQueryBus()->handle(new GetCreditSlipIdsByDateRange(
                    new DateTime($dateRange['from']),
                    new DateTime($dateRange['to'])
                ));

                return new Response($this->get('prestashop.adapter.pdf.credit_slip_pdf_generator')->generatePDF($slipIds));
            } catch (CoreException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
            }
        }

        return $this->redirectToRoute('admin_credit_slips_index');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getSlipOptionsFormHandler()
    {
        return $this->get('prestashop.admin.credit_slip_options.form_handler');
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages()
    {
        return [
            CreditSlipNotFoundException::class => [
                CreditSlipNotFoundException::BY_DATE_RANGE => $this->trans(
                    'No order slips were found for this period.',
                    'Admin.Orderscustomers.Notification'
                ),
            ],
            MissingDataException::class => $this->trans(
                'The object cannot be loaded (or found).',
                'Admin.Notifications.Error'
            ),
        ];
    }
}
