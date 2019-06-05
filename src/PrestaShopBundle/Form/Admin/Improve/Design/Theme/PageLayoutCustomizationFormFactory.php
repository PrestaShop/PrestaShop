<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Addon\Theme\ThemeRepository;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\LayoutCustomizationPage;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class PageLayoutCustomizationFormFactory creates form for Front Office theme's pages layout customization.
 */
final class PageLayoutCustomizationFormFactory implements PageLayoutCustomizationFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var ThemeRepository
     */
    private $themeRepository;

    /**
     * @var string
     */
    private $shopThemeName;

    /**
     * @param FormFactoryInterface $formFactory
     * @param ThemeRepository $themeRepository
     * @param string $shopThemeName
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        ThemeRepository $themeRepository,
        $shopThemeName
    ) {
        $this->formFactory = $formFactory;
        $this->themeRepository = $themeRepository;
        $this->shopThemeName = $shopThemeName;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $customizablePages)
    {
        $theme = $this->themeRepository->getInstanceByName($this->shopThemeName);

        $pageLayoutCustomizationForm = $this->formFactory->create(PageLayoutsCustomizationType::class, [
            'layouts' => $this->getCustomizablePageLayouts($theme, $customizablePages),
        ]);

        return $pageLayoutCustomizationForm;
    }

    /**
     * @param Theme $theme
     * @param LayoutCustomizationPage[] $customizationPages
     *
     * @return array
     */
    private function getCustomizablePageLayouts(Theme $theme, array $customizationPages)
    {
        $defaultLayout = $theme->getDefaultLayout();
        $pageLayouts = $theme->getPageLayouts();

        $layouts = [];

        foreach ($customizationPages as $page) {
            $selectedLayout = isset($pageLayouts[$page->getPage()]) ?
                $pageLayouts[$page->getPage()] :
                $defaultLayout['key'];

            $layouts[$page->getPage()] = $selectedLayout;
        }

        return $layouts;
    }
}
