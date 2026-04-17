<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\LayoutCustomizationPage;
use Symfony\Component\Form\FormInterface;

/**
 * Interface PageLayoutCustomizationFormFactoryInterface.
 */
interface PageLayoutCustomizationFormFactoryInterface
{
    /**
     * @param LayoutCustomizationPage[] $customizablePages
     *
     * @return FormInterface
     */
    public function create(array $customizablePages);
}
