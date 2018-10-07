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
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDeleteDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotDisableDefaultCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CannotToggleCurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException;
use PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Search\Filters\CurrencyFilters;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use PrestaShopBundle\Security\Annotation\AdminSecurity;
use PrestaShopBundle\Security\Annotation\DemoRestricted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
     *
     * @return array
     */
    public function indexAction(CurrencyFilters $filters)
    {
        $currencyGridFactory = $this->get('prestashop.core.grid.factory.currency');
        $currencyGrid = $currencyGridFactory->getGrid($filters);
        $gridPresenter = $this->get('prestashop.core.grid.presenter.grid_presenter');

        return [
            'layoutHeaderToolbarBtn' => [
                'add' => [
                    'href' => $this->generateUrl('admin_currency_create'),
                    'desc' => $this->trans('Add new currency', 'Admin.International.Feature'),
                    'icon' => 'add_circle_outline',
                ],
            ],
            'currencyGrid' => $gridPresenter->present($currencyGrid),
        ];
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

        return $this->redirectToRoute('admin_currency_index', ['filters' => $filters]);
    }

    /**
     * Displays currency form.
     *
     * @AdminSecurity("is_granted('create', request.get('_legacy_controller'))", message="You do not have permission to add this.")
     *
     * @return RedirectResponse
     */
    public function createAction()
    {
        //todo: drop legacy and after having post add @DemoRestricted
        $legacyLink = $this->getAdminLink('AdminCurrencies', [
            'addcurrency' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Displays currency form.
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     *
     * @param int $currencyId
     *
     * @return RedirectResponse
     */
    public function editAction($currencyId)
    {
        //todo: drop legacy and after having post add @DemoRestricted
        $legacyLink = $this->getAdminLink('AdminCurrencies', [
            'id_currency' => $currencyId,
            'updatecurrency' => 1,
        ]);

        return $this->redirect($legacyLink);
    }

    /**
     * Deletes currency.
     *
     * @AdminSecurity("is_granted('delete', request.get('_legacy_controller'))", message="You do not have permission to delete this.")
     * @DemoRestricted(redirectRoute="admin_currency_index")
     *
     * @param int $currencyId
     *
     * @return RedirectResponse
     */
    public function deleteAction($currencyId)
    {
        $commandBus = $this->get('prestashop.core.command_bus');

        try {
            $currencyId = new CurrencyId($currencyId);
            $commandBus->handle(new DeleteCurrencyCommand($currencyId));
        } catch (CurrencyException $exception) {
            $error = $this->getErrorByExceptionType($exception);
            $this->flashErrors([$error]);

            return $this->redirectToRoute('admin_currency_index');
        }

        $this->addFlash(
            'success',
            $this->trans('Successful deletion.', 'Admin.Notifications.Success')
        );

        return $this->redirectToRoute('admin_currency_index');
    }

    /**
     * Toggles status.
     *
     * @param int $currencyId
     *
     * @AdminSecurity("is_granted('update', request.get('_legacy_controller'))", message="You do not have permission to edit this.")
     * @DemoRestricted(redirectRoute="admin_currency_index")
     *
     * @return RedirectResponse
     */
    public function toggleStatusAction($currencyId)
    {
        $commandBus = $this->get('prestashop.core.command_bus');

        try {
            $currencyId = new CurrencyId($currencyId);
            $commandBus->handle(new ToggleCurrencyStatusCommand($currencyId));
        } catch (CurrencyException $exception) {
            $error = $this->getErrorByExceptionType($exception);
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
            ],
            CannotDeleteDefaultCurrencyException::class => [
                'key' => 'You cannot delete the default currency',
                'parameters' => [],
                'domain' => 'Admin.International.Notification',
            ],
            CannotDisableDefaultCurrencyException::class => [
                'key' => 'You cannot disable the default currency',
                'parameters' => [],
                'domain' => 'Admin.International.Notification',
            ],
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
}
