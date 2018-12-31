<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Domain\Currency\Command\DeleteCurrencyCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\ToggleCurrencyStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Command\RefreshExchangeRatesCommand;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotRefreshExchangeRatesException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotToggleCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\ScheduleExchangeRatesUpdateException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder\FormBuilderInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler\FormHandlerInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\CurrencyFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use PrestaShopBundle\Security\Voter\PageVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CurrencyController is responsible for handling "Improve -> International -> Localization -> Currencies" page.
 */
class CurrencyController extends FrameworkBundleAdminController
{
    /**
     * Show currency page.
     *
     * @AdminSecurity(
     *     "is_granted('read', request.get('_legacy_controller'))",
     *     redirectRoute="admin_currencies_index"
     * )
     *
     * @param CurrencyFilters $filters
     *
     * @return Response
     */
    public function indexAction(CurrencyFilters $filters)
    {
        $currencyGridFactory = $this->get('prestashop.core.grid.factory.currency');
        $currencyGrid = $currencyGridFactory->getGrid($filters);
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        $settingsForm = $this->getSettingsFormHandler()->getForm();

        return $this->render('@PrestaShop/Admin/Improve/International/Currency/index.html.twig', [
            'currencyGrid' => $gridPresenter->present($currencyGrid),
            'currencySettingsForm' => $settingsForm->createView(),
        ]);
    }

    /**
     * Provides filters functionality.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function searchAction(Request $request)
    {
        $definitionFactory = $this->get('prestashop.core.grid.definition.factory.currency');
        $definitionFactory = $definitionFactory->getDefinition();

        $gridFilterFormFactory = $this->get('prestashop.core.grid.filter.form_factory');
        $searchParametersForm = $gridFilterFormFactory->create($definitionFactory);
        $searchParametersForm->handleRequest($request);

        $filters = [];
        if ($searchParametersForm->isSubmitted()) {
            $filters = $searchParametersForm->getData();
        }

        return $this->redirectToRoute('admin_currencies_index', ['filters' => $filters]);
    }

    /**
     * Displays and handles currency form.
     *
     * @AdminSecurity(
     *     "is_granted('create', request.get('_legacy_controller'))",
     *     redirectRoute="admin_currencies_index",
     *     message="You do not have permission to add this."
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $multiStoreFeature = $this->get('prestashop.adapter.multistore_feature');

        $currencyForm = $this->getCurrencyFormBuilder()->getForm();
        $currencyForm->handleRequest($request);

        try {
            if ($currencyForm->isSubmitted()) {
                $result = $this->getCurrencyFormHandler()->handle($currencyForm);
                if (null !== $result->getIdentifiableObjectId()) {
                    $this->addFlash(
                        'success',
                        $this->trans('Successful creation.', 'Admin.Notifications.Success')
                    );

                    return $this->redirectToRoute('admin_currencies_index');
                }
            }
        } catch (CurrencyException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Currency/create.html.twig', [
            'isShopFeatureEnabled' => $multiStoreFeature->isUsed(),
            'currencyForm' => $currencyForm->createView(),
        ]);
    }

    /**
     * Displays currency form.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_currencies_index",
     *     message="You do not have permission to edit this."
     * )
     *
     * @param int $currencyId
     * @param Request $request
     *
     * @return Response
     */
    public function editAction($currencyId, Request $request)
    {
        $multiStoreFeature = $this->get('prestashop.adapter.multistore_feature');
        $currencyForm = null;

        try {
            $currencyForm = $this->getCurrencyFormBuilder()->getFormFor($currencyId);
            $currencyForm->handleRequest($request);

            if ($currencyForm->isSubmitted()) {
                $result = $this->getCurrencyFormHandler()->handleFor($currencyId, $currencyForm);

                if (null !== $result->getIdentifiableObjectId()) {
                    $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                    return $this->redirectToRoute('admin_currencies_index');
                }
            }
        } catch (CurrencyException $exception) {
            $this->addFlash('error', $this->handleException($exception));
        }

        return $this->render('@PrestaShop/Admin/Improve/International/Currency/create.html.twig', [
            'isShopFeatureEnabled' => $multiStoreFeature->isUsed(),
            'currencyForm' => null !== $currencyForm ? $currencyForm->createView() : null,
        ]);
    }

    /**
     * Deletes currency.
     *
     * @AdminSecurity(
     *     "is_granted('delete', request.get('_legacy_controller'))",
     *     redirectRoute="admin_currencies_index",
     *     message="You do not have permission to delete this."
     * )
     * @DemoRestricted(redirectRoute="admin_currencies_index")
     *
     * @param int $currencyId
     *
     * @return RedirectResponse
     */
    public function deleteAction($currencyId)
    {
        try {
            $this->getCommandBus()->handle(new DeleteCurrencyCommand((int) $currencyId));
        } catch (CurrencyException $exception) {
            $this->addFlash('error', $this->handleException($exception));

            return $this->redirectToRoute('admin_currencies_index');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful deletion.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_currencies_index');
    }

    /**
     * Toggles status.
     *
     * @param int $currencyId
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_currencies_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_currencies_index")
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($currencyId)
    {
        try {
            $this->getCommandBus()->handle(new ToggleCurrencyStatusCommand((int) $currencyId));
        } catch (CurrencyException $exception) {
            $this->addFlash('error', $this->handleException($exception));

            return $this->redirectToRoute('admin_currencies_index');
        }

        $this->addFlash(
            'success',
            $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_currencies_index');
    }

    /**
     * Updates exchange rates.
     *
     * @AdminSecurity(
     *     "is_granted('update', request.get('_legacy_controller'))",
     *     redirectRoute="admin_currencies_index",
     *     message="You do not have permission to edit this."
     * )
     * @DemoRestricted(redirectRoute="admin_currencies_index")
     *
     * @return RedirectResponse
     */
    public function updateExchangeRatesAction()
    {
        try {
            $this->getCommandBus()->handle(new RefreshExchangeRatesCommand());

            $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));
        } catch (CannotRefreshExchangeRatesException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirectToRoute('admin_currencies_index');
    }

