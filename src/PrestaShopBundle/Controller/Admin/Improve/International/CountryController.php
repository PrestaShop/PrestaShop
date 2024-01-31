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

declare(strict_types=1);

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\DeleteCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotEditCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\DeleteCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;
use PrestaShop\PrestaShop\Core\Search\Filters\CountryFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CountryController is responsible for handling "Improve > International > Locations > Countries"
 */
class CountryController extends FrameworkBundleAdminController
{
    /**
     * Show countries listing page
     *
     * @param Request $request
     * @param CountryFilters $filters
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('read', request.get('_legacy_controller'))")]
    public function indexAction(Request $request, CountryFilters $filters): Response
    {
        $countryGridFactory = $this->get('prestashop.core.grid.factory.country');
        $countryGrid = $countryGridFactory->getGrid($filters);

        return $this->render('@PrestaShop/Admin/Improve/International/Country/index.html.twig', [
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'countryGrid' => $this->presentGrid($countryGrid),
            'enableSidebar' => true,
            'layoutHeaderToolbarBtn' => $this->getCountryToolbarButtons(),
        ]);
    }

    /**
     * Show "Add new" country form and handles its submit.
     *
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('create', request.get('_legacy_controller'))", redirectRoute: 'admin_countries_index', message: 'You need permission to create new country.')]
    public function createAction(Request $request): Response
    {
        $countryFormBuilder = $this->get('prestashop.core.form.identifiable_object.builder.country_form_builder');
        $countryFormHandler = $this->get('prestashop.core.form.identifiable_object.handler.country_form_handler');

        $countryForm = $countryFormBuilder->getForm();
        $countryForm->handleRequest($request);

        try {
            $handleResult = $countryFormHandler->handle($countryForm);

            if (null !== $handleResult->getIdentifiableObjectId()) {
                $this->addFlash('success', $this->trans('Successful creation', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_countries_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Country/create.html.twig', [
            'countryForm' => $countryForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'enableSidebar' => true,
            'layoutTitle' => $this->trans('New country', 'Admin.Navigation.Menu'),
        ]);
    }

    /**
     * Displays country edit form and handles its submit.
     *
     * @param int $countryId
     * @param Request $request
     *
     * @return Response
     */
    #[AdminSecurity("is_granted('update', request.get('_legacy_controller'))", redirectRoute: 'admin_countries_index', message: 'You need permission to edit this.')]
    public function editAction(int $countryId, Request $request): Response
    {
        try {
            /** @var CountryForEditing $editableCountry */
            $editableCountry = $this->getQueryBus()->handle(new GetCountryForEditing($countryId));

            $countryFormBuilder = $this->get(
                'prestashop.core.form.identifiable_object.builder.country_form_builder'
            );
            $countryFormHandler = $this->get(
                'prestashop.core.form.identifiable_object.handler.country_form_handler'
            );

            $countryForm = $countryFormBuilder->getFormFor($countryId);
            $countryForm->handleRequest($request);
            $result = $countryFormHandler->handleFor($countryId, $countryForm);

            if ($result->isSubmitted() && $result->isValid()) {
                $this->addFlash('success', $this->trans('Successful update', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_countries_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_countries_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Country/edit.html.twig', [
            'enableSidebar' => true,
            'countryForm' => $countryForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'countryName' => $editableCountry->getLocalizedNames()[$this->getContextLangId()],
        ]);
    }

    /**
     * Deletes country.
     *
     * @param int $countryId
     *
     * @return RedirectResponse
     */
    #[DemoRestricted(redirectRoute: 'admin_countries_index')]
    #[AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", redirectRoute: 'admin_countries_index', message: 'You need permission to delete this.')]
    public function deleteAction(int $countryId): RedirectResponse
    {
        try {
            $this->getCommandBus()->handle(new DeleteCountryCommand($countryId));
            $this->addFlash('success', $this->trans('Successful deletion.', 'Admin.Notifications.Success'));
        } catch (CountryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages($e)));

            return $this->redirectToRoute('admin_countries_index');
        }

        return $this->redirectToRoute('admin_countries_index');
    }

    /**
     * @return array
     */
    protected function getCountryToolbarButtons(): array
    {
        return [
            'add' => [
                'href' => $this->generateUrl('admin_countries_create'),
                'desc' => $this->trans('Add new country', 'Admin.International.Feature'),
                'icon' => 'add_circle_outline',
            ],
        ];
    }

    /**
     * Returns country error messages mapping.
     *
     * @param Exception $e
     *
     * @return array
     */
    protected function getErrorMessages(Exception $e): array
    {
        return [
            CountryNotFoundException::class => $this->trans(
                'This country does not exist.',
                'Admin.International.Feature'
            ),
            CannotEditCountryException::class => [
                CannotEditCountryException::FAILED_TO_UPDATE_COUNTRY => $this->trans(
                    'Failed to update country.',
                    'Admin.International.Feature'
                ),
                CannotEditCountryException::UNKNOWN_EXCEPTION => $this->trans(
                    'Failed to update country. An unexpected error occurred.',
                    'Admin.International.Feature'
                ),
            ],
            CountryConstraintException::class => $this->trans(
                'Country contains invalid field values.',
                'Admin.International.Feature'
            ),
            DeleteCountryException::class => $this->trans(
                'Country cannot be deleted.',
                'Admin.International.Feature'
            ),
        ];
    }
}
