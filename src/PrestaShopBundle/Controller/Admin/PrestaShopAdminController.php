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

namespace PrestaShopBundle\Controller\Admin;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Configuration\IniConfiguration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\CountryContext;
use PrestaShop\PrestaShop\Core\Context\CurrencyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\EnvironmentInterface;
use PrestaShop\PrestaShop\Core\Exception\MultiShopAccessDeniedException;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenterInterface;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Translation\TranslatorInterface;
use Throwable;

/**
 * Default controller for PrestaShop admin pages.
 */
class PrestaShopAdminController extends AbstractController
{
    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            IniConfiguration::class => IniConfiguration::class,
            ConfigurationInterface::class => ConfigurationInterface::class,
            CommandBusInterface::class => CommandBusInterface::class,
            HookDispatcherInterface::class => HookDispatcherInterface::class,
            TranslatorInterface::class => TranslatorInterface::class,
            GridPresenterInterface::class => GridPresenterInterface::class,
            ApiClientContext::class => ApiClientContext::class,
            CountryContext::class => CountryContext::class,
            CurrencyContext::class => CurrencyContext::class,
            EmployeeContext::class => EmployeeContext::class,
            LanguageContext::class => LanguageContext::class,
            LegacyControllerContext::class => LegacyControllerContext::class,
            ShopContext::class => ShopContext::class,
            Documentation::class => Documentation::class,
            ResponseBuilder::class => ResponseBuilder::class,
            PositionUpdateFactoryInterface::class => PositionUpdateFactoryInterface::class,
            GridPositionUpdaterInterface::class => GridPositionUpdaterInterface::class,
            FeatureFlagStateCheckerInterface::class => FeatureFlagStateCheckerInterface::class,
            EnvironmentInterface::class => EnvironmentInterface::class,
        ];
    }

    protected function getIniConfiguration(): IniConfiguration
    {
        return $this->container->get(IniConfiguration::class);
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return $this->container->get(ConfigurationInterface::class);
    }

    protected function getFeatureFlagStateChecker(): FeatureFlagStateCheckerInterface
    {
        return $this->container->get(FeatureFlagStateCheckerInterface::class);
    }

    protected function getApiClientContext(): ApiClientContext
    {
        return $this->container->get(ApiClientContext::class);
    }

    protected function getCountryContext(): CountryContext
    {
        return $this->container->get(CountryContext::class);
    }

    protected function getCurrencyContext(): CurrencyContext
    {
        return $this->container->get(CurrencyContext::class);
    }

    protected function getEmployeeContext(): EmployeeContext
    {
        return $this->container->get(EmployeeContext::class);
    }

    protected function getLanguageContext(): LanguageContext
    {
        return $this->container->get(LanguageContext::class);
    }

    protected function getLegacyControllerContext(): LegacyControllerContext
    {
        return $this->container->get(LegacyControllerContext::class);
    }

    protected function getShopContext(): ShopContext
    {
        return $this->container->get(ShopContext::class);
    }

    protected function getEnvironment(): EnvironmentInterface
    {
        return $this->container->get(EnvironmentInterface::class);
    }

    /**
     * Get commands bus to execute command.
     */
    protected function dispatchCommand(mixed $command): mixed
    {
        return $this->container->get(CommandBusInterface::class)->handle($command);
    }

    /**
     * Get commands bus to execute query.
     */
    protected function dispatchQuery(mixed $query): mixed
    {
        return $this->container->get(CommandBusInterface::class)->handle($query);
    }

    protected function presentGrid(GridInterface $grid): array
    {
        return $this->container->get(GridPresenterInterface::class)->present($grid);
    }

    protected function dispatchHookWithParameters(string $hookName, array $parameters = []): void
    {
        $this->container->get(HookDispatcherInterface::class)->dispatchWithParameters($hookName, $parameters);
    }

    protected function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
    {
        return $this->container->get(TranslatorInterface::class)->trans($id, $parameters, $domain, $locale);
    }

    protected function generateSidebarLink(string $section, ?string $title = null): string
    {
        if (empty($title)) {
            $title = $this->trans('Help', [], 'Admin.Global');
        }

        $iso = $this->getLanguageContext()->getIsoCode();
        $url = $this->generateUrl('admin_common_sidebar', [
            'url' => $this->container->get(Documentation::class)->generateLink($section, $iso),
            'title' => $title,
        ]);

        // this line is allow to revert a new behaviour introduce in sf 5.4 which break the result we used to have
        return strtr($url, ['%2F' => '%252F']);
    }

    /**
     * Get error by exception from given messages
     *
     * @param array<string, string|array<int, string>> $messages
     *
     * @return string
     */
    protected function getErrorMessageForException(Throwable $e, array $messages = []): string
    {
        if ($e instanceof ModuleErrorInterface) {
            return $e->getMessage();
        }

        $exceptionType = $e::class;
        $exceptionCode = $e->getCode();

        if (isset($messages[$exceptionType])) {
            $message = $messages[$exceptionType];

            if (is_string($message)) {
                return $message;
            }

            if (is_array($message) && isset($message[$exceptionCode])) {
                return $message[$exceptionCode];
            }
        }

        if ($e instanceof MultiShopAccessDeniedException && $e->getShopConstraint()) {
            if ($e->getShopConstraint()->forAllShops()) {
                return $this->trans(
                    'Authorization not allowed for all stores.',
                    [],
                    'Admin.Notifications.Error'
                );
            }

            if ($e->getShopConstraint()->getShopId()) {
                return $this->trans(
                    'Authorization not allowed for this store.',
                    [],
                    'Admin.Notifications.Error'
                );
            }

            if ($e->getShopConstraint()->getShopGroupId()) {
                return $this->trans(
                    'Authorization not allowed for this group of stores.',
                    [],
                    'Admin.Notifications.Error'
                );
            }

            return $this->trans(
                'Authorization not allowed for this store context.',
                [],
                'Admin.Notifications.Error'
            );
        }

        // Fallback error message
        $isDebug = $this->getParameter('kernel.debug');
        if ($isDebug && !empty($e->getMessage())) {
            return $this->trans(
                'An unexpected error occurred. [%type% code %code%]: %message%',
                [
                    '%type%' => $exceptionType,
                    '%code%' => $exceptionCode,
                    '%message%' => $e->getMessage(),
                ],
                'Admin.Notifications.Error',
            );
        }

        return $this->trans(
            'An unexpected error occurred. [%type% code %code%]',
            [
                '%type%' => $exceptionType,
                '%code%' => $exceptionCode,
            ],
            'Admin.Notifications.Error',
        );
    }

    /**
     * Returns form errors for JS implementation.
     *
     * Parse all errors mapped by id html field
     *
     * @param FormInterface $form
     *
     * @return array<array<string>> Errors
     *
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     */
    protected function getFormErrorsForJS(FormInterface $form): array
    {
        $errors = [];

        if ($form->count() === 0) {
            return $errors;
        }

        foreach ($form->getErrors(true) as $error) {
            if ($error->getCause() && method_exists($error->getCause(), 'getPropertyPath')) {
                $formId = str_replace(
                    ['.', 'children[', ']', '_data'],
                    ['_', '', '', ''],
                    $error->getCause()->getPropertyPath()
                );
            } else {
                $formId = 'bubbling_errors';
            }

            if ($error->getMessagePluralization()) {
                $errors[$formId][] = $this->trans(
                    $error->getMessageTemplate(),
                    array_merge(
                        $error->getMessageParameters(),
                        ['%count%' => $error->getMessagePluralization()]
                    ),
                    'validators'
                );
            } else {
                $errors[$formId][] = $this->trans(
                    $error->getMessageTemplate(),
                    $error->getMessageParameters(),
                    'validators'
                );
            }
        }

        return $errors;
    }

    /**
     * Interprets the filters provided in the request (based on the grid definition) and return a redirect
     * response to the provided route (usually the listing).
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function buildSearchResponse(
        GridDefinitionFactoryInterface $definitionFactory,
        Request $request,
        string $filterId,
        string $redirectRoute,
        array $queryParamsToKeep = []
    ): RedirectResponse {
        $responseBuilder = $this->container->get(ResponseBuilder::class);

        return $responseBuilder->buildSearchResponse(
            $definitionFactory,
            $request,
            $filterId,
            $redirectRoute,
            $queryParamsToKeep
        );
    }

    /**
     * Updates the position of a grid based on the provided PositionDefinition and provided data.
     *
     * @param PositionDefinitionInterface $positionDefinition
     * @param array $positionsData
     *
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function updateGridPosition(PositionDefinitionInterface $positionDefinition, array $positionsData): void
    {
        $positionUpdateFactory = $this->container->get(PositionUpdateFactoryInterface::class);
        $positionUpdate = $positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
        $updater = $this->container->get(GridPositionUpdaterInterface::class);
        $updater->update($positionUpdate);
    }

    /**
     * Adds a list of errors as flash error message.
     *
     * @param array $errorMessages Error message, can be a string or an array with parameters for trans method
     */
    protected function addFlashErrors(array $errorMessages): void
    {
        foreach ($errorMessages as $error) {
            $message = is_array($error) ? $this->trans($error['key'], $error['parameters'], $error['domain']) : $error;
            $this->addFlash('error', $message);
        }
    }

    protected function addFlashFormErrors(FormInterface $form): void
    {
        /** @var FormError $formError */
        foreach ($form->getErrors() as $formError) {
            $this->addFlash('error', $formError->getMessage());
        }
    }

    /**
     * Return the authorization level of the current employee for the request controller.
     *
     * @param string $legacyControllerName Name of the legacy controller of which the level is requested
     *
     * @return int
     */
    protected function getAuthorizationLevel(string $legacyControllerName): int
    {
        if ($this->isGranted(Permission::DELETE, $legacyControllerName)) {
            return Permission::LEVEL_DELETE;
        }

        if ($this->isGranted(Permission::CREATE, $legacyControllerName)) {
            return Permission::LEVEL_CREATE;
        }

        if ($this->isGranted(Permission::UPDATE, $legacyControllerName)) {
            return Permission::LEVEL_UPDATE;
        }

        if ($this->isGranted(Permission::READ, $legacyControllerName)) {
            return Permission::LEVEL_READ;
        }

        return 0;
    }

    protected function hasAuthorizationByShopConstraint(ShopConstraint $shopConstraint): bool
    {
        if (!$this->getShopContext()->isMultiShopEnabled()) {
            return true;
        }

        if ($shopConstraint->getShopId()) {
            return $this->getEmployeeContext()->hasAuthorizationOnShop($shopConstraint->getShopId()->getValue());
        }

        if ($shopConstraint->getShopGroupId()) {
            return $this->getEmployeeContext()->hasAuthorizationOnShopGroup($shopConstraint->getShopGroupId()->getValue());
        }

        if ($shopConstraint->forAllShops()) {
            return $this->getEmployeeContext()->hasAuthorizationForAllShops();
        }

        return false;
    }

    protected function isDemoModeEnabled(): bool
    {
        return (bool) $this->getConfiguration()->get('_PS_MODE_DEMO_');
    }

    protected function getDemoModeErrorMessage(): string
    {
        return $this->trans('This functionality has been disabled.', [], 'Admin.Notifications.Error');
    }
}
