<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
