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
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotToggleCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Search\Filters\CurrencyFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CurrencyController is responsible for handling "Improve -> International -> Localization -> Currencies" page.
 */
class CurrencyController extends FrameworkBundleAdminController
{
    /**
     * Show currency page.
     *
     * @Template("@PrestaShop/Admin/Improve/International/Currency/currency.html.twig")
     *
     * @AdminSecurity("is_granted('read', request.get('_legacy_controller'))")
     *
     * @param CurrencyFilters $filters
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(CurrencyFilters $filters, Request $request)
    {
        $currencyGridFactory = $this->get('prestashop.core.grid.factory.currency');
        $currencyGrid = $currencyGridFactory->getGrid($filters);
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'currencyGrid' => $gridPresenter->present($currencyGrid),
        ];
    }

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

        return $this->redirectToRoute('admin_currency_index', ['filters' => $filters]);
    }

    public function editAction()
    {
//        todo: implement
    }

    public function deleteAction($currencyId)
    {
        $commandBus = $this->get('prestashop.core.command_bus');

        try {
            $currencyId = new CurrencyId($currencyId);
            $commandBus->handle(new DeleteCurrencyCommand($currencyId));
        } catch (CurrencyException $exception) {
            $error = $this->handleException($exception);
            $this->flashErrors([$error]);

            return $this->redirectToRoute('admin_currency_index');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful deletion.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_currency_index');
    }

    public function toggleStatusAction($currencyId)
    {
        $commandBus = $this->get('prestashop.core.command_bus');

        try {
            $currencyId = new CurrencyId($currencyId);
            $commandBus->handle(new ToggleCurrencyStatusCommand($currencyId));
        } catch (CurrencyException $exception) {
            $error = $this->handleException($exception);
            $this->flashErrors([$error]);

            return $this->redirectToRoute('admin_currency_index');
        }

        $this->addFlash(
            'success',
            $this->trans('The status has been successfully updated.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_currency_index');
    }

    /**
     * Handles exceptions that might appear in all actions related to currency controller.
     *
     * @param CurrencyException $exception
     *
     * @return array
     */
    private function handleException(CurrencyException $exception)
    {
        if (0 !== $exception->getCode()) {
            return $this->getExceptionByErrorCodeType($exception);
        }

        return $this->getErrorByExceptionType($exception);
    }

    /**
     * Gets errors by exception type.
     *
     * @param CurrencyException $exception
     *
     * @return array
     */
    private function getErrorByExceptionType(CurrencyException $exception)
    {
        $exceptionTypeDictionary = [
            CurrencyNotFoundException::class => [
                'key' => 'The object cannot be loaded (or found)',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ],
            CannotToggleCurrencyException::class => [
                'key' => 'An error occurred while updating the status.',
                'parameters' => [],
                'domain' => 'Admin.Notifications.Error',
            ]
        ];

        $exceptionType = get_class($exception);

        if (isset($exceptionTypeDictionary[$exceptionType])) {
            return $exceptionTypeDictionary[$exceptionType];
        }

        return [
            'key' => 'Unexpected error occurred.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }

    /**
     * Gets error by error code.
     *
     * @param CurrencyException $exception
     *
     * @return array
     */
    private function getExceptionByErrorCodeType(CurrencyException $exception)
    {
        $exceptionTypeDictionary = [
            CannotToggleCurrencyException::class => [
                CannotToggleCurrencyException::CANNOT_DISABLE_DEFAULT_CURRENCY => [
                    'key' => 'You cannot disable the default currency',
                    'parameters' => [],
                    'domain' => 'Admin.International.Notification',
                ]
            ],
            CannotDeleteCurrencyException::class => [
                CannotDeleteCurrencyException::CANNOT_DELETE_DEFAULT_CURRENCY => [
                    'key' => 'You cannot delete the default currency',
                    'parameters' => [],
                    'domain' => 'Admin.International.Notification',
                ]
            ]
        ];

        $exceptionType = get_class($exception);
        $exceptionCode = $exception->getCode();

        if (isset($exceptionTypeDictionary[$exceptionType][$exceptionCode])) {
            return $exceptionTypeDictionary[$exceptionType][$exceptionCode];
        }

        return [
            'key' => 'Unexpected error occurred.',
            'parameters' => [],
            'domain' => 'Admin.Notifications.Error',
        ];
    }
}
