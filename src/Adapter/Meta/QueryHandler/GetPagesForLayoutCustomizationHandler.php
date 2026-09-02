<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta\QueryHandler;

use Meta;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetPagesForLayoutCustomization;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryHandler\GetPagesForLayoutCustomizationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\LayoutCustomizationPage;

/**
 * Class GetMetaPagesListHandler.
 */
#[AsQueryHandler]
final class GetPagesForLayoutCustomizationHandler implements GetPagesForLayoutCustomizationHandlerInterface
{
    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param int $contextLangId
     */
    public function __construct($contextLangId)
    {
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetPagesForLayoutCustomization $query)
    {
        $metas = Meta::getAllMeta($this->contextLangId);
        $pages = [];

        foreach ($metas as $meta) {
            $pages[] = new LayoutCustomizationPage(
                $meta['page'],
                $meta['title'],
                $meta['description']
            );
        }

        return $pages;
    }
}
