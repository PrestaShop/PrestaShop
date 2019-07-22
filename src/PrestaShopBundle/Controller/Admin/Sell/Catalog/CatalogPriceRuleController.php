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

namespace PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\UpdateCatalogPriceRuleException;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Query\GetCatalogPriceRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\QueryResult\EditableCatalogPriceRule;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for Sell > Catalog > Discounts > Catalog Price Rules page
 */
class CatalogPriceRuleController extends FrameworkBundleAdminController
{
    /**
     * Show & process catalogPriceRule creation.
     *
     * @AdminSecurity("is_granted(['create'], request.get('_legacy_controller'))")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        try {
            $catalogPriceRuleForm = $this->getFormBuilder()->getForm();
            $catalogPriceRuleForm->handleRequest($request);

            $result = $this->getFormHandler()->handle($catalogPriceRuleForm);

            if (null !== $result->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_catalog_price_rules_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CatalogPriceRule/create.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'catalogPriceRuleForm' => $catalogPriceRuleForm->createView(),
        ]);
    }

    /**
     * Show & process catalog price rule editing.
     *
     * @AdminSecurity("is_granted(['update'], request.get('_legacy_controller'))")
     *
     * @param int $catalogPriceRuleId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request, int $catalogPriceRuleId): Response
    {
        $catalogPriceRuleId = (int) $catalogPriceRuleId;

        try {
            /** @var EditableCatalogPriceRule $editableCatalogPriceRule */
            $editableCatalogPriceRule = $this->getQueryBus()->handle(new GetCatalogPriceRuleForEditing($catalogPriceRuleId));

            $catalogPriceRuleForm = $this->getFormBuilder()->getFormFor($catalogPriceRuleId);
            $catalogPriceRuleForm->handleRequest($request);

            $result = $this->getFormHandler()->handleFor($catalogPriceRuleId, $catalogPriceRuleForm);
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_catalog_price_rules_index');
        }

        if ($result->isSubmitted() && $result->isValid()) {
            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

            return $this->redirectToRoute('admin_catalog_price_rules_index');
        }

        return $this->render('@PrestaShop/Admin/Sell/Catalog/CatalogPriceRule/edit.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'catalogPriceRuleForm' => $catalogPriceRuleForm->createView(),
            'catalogPriceRuleName' => $editableCatalogPriceRule->getName(),
        ]);
    }

    /**
     * Provides translated error messages for exceptions
     *
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            UpdateCatalogPriceRuleException::class => $this->trans(
                'An error occurred while updating an object.',
                'Admin.Notifications.Error'
            ),
            CatalogPriceRuleNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
        ];
    }

    /**
     * @return FormHandlerInterface
     */
    private function getFormHandler(): FormHandlerInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.handler.catalog_price_rule_form_handler');
    }

    /**
     * @return FormBuilderInterface
     */
    private function getFormBuilder(): FormBuilderInterface
    {
        return $this->get('prestashop.core.form.identifiable_object.builder.catalog_price_rule_form_builder');
    }
}
