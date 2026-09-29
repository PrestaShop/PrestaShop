<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AllowCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\BulkRevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\ClearCspLogCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotAddCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotBulkRevokeCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CannotDeleteCspRuleException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspLogNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspRuleNotFoundException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CspLogFilters;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Csp\AddCspRuleType;
use PrestaShopBundle\Security\Attribute\AdminSecurity;
use PrestaShopBundle\Security\Attribute\DemoRestricted;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/** The "Advanced parameters > Security > Content Security Policy" page: a native page gated behind the 'csp' feature flag. */
class CspController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminSecurityCsp')")]
    public function indexAction(
        CspLogFilters $filters,
        CspFeatureChecker $featureChecker,
        CspRuleRepository $cspRuleRepository,
        ShopListResolverInterface $shopListResolver,
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.csp_log')]
        GridFactoryInterface $cspLogGridFactory,
    ): Response {
        $this->assertFeatureEnabled();

        $cspForm = $cspFormHandler->getForm();

        // Warn when the policy has weakening sources: under enforcement they give little XSS protection.
        // In an all-shops/group scope (no single shop id) the warning covers every shop in the scope.
        $shopConstraint = $this->getShopContext()->getShopConstraint();
        $shopId = $shopConstraint->getShopId()?->getValue();
        $isEnforcing = false;
        $hasWeakeningSources = false;
        $scopedShopIds = null !== $shopId ? [$shopId] : $shopListResolver->resolveShopIds($shopConstraint);
        foreach ($scopedShopIds as $scopedShopId) {
            if (!$featureChecker->isEnabledForShop($scopedShopId)) {
                continue;
            }
            if (!$featureChecker->isReportOnlyForShop($scopedShopId)) {
                $isEnforcing = true;
            }
            if ($cspRuleRepository->countWeakeningRulesByShop($scopedShopId) > 0) {
                $hasWeakeningSources = true;
            }
        }

        // A rule can only be added for a single shop, so the toolbar button is hidden in an
        // all-shops/group scope where AddCspRuleHandler would reject the command.
        $toolbarButtons = [];
        if (null !== $shopId) {
            $toolbarButtons['add'] = [
                'href' => $this->generateUrl('admin_security_csp_add'),
                'desc' => $this->trans('Add allowed source', [], 'Admin.Advparameters.Feature'),
                'icon' => 'add_circle_outline',
            ];
        }
        $toolbarButtons['clear_log'] = [
            'href' => $this->generateUrl('admin_security_csp_clear_log'),
            'desc' => $this->trans('Clear log', [], 'Admin.Advparameters.Feature'),
            'icon' => 'delete',
            // json_encode builds a safe JS string literal;
            // the toolbar template HTML-escapes the onclick around it. "Clear log" clears every shop
            // resolved from the current scope, so an all-shops/group view warns that it wipes all of them.
            'js' => 'return confirm(' . json_encode(
                null === $shopId
                    ? $this->trans('Clear the Content Security Policy log for all shops? Allowed sources are kept.', [], 'Admin.Advparameters.Notification')
                    : $this->trans('Clear the Content Security Policy log? Allowed sources are kept.', [], 'Admin.Advparameters.Notification')
            ) . ');',
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Csp/index.html.twig',
            [
                'cspHasWeakeningSources' => $hasWeakeningSources,
                'cspIsEnforcing' => $isEnforcing,
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'layoutTitle' => $this->trans('Content Security Policy', [], 'Admin.Navigation.Menu'),
                'help_link' => $this->generateSidebarLink('AdminSecurityCsp'),
                'cspForm' => $cspForm->createView(),
                'cspLogGrid' => $this->presentGrid($cspLogGridFactory->getGrid($filters)),
            ]
        );
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('update', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function saveAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
    ): RedirectResponse {
        $this->assertFeatureEnabled();

        $form = $cspFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saveErrors = $cspFormHandler->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_security_csp_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('create', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function addAction(Request $request): Response
    {
        $this->assertFeatureEnabled();

        $form = $this->createForm(AddCspRuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->dispatchCommand(new AddCspRuleCommand(
                    (string) $data['directive'],
                    (string) $data['source'],
                    $this->getShopContext()->getShopConstraint()
                ));
                $this->addFlash('success', $this->trans('The source has been added to the allow-list.', [], 'Admin.Advparameters.Notification'));

                return $this->redirectToRoute('admin_security_csp_index');
            } catch (CspException $e) {
                $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
            }
        }

        return $this->render('@PrestaShop/Admin/Configure/AdvancedParameters/Csp/add.html.twig', [
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('Add allowed source', [], 'Admin.Advparameters.Feature'),
            'help_link' => $this->generateSidebarLink('AdminSecurityCsp'),
            'addCspRuleForm' => $form->createView(),
        ]);
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('delete', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function clearLogAction(): RedirectResponse
    {
        $this->assertFeatureEnabled();

        $this->dispatchCommand(new ClearCspLogCommand($this->getShopContext()->getShopConstraint()));
        $this->addFlash('success', $this->trans('The Content Security Policy log has been cleared. Allowed sources were kept.', [], 'Admin.Advparameters.Notification'));

        return $this->redirectToRoute('admin_security_csp_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('create', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function allowAction(int $cspLogId): RedirectResponse
    {
        $this->assertFeatureEnabled();

        try {
            $this->dispatchCommand(new AllowCspSourceCommand($cspLogId, $this->getShopContext()->getShopConstraint()));
            $this->addFlash('success', $this->trans('The source has been added to the allow-list.', [], 'Admin.Advparameters.Notification'));
        } catch (CspException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_csp_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('delete', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function revokeAction(int $cspRuleId): RedirectResponse
    {
        $this->assertFeatureEnabled();

        try {
            $this->dispatchCommand(new RevokeCspSourceCommand($cspRuleId, $this->getShopContext()->getShopConstraint()));
            $this->addFlash('success', $this->trans('The source has been removed from the allow-list.', [], 'Admin.Advparameters.Notification'));
        } catch (CspException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_csp_index');
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('delete', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function bulkRevokeAction(Request $request): RedirectResponse
    {
        $this->assertFeatureEnabled();

        // The POST field is "{gridId}_{bulkColumnId}" = 'csp_log_bulk_action[]' (see CspLogGridDefinitionFactory).
        $cspRuleIds = array_values(array_filter(array_map('intval', $request->request->all('csp_log_bulk_action'))));

        if ([] === $cspRuleIds) {
            // Only allowed rows carry a rule id, so an un-allowed (or empty) selection means nothing was done.
            $this->addFlash('warning', $this->trans('Select at least one allowed source to revoke.', [], 'Admin.Advparameters.Notification'));

            return $this->redirectToRoute('admin_security_csp_index');
        }

        try {
            $this->dispatchCommand(new BulkRevokeCspSourceCommand($cspRuleIds, $this->getShopContext()->getShopConstraint()));
            $this->addFlash('success', $this->trans('The selected sources have been removed from the allow-list.', [], 'Admin.Advparameters.Notification'));
        } catch (CspException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_csp_index');
    }

    private function assertFeatureEnabled(): void
    {
        if (!$this->getFeatureFlagStateChecker()->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)) {
            throw $this->createNotFoundException('The Content Security Policy feature is not enabled.');
        }
    }

    /**
     * @return array<class-string, string|array<int, string>>
     */
    private function getErrorMessages(Throwable $e): array
    {
        return [
            CspRuleNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            CspLogNotFoundException::class => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
            CannotAddCspRuleException::class => $this->trans('An error occurred while adding the source to the allow-list.', [], 'Admin.Advparameters.Notification'),
            CannotDeleteCspRuleException::class => $this->trans('An error occurred while removing the source from the allow-list.', [], 'Admin.Advparameters.Notification'),
            // Keyed by CspConstraintException code, so each code maps to its own message.
            CspConstraintException::class => [
                CspConstraintException::INVALID_DIRECTIVE => $this->trans('This is not a valid Content Security Policy directive.', [], 'Admin.Advparameters.Notification'),
                CspConstraintException::INVALID_SOURCE => $this->trans('This is not a valid Content Security Policy source.', [], 'Admin.Advparameters.Notification'),
                CspConstraintException::INVALID_ID => $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error'),
                CspConstraintException::DUPLICATE_RULE => $this->trans('This source is already allowed for this directive.', [], 'Admin.Advparameters.Notification'),
            ],
            CannotBulkRevokeCspRuleException::class => $this->trans('An error occurred while removing the selected sources.', [], 'Admin.Advparameters.Notification'),
        ];
    }
}
