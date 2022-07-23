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

namespace PrestaShop\PrestaShop\Core\Search\Row;

use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Search\SearchPanel;
use PrestaShop\PrestaShop\Core\Search\SearchPanelInterface;
use Symfony\Component\Translation\TranslatorInterface;

final class HookableSearchPanel {
    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(HookDispatcherInterface $hookDispatcher, TranslatorInterface $translator)
    {
        $this->hookDispatcher = $hookDispatcher;
        $this->translator = $translator;
    }

    public function build(string $searchExpression): array
    {
        $searchPanels = [];
        $searchPanels[] = new SearchPanel(
            $this->translator->trans('Search docs.prestashop-project.org', [], 'Admin.Navigation.Search'),
            $this->translator->trans('Go to the documentation', [], 'Admin.Navigation.Search'),
            'https://docs.prestashop-project.org/welcome/',
            [
                'q' => $searchExpression,
            ]
        );

        // Get additional search panels from hooks
        $alternativeSearchPanelsFromModules = $this->hookDispatcher->dispatchRenderingWithParameters('actionGetAlternativeSearchPanels', [
            'previous_search_panels' => $searchPanels,
            'bo_query' => $searchExpression,
        ])->getContent();

        foreach ($alternativeSearchPanelsFromModules as $alternativeSearchPanelsFromModule) {
            foreach ($alternativeSearchPanelsFromModule as $alternativeSearchPanel) {
                if ($alternativeSearchPanel instanceof SearchPanelInterface) {
                    $searchPanels[] = $alternativeSearchPanel;
                }
            }
        }

        return $searchPanels;
    }
}
