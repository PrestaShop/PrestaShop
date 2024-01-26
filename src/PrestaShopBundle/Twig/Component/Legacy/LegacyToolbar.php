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

namespace PrestaShopBundle\Twig\Component\Legacy;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Help\Documentation;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Twig\Component\Toolbar;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/LegacyLayout/toolbar.html.twig')]
class LegacyToolbar extends Toolbar
{
    use LegacyControllerTrait;

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        MenuBuilder $menuBuilder,
        protected readonly LegacyContext $context,
        protected readonly Documentation $helpDocumentation,
        protected readonly LanguageContext $languageContext
    ) {
        parent::__construct($hookDispatcher, $menuBuilder);
    }

    /**
     * No parameters are passed to this component but we must respect the method signature.
     *
     * @param string $layoutTitle
     * @param string $helpLink
     * @param bool $enableSidebar
     */
    public function mount(string $layoutTitle = '', string $helpLink = '', bool $enableSidebar = false): void
    {
        parent::mount(
            $layoutTitle,
            urldecode($this->helpDocumentation->generateLink($this->getLegacyController()->controller_name, $this->languageContext->getIsoCode())),
            $enableSidebar
        );
    }

    public function getLayoutHeaderToolbarBtn(): array
    {
        return $this->getLegacyController()->page_header_toolbar_btn;
    }

    public function getTable(): string
    {
        return $this->getLegacyController()->table;
    }
}
