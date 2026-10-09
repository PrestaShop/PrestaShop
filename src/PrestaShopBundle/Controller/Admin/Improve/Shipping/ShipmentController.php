<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Improve\Shipping;

use Doctrine\DBAL\Exception as DBALException;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ShipmentGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ShipmentFilters;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manages the "Improve > Shipping > Shipments" page, listing every shipment of the shop regardless
 * of the order it belongs to.
 *
 * The page only exists while the improved shipment feature is on, since no shipment is created at
 * all otherwise.
 *
 * There is no legacy controller behind this page, so permissions are checked against the AdminShipments
 * tab name directly. The `_legacy_controller` route default is deliberately absent: it exists to pair a
 * legacy page with its Symfony replacement during a migration, which does not apply here.
 */
class ShipmentController extends PrestaShopAdminController
{
    /**
     * @throws DBALException if the shipments cannot be read from the database
     * @throws NotFoundHttpException if the improved shipment feature is disabled
     */
    #[AdminSecurity("is_granted('read', 'AdminShipments')")]
    public function indexAction(
        ShipmentFilters $filters,
        #[Autowire(service: 'PrestaShop\PrestaShop\Core\Grid\Factory\ShipmentFactory')]
        GridFactoryInterface $shipmentGridFactory,
        FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ): Response {
        $this->assertImprovedShipmentIsEnabled($featureFlagStateChecker);

        return $this->render('@PrestaShop/Admin/Improve/Shipping/Shipment/index.html.twig', [
            'shipmentGrid' => $this->presentGrid($shipmentGridFactory->getGrid($filters)),
            'help_link' => $this->generateSidebarLink('AdminShipments'),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Shipments', [], 'Admin.Global'),
        ]);
    }

    /**
     * Persists the grid filters and redirects back to the listing.
     *
     * CommonController::searchGridAction would do the same, but its own permission check reads the
     * `_legacy_controller` route default, which this page has no reason to declare.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[AdminSecurity("is_granted('read', 'AdminShipments')")]
    public function searchAction(
        Request $request,
        #[Autowire(service: 'PrestaShop\PrestaShop\Core\Grid\Definition\Factory\ShipmentGridDefinitionFactory')]
        GridDefinitionFactoryInterface $gridDefinitionFactory,
    ): RedirectResponse {
        return $this->buildSearchResponse(
            $gridDefinitionFactory,
            $request,
            ShipmentGridDefinitionFactory::GRID_ID,
            'admin_shipments_index'
        );
    }

    /**
     * Generates a single delivery slip gathering every selected shipment, across orders.
     *
     * The submitted ids are narrowed down to what the caller could actually have been shown before
     * they reach the generator: it trusts whatever it is handed, and the listing they come from is
     * scoped on the context shops while a POST body is not.
     *
     * @throws CoreException if the generator is handed an empty selection, which the guards below
     *                       already rule out
     * @throws DBALException if the selected shipments cannot be read from the database
     * @throws NotFoundHttpException if the improved shipment feature is disabled
     * @throws RuntimeException if a shipment that passed the narrowing, or the order it belongs to,
     *                          cannot be loaded by the generator
     */
    #[AdminSecurity("is_granted('read', 'AdminShipments')")]
    public function generateDeliverySlipsPdfAction(
        Request $request,
        #[Autowire(service: 'prestashop.adapter.pdf.shipment_delivery_slip_pdf_generator')]
        PDFGeneratorInterface $shipmentDeliverySlipPdfGenerator,
        ShipmentRepository $shipmentRepository,
        ShopContext $shopContext,
        FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ): BinaryFileResponse|RedirectResponse {
        $this->assertImprovedShipmentIsEnabled($featureFlagStateChecker);

        $requested = array_map('intval', $request->request->all('shipment_bulk'));

        if (empty($requested)) {
            $this->addFlash(
                'error',
                $this->trans('You must select at least one shipment.', [], 'Admin.Notifications.Error')
            );

            return $this->redirectToRoute('admin_shipments_index');
        }

        // Scoped exactly like the listing the ids come from, so the two can never disagree.
        $printable = $shipmentRepository->findPrintableIds($requested, $shopContext->getAssociatedShopIds());

        if (empty($printable)) {
            $this->addFlash(
                'error',
                $this->trans(
                    'There is no fulfilled shipment to download',
                    [],
                    'Admin.Notifications.Error'
                )
            );

            return $this->redirectToRoute('admin_shipments_index');
        }

        if (count($printable) < count($requested)) {
            $this->addFlash(
                'warning',
                $this->trans(
                    'Only fulfilled shipments have a delivery slip, so %count% of the selected shipments were skipped.',
                    ['%count%' => count($requested) - count($printable)],
                    'Admin.Orderscustomers.Notification'
                )
            );
        }

        return new BinaryFileResponse($shipmentDeliverySlipPdfGenerator->generatePDF($printable));
    }

    /**
     * @throws NotFoundHttpException if the improved shipment feature is disabled
     */
    private function assertImprovedShipmentIsEnabled(FeatureFlagStateCheckerInterface $featureFlagStateChecker): void
    {
        if (!$featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_IMPROVED_SHIPMENT)) {
            throw new NotFoundHttpException('The improved shipment feature is disabled.');
        }
    }
}