    /**
     * Handles ajax request which updates live exchange rates.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function updateLiveExchangeRatesAction(Request $request)
    {
        if ($this->isDemoModeEnabled()) {
            return $this->json([
                    'status' => false,
                    'message' => $this->getDemoModeErrorMessage(),
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $authLevel = $this->authorizationLevel($request->attributes->get('_legacy_controller'));

        if (!in_array($authLevel, [PageVoter::LEVEL_UPDATE, PageVoter::LEVEL_DELETE])) {
            return $this->json([
                    'status' => false,
                    'message' => $this->trans(
                        'You do not have permission to update this.',
                        'Admin.Notifications.Error'
                    ),
                ],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $settingsFormHandler = $this->getSettingsFormHandler();
        $settingsForm = $settingsFormHandler->getForm();

        $settingsForm->handleRequest($request);

        $response = [
            'status' => false,
            'message' => $this->trans('Unexpected error occurred.', 'Admin.Notifications.Error'),
        ];
        $statusCode = Response::HTTP_BAD_REQUEST;

        if ($settingsForm->isSubmitted()) {
            try {
                $settingsFormHandler->save($settingsForm->getData());
                $response = [
                    'status' => true,
                    'message' => $this->trans(
                        'The status has been successfully updated.',
                        'Admin.Notifications.Success'
                    ),
                ];
                $statusCode = Response::HTTP_OK;
            } catch (CurrencyException $exception) {
                $response['message'] = $this->handleException($exception);
            }
        }

        return $this->json($response, $statusCode);
    }

    /**
     * Gets form builder.
     *
     * @return FormBuilderInterface
     */
    private function getCurrencyFormBuilder()
    {
        return $this->get('prestashop.core.form.builder.currency_builder');
    }

    /**
     * @return FormHandlerInterface
     */
    private function getCurrencyFormHandler()
    {
        return $this->get('prestashop.core.form.identifiable_object.currency_form_handler');
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Form\FormHandlerInterface
     */
    private function getSettingsFormHandler()
    {
        return $this->get('prestashop.admin.currency_settings.form_handler');
    }

    /**
     * Gets error message by comparing exception code or by just comparing exception class.
     *
     * @param CurrencyException $exception
     *
     * @return string
     */
    private function handleException(CurrencyException $exception)
    {
        if (0 !== $exception->getCode()) {
            return $this->getErrorByExceptionCode($exception);
        }

        return $this->getErrorByExceptionType($exception);
    }

    /**
     * Gets error by exception type.
     *
     * @param CurrencyException $exception
     *
     * @return string
     */
    private function getErrorByExceptionType(CurrencyException $exception)
    {
        $exceptionTypeDictionary = [
            CurrencyNotFoundException::class => $this->trans(
                'The object cannot be loaded (or found)',
                'Admin.Notifications.Error'
            ),
            CannotToggleCurrencyException::class => $this->trans(
                'An error occurred while updating the status.',
                'Admin.Notifications.Error'
            ),
            CannotDeleteDefaultCurrencyException::class => $this->trans(
                'You cannot delete the default currency',
                'Admin.International.Notification'
            ),
            CannotDisableDefaultCurrencyException::class => $this->trans(
                'You cannot disable the default currency',
                'Admin.International.Notification'
            ),
        ];

        $exceptionType = get_class($exception);

        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return $this->getFallbackErrorMessage($exceptionType, $exception->getCode());
    }

    /**
     * Gets an error by exception class and its code.
     *
     * @param CurrencyException $exception
     *
     * @return string
     */
    private function getErrorByExceptionCode(CurrencyException $exception)
    {
        $exceptionDictionary = [
            CurrencyConstraintException::class => [
                CurrencyConstraintException::INVALID_ISO_CODE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Currency', 'Admin.Global')),
                        ]
                    ),
                CurrencyConstraintException::INVALID_EXCHANGE_RATE => $this->trans(
                        'The %s field is not valid',
                        'Admin.Notifications.Error',
                        [
                            sprintf(
                                '"%s"',
                                $this->trans('Exchange rate', 'Admin.International.Feature')),
                        ]
                    ),
                CurrencyConstraintException::CURRENCY_ALREADY_EXISTS => $this->trans(
                        'This currency already exists.',
                        'Admin.International.Notification'
                    ),
            ],
            ScheduleExchangeRatesUpdateException::class => [
                ScheduleExchangeRatesUpdateException::CRON_TASK_MANAGER_MODULE_NOT_INSTALLED => $this->trans(
                    'Please install the %module_name% module before using this feature.',
                    'Admin.International.Notification',
                    [
                        '%module_name%' => 'cronjobs',
                    ]
                ),
            ],
        ];

        $exceptionClass = get_class($exception);
        $exceptionCode = $exception->getCode();

        if (isset($exceptionDictionary[$exceptionClass][$exceptionCode])) {
            return $exceptionDictionary[$exceptionClass][$exceptionCode];
        }

        return $this->getFallbackErrorMessage($exceptionClass, $exceptionCode);
    }
}
