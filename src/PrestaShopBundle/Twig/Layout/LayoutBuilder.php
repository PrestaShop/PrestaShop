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

namespace PrestaShopBundle\Twig\Layout;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Entity\Repository\TabRepository;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Entity\TabLang;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Layout builder is responsible for building all the variables necessary for the layout.
 */
class LayoutBuilder
{
    private ?string $metaTitle = null;

    public function __construct(
        private LegacyContext $legacyContext,
        private TabRepository $tabRepository,
        private RequestStack $requestStack
    ) {
    }

    public function buildView(): LayoutView
    {
        return new LayoutView(
            $this->getMetaTitle(),
            $this->getSmartyTemplateVariables()
        );
    }

    public function getMetaTitle(): string
    {
        if ($this->metaTitle) {
            return $this->metaTitle;
        }

        $ancestors = $this->tabRepository->getAncestors($this->getCurrentTabId());

        /** @var Tab $current */
        $current = end($ancestors);

        $currentTabLang = null;
        /** @var TabLang $tabLang */
        foreach ($current->getTabLangs() as $tabLang) {
            if ($tabLang->getLang()->getId() === $this->getLanguageId()) {
                $currentTabLang = $tabLang;
                break;
            }
        }

        if (empty($currentTabLang)) {
            $currentTabLang = $current->getTabLangs()[0];
        }

        return $currentTabLang->getName();
    }

    public function setMetaTitle(string $metaTitle): self
    {
        $this->metaTitle = $metaTitle;

        return $this;
    }

    public function getSmartyTemplateVariables(): array
    {
        return [
            'controller_name' => $this->getControllerName(),
        ];
    }

    private function getCurrentTabId(): int
    {
        return $this->tabRepository->getIdByClassName($this->getControllerName());
    }

    /**
     * @return string
     */
    private function getControllerName(): string
    {
        return $this->requestStack->getMainRequest()->attributes->get('_legacy_controller');
    }

    /**
     * Should be refactored not to depend on the legacy context, the language should
     * be fetched from a dedicated sub context service.
     *
     * @return int
     */
    private function getLanguageId(): int
    {
        return $this->legacyContext->getLanguage()->getId();
    }
}
