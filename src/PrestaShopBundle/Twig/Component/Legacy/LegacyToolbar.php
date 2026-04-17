<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    public function mount(string $layoutTitle = '', string $helpLink = '', bool $enableSidebar = false, string $layoutSubTitle = '', array $layoutHeaderToolbarBtn = [], array $breadcrumbLinks = []): void
    {
        if (empty($helpLink) && $this->hasLegacyController()) {
            $helpLink = urldecode($this->helpDocumentation->generateLink($this->getLegacyController()->controller_name, $this->languageContext->getIsoCode()));
        }

        if (empty($layoutHeaderToolbarBtn) && $this->hasLegacyController()) {
            $layoutHeaderToolbarBtn = $this->getLegacyController()->page_header_toolbar_btn;
        }

        parent::mount(
            $layoutTitle,
            $helpLink,
            $enableSidebar,
            $layoutSubTitle,
            $layoutHeaderToolbarBtn,
            $breadcrumbLinks,
        );
    }

    public function getTable(): string
    {
        if ($this->hasLegacyController()) {
            return $this->getLegacyController()->table;
        }

        return 'configuration';
    }
}
