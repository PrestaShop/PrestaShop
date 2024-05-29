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
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\GridDefinitionFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenterInterface;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Module\Exception\ModuleErrorInterface;
use PrestaShopBundle\Service\Grid\ResponseBuilder;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            ConfigurationInterface::class => ConfigurationInterface::class,
            CommandBusInterface::class => CommandBusInterface::class,
            HookDispatcherInterface::class => HookDispatcherInterface::class,
            TranslatorInterface::class => TranslatorInterface::class,
            GridPresenterInterface::class => GridPresenterInterface::class,
            LanguageContext::class => LanguageContext::class,
            Documentation::class => Documentation::class,
            ResponseBuilder::class => ResponseBuilder::class,
            PositionUpdateFactoryInterface::class => PositionUpdateFactoryInterface::class,
            GridPositionUpdaterInterface::class => GridPositionUpdaterInterface::class,
        ];
    }

    protected function getConfiguration(): ConfigurationInterface
    {
        return $this->container->get(ConfigurationInterface::class);
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

        $iso = $this->container->get(LanguageContext::class)->getIsoCode();
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
    protected function getErrorMessageForException(Throwable $e, array $messages): string
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

        // Fallback error message
        $isDebug = $this->getParameter('kernel.debug');
        if ($isDebug && !empty($message)) {
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
     * @param PositionDefinition $positionDefinition
     * @param array $positionsData
     *
     * @return void
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function updateGridPosition(PositionDefinition $positionDefinition, array $positionsData): void
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
}
