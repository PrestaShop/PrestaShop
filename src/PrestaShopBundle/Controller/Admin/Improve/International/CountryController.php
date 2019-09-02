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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotAddAddressFormatException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateAddressFormatException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotAddCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotUpdateCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetAddressFormatData;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\AddressFormatData;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\EditableCountry;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Responsible for International > Locations > Countries data
 */
class CountryController extends FrameworkBundleAdminController
{
    /**
     * Show legacy countries listing page
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $indexLink = $this->getAdminLink('AdminCountries', []);

        return $this->redirect($indexLink);
    }

    /**
     * Show "Add new" form and handle form submit.
     *
     * @AdminSecurity(
     *     "is_granted(['create'], request.get('_legacy_controller'))",
     *     redirectRoute="admin_countries_index",
     *     message="You do not have permission to create this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request): Response
    {
        $countryFormBuilder = $this->get(
            'prestashop.core.form.identifiable_object.builder.country_form_builder'
        );
        $countryFormHandler = $this->get(
            'prestashop.core.form.identifiable_object.handler.country_form_handler'
        );

        $countryForm = $countryFormBuilder->getForm();
        $countryForm->handleRequest($request);

        try {
            /** @var AddressFormatData $addressFormat */
            $addressFormat = $this->getQueryBus()->handle(new GetAddressFormatData());

            $handlerResult = $countryFormHandler->handle($countryForm);

            if ($handlerResult->isSubmitted() && $handlerResult->isValid()) {
                $this->addFlash('success', $this->trans('Successful creation.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_countries_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_countries_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/Country/add.html.twig', [
            'enableSidebar' => true,
            'countryForm' => $countryForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'addressFormat' => $addressFormat->getAddressFormat(),
            'encodingAddressFormat' => urlencode($addressFormat->getAddressFormat()),
            'defaultFormat' => urlencode($addressFormat->getDefaultFormat()),
            'availableFields' => $addressFormat->getAvailableFields(),
        ]);
    }

    /**
     * Handles edit form rendering and submission
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_countries_index"
     * )
     *
     * @param int $countryId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(int $countryId, Request $request): Response
    {
        try {
            /** @var EditableCountry $editableCountry */
            $editableCountry = $this->getQueryBus()->handle(new GetCountryForEditing((int) $countryId));
        } catch (CountryException $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_countries_index');
        }

        try {
            $getAddressFormatDataQuery = new GetAddressFormatData();
            $getAddressFormatDataQuery->setCountryId($countryId);

            /** @var AddressFormatData $addressFormat */
            $addressFormat = $this->getQueryBus()->handle($getAddressFormatDataQuery);

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
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('admin_countries_index');
            }
        } catch (Exception $e) {
            $this->addFlash('error', $this->getErrorMessageForException($e, $this->getErrorMessages()));

            return $this->redirectToRoute('admin_countries_index');
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Locations/Country/edit.html.twig', [
            'enableSidebar' => true,
            'countryForm' => $countryForm->createView(),
            'help_link' => $this->generateSidebarLink($request->attributes->get('_legacy_controller')),
            'addressFormat' => urlencode($addressFormat->getAddressFormat()),
            'defaultFormat' => urlencode($addressFormat->getDefaultFormat()),
            'availableFields' => $addressFormat->getAvailableFields(),
            'countryName' => $editableCountry->getLocalisedNames()[$this->getContextLangId()],
        ]);
    }

    /**
     * @return array
     */
    private function getErrorMessages(): array
    {
        return [
            CountryNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotUpdateCountryException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CannotAddCountryException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CannotUpdateAddressFormatException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CannotAddAddressFormatException::class => $this->trans(
                'An error occurred while attempting to save.',
                'Admin.Notifications.Error'
            ),
            CountryConstraintException::class => [
                CountryConstraintException::INVALID_ID => $this->trans(
                    'The object cannot be loaded (the identifier is missing or invalid)',
                    'Admin.Notifications.Error'
                ),
                CountryConstraintException::INVALID_ZIP_CODE => $this->trans(
                    'The %s field is not valid',
                    'Admin.Notifications.Error',
                    [
                        sprintf('"%s"', $this->trans('Exchange rate', 'Admin.International.Feature')),
                    ]
                ),
                CountryConstraintException::class => $this->trans(
                    'An error occurred when attempting to update the required fields.',
                    'Admin.Notifications.Error'
                ),
            ],
        ];
    }
}
