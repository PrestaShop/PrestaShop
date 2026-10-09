<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Adapter\Csp\CspPolicyProvider;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Csp\CspSurfaceResolver;
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
use PrestaShop\PrestaShop\Core\Search\Filters\CspRuleFilters;
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
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Throwable;

/** The "Advanced parameters > Security > Content Security Policy" page: a native page gated behind the 'csp' feature flag. */
class CspController extends PrestaShopAdminController
{
    #[AdminSecurity("is_granted('read', 'AdminSecurityCsp')")]
    public function indexAction(
        Request $request,
        CspLogFilters $filters,
        CspRuleFilters $ruleFilters,
        CspFeatureChecker $featureChecker,
        CspRuleRepository $cspRuleRepository,
        CspLogRepository $cspLogRepository,
        ShopListResolverInterface $shopListResolver,
        #[Autowire(service: 'prestashop.admin.csp.settings.form_handler')]
        FormHandlerInterface $cspFormHandler,
        #[Autowire(service: 'prestashop.admin.csp.admin_settings.form_handler')]
        FormHandlerInterface $adminCspFormHandler,
        #[Autowire(service: 'prestashop.core.grid.factory.csp_log')]
        GridFactoryInterface $cspLogGridFactory,
        #[Autowire(service: 'prestashop.core.grid.factory.csp_rule')]
        GridFactoryInterface $cspRuleGridFactory,
    ): Response {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);
        $isAdmin = CspContext::ADMIN === $context;

        // The storefront and back office each have their own settings form (per-shop vs global).
        $cspForm = ($isAdmin ? $adminCspFormHandler : $cspFormHandler)->getForm();

        // The shops the current scope covers: the back office is one global surface (shop id 0), the
        // storefront is the selected shop or every shop in an all-shops/group scope.
        if ($isAdmin) {
            $scopedShopIds = [0];
        } else {
            $shopConstraint = $this->getShopContext()->getShopConstraint();
            $shopId = $shopConstraint->getShopId()?->getValue();
            $scopedShopIds = null !== $shopId ? [$shopId] : $shopListResolver->resolveShopIds($shopConstraint);
        }

        // Which of those shops have CSP enabled (and of those, enforcing). The toggles are cached
        // configuration reads, not per-shop queries; the counts below are one grouped query each.
        $isEnforcing = false;
        $enabledShopIds = [];
        foreach ($scopedShopIds as $scopedShopId) {
            $enabled = $isAdmin
                ? $featureChecker->isEnabledForContext(CspContext::ADMIN, 0)
                : $featureChecker->isEnabledForShop($scopedShopId);
            if (!$enabled) {
                continue;
            }
            $enabledShopIds[] = $scopedShopId;
            $reportOnly = $isAdmin
                ? $featureChecker->isReportOnlyForContext(CspContext::ADMIN, 0)
                : $featureChecker->isReportOnlyForShop($scopedShopId);
            if (!$reportOnly) {
                $isEnforcing = true;
            }
        }

        // Pre-enforcement nudge: reported sources not yet on the allow-list, i.e. what enforcing would block.
        $unreviewedCount = $cspLogRepository->countUnreviewedByShops($context, $scopedShopIds);
        // Warn when the scope's allow-list has weakening sources: under enforcement they give little XSS
        // protection. Only enabled shops matter (a disabled shop sends no policy).
        $hasWeakeningSources = $cspRuleRepository->hasWeakeningRuleForAnyShop($context, $enabledShopIds);
        // At the row cap, new sources are no longer recorded; tell the merchant to clear the log.
        $cspLogFull = $cspLogRepository->anyShopAtCap($context, $scopedShopIds, CspViolationRecorder::DEFAULT_ROW_CAP);

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
            'href' => '#',
            'desc' => $this->trans('Clear log', [], 'Admin.Advparameters.Feature'),
            'icon' => 'delete',
            // Clearing the log is a CSRF-protected POST; the toolbar can only render a link, so confirm and
            // then submit the hidden form in the template. json_encode builds a safe JS string literal.
            'js' => 'if(confirm(' . json_encode($this->clearLogConfirmMessage($isAdmin)) . ')){document.getElementById(\'csp-clear-log-form\').submit();}return false;',
        ];

        return $this->render(
            '@PrestaShop/Admin/Configure/AdvancedParameters/Csp/index.html.twig',
            [
                'cspHasWeakeningSources' => $hasWeakeningSources,
                'cspIsEnforcing' => $isEnforcing,
                'cspShowEnforceNudge' => $showEnforceNudge,
                'cspLogFull' => $cspLogFull,
                'cspUnreviewedCount' => $unreviewedCount,
                'cspSelectedContext' => $context->value,
                'cspContextParams' => $contextParams,
                // The back office pre-allows PrestaShop's own domains; show them read-only so the merchant
                // sees what is permitted by default (these never produce a violation). Admin surface only.
                'cspBuiltInSources' => $isAdmin ? CspPolicyProvider::getAdminFirstPartySources() : [],
                'enableSidebar' => true,
                'layoutHeaderToolbarBtn' => $toolbarButtons,
                'layoutTitle' => $this->trans('Content Security Policy', [], 'Admin.Navigation.Menu'),
                'help_link' => $this->generateSidebarLink('AdminSecurityCsp'),
                'cspForm' => $cspForm->createView(),
                'cspLogGrid' => $this->presentGrid($cspLogGridFactory->getGrid($filters)),
                'cspRuleGrid' => $this->presentGrid($cspRuleGridFactory->getGrid($ruleFilters)),
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
    public function clearLogAction(Request $request, CsrfTokenManagerInterface $csrfTokenManager): RedirectResponse
    {
        $this->assertFeatureEnabled();

        $context = $this->resolveContext($request);

        // Clearing the log is a state change, so it is POST and CSRF-protected (matching Allow/Revoke).
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('clear-csp-log', (string) $request->request->get('_token')))) {
            $this->addFlash('error', $this->trans('Invalid security token. Please try again.', [], 'Admin.Notifications.Error'));

            return $this->redirectToRoute('admin_security_csp_index', $this->contextRedirectParams($context));
        }

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

        // The POST field is "{gridId}_{bulkColumnId}" = 'csp_rule_bulk_action[]' (see CspRuleGridDefinitionFactory).
        $cspRuleIds = array_values(array_filter(array_map('intval', $request->request->all('csp_rule_bulk_action'))));

        if ([] === $cspRuleIds) {
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

    /**
     * The page shows one surface at a time, selected by ?context (default the storefront). The bulk-revoke
     * POST has no query string, so it carries the surface as a route default instead (request attributes).
     */
    private function resolveContext(Request $request): CspContext
    {
        return CspSurfaceResolver::fromRequest($request);
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
