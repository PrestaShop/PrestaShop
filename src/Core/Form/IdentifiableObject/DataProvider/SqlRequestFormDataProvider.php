<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditing;

/**
 * Provides data for SqlRequest forms.
 */
final class SqlRequestFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($id)
    {
        $getRequestSqlForEditingQuery = new GetSqlRequestForEditing($id);

        /** @var EditableSqlRequest $editableSqlRequest */
        $editableSqlRequest = $this->queryBus->handle($getRequestSqlForEditingQuery);

        return [
            'id' => $editableSqlRequest->getSqlRequestId()->getValue(),
            'name' => $editableSqlRequest->getName(),
            'sql' => $editableSqlRequest->getSql(),
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
