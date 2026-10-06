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
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\Form\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CspLogFilters;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
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
        Request $request,
        CspLogFilters $filters,
        CspFeatureChecker $featureChecker,
        CspRuleRepository $cspRuleRepository,
        CspLogRepository $cspLogRepository,
        ShopListResolverInterface $shopListResolver,
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
        #[Autowire(service: 'prestashop.admin.csp.admin_settings.form_handler')]
        FormHandlerInterface $adminCspFormHandler,
        #[Autowire(service: 'prestashop.admin.security_headers.form_handler')]
        FormHandlerInterface $securityHeadersFormHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.csp_log')]
        GridFactoryInterface $cspLogGridFactory,
    ): Response {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        $isAdmin = CspContext::ADMIN === $context;

        // The storefront and back office each have their own settings form (per-shop vs global).
        $cspForm = ($isAdmin ? $adminCspFormHandler : $cspFormHandler)->getForm();

        // Warn when the selected surface's allow-list has weakening sources: under enforcement they give
        // little XSS protection. In a storefront all-shops/group scope the warning covers the whole scope.
        $isEnforcing = false;
        $hasWeakeningSources = false;
        // Pre-enforcement nudge: reported sources not yet on the allow-list, i.e. what enforcing would block.
        $unreviewedCount = 0;
        if ($isAdmin) {
            $unreviewedCount = $cspLogRepository->countUnreviewedByShop(CspContext::ADMIN, 0);
            if ($featureChecker->isEnabledForContext(CspContext::ADMIN, 0)) {
                $isEnforcing = !$featureChecker->isReportOnlyForContext(CspContext::ADMIN, 0);
                $hasWeakeningSources = $cspRuleRepository->countWeakeningRulesByShop(CspContext::ADMIN, 0) > 0;
            }
        } else {
            $shopConstraint = $this->getShopContext()->getShopConstraint();
            $shopId = $shopConstraint->getShopId()?->getValue();
            $scopedShopIds = null !== $shopId ? [$shopId] : $shopListResolver->resolveShopIds($shopConstraint);
            foreach ($scopedShopIds as $scopedShopId) {
                $unreviewedCount += $cspLogRepository->countUnreviewedByShop(CspContext::FRONT, $scopedShopId);
                if (!$featureChecker->isEnabledForShop($scopedShopId)) {
                    continue;
                }
                if (!$featureChecker->isReportOnlyForShop($scopedShopId)) {
                    $isEnforcing = true;
                }
                if ($cspRuleRepository->countWeakeningRulesByShop(CspContext::FRONT, $scopedShopId) > 0) {
                    $hasWeakeningSources = true;
                }
            }
        }

        // Only nudge while still report-only (not yet enforcing); once enforced the weakening banner applies.
        $showEnforceNudge = !$isEnforcing && $unreviewedCount > 0;

        $contextParams = $this->contextRedirectParams($context);

        // A rule can only be added for a single shop; the back office is one global surface, so "Add" is
        // always available there, and hidden only in a storefront all-shops/group scope.
        $canAdd = $isAdmin || null !== $this->getShopContext()->getShopConstraint()->getShopId();
        $toolbarButtons = [];
        if ($canAdd) {
            $toolbarButtons['add'] = [
                'href' => $this->generateUrl('admin_security_csp_add', $contextParams),
                'desc' => $this->trans('Add allowed source', [], 'Admin.Advparameters.Feature'),
                'icon' => 'add_circle_outline',
            ];
        }
        $toolbarButtons['clear_log'] = [
            'href' => $this->generateUrl('admin_security_csp_clear_log', $contextParams),
            'desc' => $this->trans('Clear log', [], 'Admin.Advparameters.Feature'),
            'icon' => 'delete',
            // json_encode builds a safe JS string literal; the toolbar template HTML-escapes the onclick.
            'js' => 'return confirm(' . json_encode($this->clearLogConfirmMessage($isAdmin)) . ');',
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Csp/index.html.twig',
            [
                'cspHasWeakeningSources' => $hasWeakeningSources,
                'cspIsEnforcing' => $isEnforcing,
                'cspShowEnforceNudge' => $showEnforceNudge,
                'cspUnreviewedCount' => $unreviewedCount,
                'cspSelectedContext' => $context->value,
                'cspContextParams' => $contextParams,
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'layoutTitle' => $this->trans('Security headers', [], 'Admin.Navigation.Menu'),
                'help_link' => $this->generateSidebarLink('AdminSecurityCsp'),
                'securityHeadersForm' => $securityHeadersFormHandler->getForm()->createView(),
                'cspForm' => $cspForm->createView(),
                'cspLogGrid' => $this->presentGrid($cspLogGridFactory->getGrid($filters)),
            ]
        );
    }

    private function clearLogConfirmMessage(bool $isAdmin): string
    {
        if ($isAdmin) {
            return $this->trans('Clear the back-office Content Security Policy log? Allowed sources are kept.', [], 'Admin.Advparameters.Notification');
        }

        // "Clear log" clears every shop resolved from the current scope, so an all-shops/group view warns.
        return null === $this->getShopContext()->getShopConstraint()->getShopId()
            ? $this->trans('Clear the Content Security Policy log for all shops? Allowed sources are kept.', [], 'Admin.Advparameters.Notification')
            : $this->trans('Clear the Content Security Policy log? Allowed sources are kept.', [], 'Admin.Advparameters.Notification');
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('update', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function saveAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
        #[Autowire(service: 'prestashop.admin.csp.admin_settings.form_handler')]
        FormHandlerInterface $adminCspFormHandler,
    ): RedirectResponse {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        $formHandler = CspContext::ADMIN === $context ? $adminCspFormHandler : $cspFormHandler;

        $form = $formHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saveErrors = $formHandler->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('create', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function addAction(Request $request): Response
    {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        $form = $this->createForm(AddCspRuleType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $this->dispatchCommand(new AddCspRuleCommand(
                    (string) $data['directive'],
                    (string) $data['source'],
                    $this->getShopContext()->getShopConstraint(),
                    $context
                ));
                $this->addFlash('success', $this->trans('The source has been added to the allow-list.', [], 'Admin.Advparameters.Notification'));

                return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
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
    public function clearLogAction(Request $request): RedirectResponse
    {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        $this->dispatchCommand(new ClearCspLogCommand($this->getShopContext()->getShopConstraint(), $context));
        $this->addFlash('success', $this->trans('The Content Security Policy log has been cleared. Allowed sources were kept.', [], 'Admin.Advparameters.Notification'));

        return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('create', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function allowAction(int $cspLogId, Request $request): RedirectResponse
    {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        try {
            $this->dispatchCommand(new AllowCspSourceCommand($cspLogId, $this->getShopContext()->getShopConstraint(), $context));
            $this->addFlash('success', $this->trans('The source has been added to the allow-list.', [], 'Admin.Advparameters.Notification'));
        } catch (CspException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('delete', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function revokeAction(int $cspRuleId, Request $request): RedirectResponse
    {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        try {
            $this->dispatchCommand(new RevokeCspSourceCommand($cspRuleId, $this->getShopContext()->getShopConstraint(), $context));
            $this->addFlash('success', $this->trans('The source has been removed from the allow-list.', [], 'Admin.Advparameters.Notification'));
        } catch (CspException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('delete', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function bulkRevokeAction(Request $request): RedirectResponse
    {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);

        // The POST field is "{gridId}_{bulkColumnId}" = 'csp_log_bulk_action[]' (see CspLogGridDefinitionFactory).
        $cspRuleIds = array_values(array_filter(array_map('intval', $request->request->all('csp_log_bulk_action'))));

        if ([] === $cspRuleIds) {
            // Only allowed rows carry a rule id, so an un-allowed (or empty) selection means nothing was done.
            $this->addFlash('warning', $this->trans('Select at least one allowed source to revoke.', [], 'Admin.Advparameters.Notification'));

            return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
        }

        try {
            $this->dispatchCommand(new BulkRevokeCspSourceCommand($cspRuleIds, $this->getShopContext()->getShopConstraint(), $context));
            $this->addFlash('success', $this->trans('The selected sources have been removed from the allow-list.', [], 'Admin.Advparameters.Notification'));
        } catch (CspException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
    }

    #[DemoRestricted(redirectRoute: 'admin_security_csp_index')]
    #[AdminSecurity("is_granted('update', 'AdminSecurityCsp')", redirectRoute: 'admin_security_csp_index')]
    public function saveSecurityHeadersAction(
        Request $request,
        #[Autowire(service: 'prestashop.admin.security_headers.form_handler')]
        FormHandlerInterface $securityHeadersFormHandler,
    ): RedirectResponse {
        $this->assertFeatureEnabled();

        $form = $securityHeadersFormHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $saveErrors = $securityHeadersFormHandler->save($form->getData());

            if (0 === count($saveErrors)) {
                $this->addFlash('success', $this->trans('Update successful', [], 'Admin.Notifications.Success'));
            } else {
                $this->addFlashErrors($saveErrors);
            }
        }

        return $this->redirectToRoute('admin_security_csp_index');
    }

    /** The page shows one surface at a time, selected by ?context (default the storefront). */
    private function resolveContext(Request $request): CspContext
    {
        return 'admin' === $request->query->get('context') ? CspContext::ADMIN : CspContext::FRONT;
    }

    /** @return array<string, string> the query params that keep the current surface on a redirect back to the page */
    private function contextRedirectParams(CspContext $context): array
    {
        return CspContext::ADMIN === $context ? ['context' => 'admin'] : [];
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
