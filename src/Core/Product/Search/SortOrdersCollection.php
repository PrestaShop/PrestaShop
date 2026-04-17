<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Product\Search;

use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class provide the list of default Sort Orders.
 */
final class SortOrdersCollection
{
    /**
     * @var TranslatorInterface the translator
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Returns a set of default sort orders used by core search providers on all pages.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getDefaults()
    {
        return [
            (new SortOrder('product', 'name', 'asc'))->setLabel(
                $this->translator->trans('Name, A to Z', [], 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'name', 'desc'))->setLabel(
                $this->translator->trans('Name, Z to A', [], 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'price', 'asc'))->setLabel(
                $this->translator->trans('Price, low to high', [], 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'price', 'desc'))->setLabel(
                $this->translator->trans('Price, high to low', [], 'Shop.Theme.Catalog')
            ),
        ];
    }
}
