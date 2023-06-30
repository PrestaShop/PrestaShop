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

use Exception;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryContainerInterface;
use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenter;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShopBundle\Service\Controller\ErrorFormatter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Default controller for PrestaShop admin pages.
 */
class PrestaShopAdminController extends AbstractController
{
    /**
     * @internal
     * {@inheritdoc}
     */
    protected $container;

    public static function getSubscribedServices(): array
    {
        return parent::getSubscribedServices() + [
            GridPresenter::class => GridPresenter::class,
            ErrorFormatter::class => ErrorFormatter::class,
            ParameterBagInterface::class => ParameterBagInterface::class,
            CommandBusInterface::class => CommandBusInterface::class,
            GridFactoryContainerInterface::class => GridFactoryContainerInterface::class,
            TranslatorInterface::class => TranslatorInterface::class,
            LegacyContext::class => LegacyContext::class, // for internal use only
            Documentation::class => Documentation::class,
        ];
    }

    protected function get(string $id): never
    {
        throw new \RuntimeException('Use dependency injection instead');
    }

    /**
     * Get commands bus to execute commands.
     */
    protected function dispatchCommand(mixed $command): mixed
    {
        return $this->container->get(CommandBusInterface::class)->handle($command);
    }

    protected function dispatchQuery(mixed $query): mixed
    {
        return $this->container->get(CommandBusInterface::class)->handle($query);
    }

    protected function getErrorMessageForException(Exception $e, array $getErrorMessages)
    {
        return $this->container->get(ErrorFormatter::class)->getErrorMessageForException($e, $getErrorMessages);
    }

    /**
     * Get the translated chain from key.
     *
     * @param string $key the key to be translated
     * @param string $domain the domain to be selected
     * @param array $parameters Optional, pass parameters if needed (uncommon)
     *
     * @return string
     */
    protected function trans(string $key, string $domain, array $parameters = []): string
    {
        return $this->container->get(TranslatorInterface::class)->trans($key, $parameters, $domain);
    }

    protected function getGrid(string $gridId, SearchCriteriaInterface $searchCriteria): GridInterface
    {
        return $this->container->get(GridFactoryContainerInterface::class)->getGridFactory($gridId)->getGrid($searchCriteria);
    }

    protected function generateSidebarLink(string $section, ?string $title = null): string
    {
        $label = $title ?: $this->trans('Help', 'Admin.Global');

        $iso = (string) $this->container->get(LegacyContext::class)->getEmployeeLanguageIso();

        $url = $this->generateUrl('admin_common_sidebar', [
            'url' => $this->container->get(Documentation::class)->generateLink($section, $iso),
            'title' => $label,
        ]);

        //this line is allow to revert a new behaviour introduce in sf 5.4 which break the result we used to have
        return strtr($url, ['%2F' => '%252F']);
    }

    /**
     * Present provided grid.
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    protected function presentGrid(GridInterface $grid)
    {
        return $this->container->get(GridPresenter::class)->present($grid);
    }

    /**
     * Get Admin URI from PrestaShop 1.6 Back Office.
     *
     * @param string $controller the old Controller name
     * @param bool $withToken whether we add token or not
     * @param array $params url parameters
     *
     * @return string the page URI (with token)
     */
    protected function getAdminLink($controller, array $params, $withToken = true)
    {
        return $this->container->get(LegacyContext::class)->getAdminLink($controller, $withToken, $params);
    }

    protected function getContextLangId(): int
    {
        return $this->container->get(LegacyContext::class)->getLanguage()->id;
    }
}
