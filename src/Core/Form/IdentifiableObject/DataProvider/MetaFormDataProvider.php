<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Query\GetMetaForEditing;
use PrestaShop\PrestaShop\Core\Domain\Meta\QueryResult\EditableMeta;

/**
 * Class MetaFormDataProvider is responsible for providing required data for meta form.
 */
final class MetaFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     *
     * @throws MetaException
     */
    public function getData($metaId)
    {
        /** @var EditableMeta $result */
        $result = $this->queryBus->handle(new GetMetaForEditing((int) $metaId));

        return [
            'page_name' => $result->getPageName()->getValue(),
            'page_title' => $result->getLocalisedPageTitles(),
            'meta_description' => $result->getLocalisedMetaDescriptions(),
            'url_rewrite' => $result->getLocalisedUrlRewrites(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData()
    {
        return null;
    }
}
