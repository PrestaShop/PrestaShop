<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShopBundle\Entity\AdminGridView;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GridViewUrlGenerator
{
    public const SELECTED_VIEW_PARAM = 'grid_view';

    /**
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @param AdminGridView $gridView
     *
     * @return string
     */
    public function generate(AdminGridView $gridView): string
    {
        return $this->urlGenerator->generate($gridView->getGridConfiguration()->getControllerRoute(), [
            self::SELECTED_VIEW_PARAM => $gridView->getId(),
        ]);
    }
}
