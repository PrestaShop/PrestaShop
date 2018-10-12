<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\SqlManager\Form;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditing;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObjectFormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SqlRequestFormDataProvider.
 */
final class SqlRequestFormDataProvider implements IdentifiableObjectFormDataProviderInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param CommandBusInterface $queryBus
     * @param HookDispatcherInterface $hookDispatcher
     * @param Request $request
     */
    public function __construct(
        CommandBusInterface $queryBus,
        HookDispatcherInterface $hookDispatcher,
        Request $request = null
    ) {
        $this->request = $request;
        $this->queryBus = $queryBus;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getData($sqlRequestId = null)
    {
        $sqlRequestFormData = $this->getSqlRequestFormData($sqlRequestId);

        $this->hookDispatcher->dispatchWithParameters('actionSqlRequestFormDataModifier', [
            'id' => $sqlRequestId,
            'data' => &$sqlRequestFormData,
        ]);
    }

    /**
     * @param int|null $sqlRequestId
     *
     * @return array
     */
    private function getSqlRequestFormData($sqlRequestId)
    {
        if (null === $sqlRequestId) {
            return $this->getEmptySqlRequestFormData();
        }

        return $this->getPersistedSqlRequestFormData($sqlRequestId);
    }

    /**
     * Get data for empty form.
     *
     * @return array
     */
    private function getEmptySqlRequestFormData()
    {
        if ($this->request && $this->request->request->has('sql')) {
            return [
                'sql' => $this->request->request->get('sql'),
                'name' => $this->request->request->get('name'),
            ];
        }

        return [];
    }

    /**
     * @param int $sqlRequestId
     *
     * @return array
     */
    private function getPersistedSqlRequestFormData($sqlRequestId)
    {
        try {
            $getRequestSqlForEditingQuery = new GetSqlRequestForEditing($sqlRequestId);

            /** @var EditableSqlRequest $editableRequestSql */
            $editableRequestSql = $this->queryBus->handle($getRequestSqlForEditingQuery);

            return [
                'id' => $editableRequestSql->getSqlRequestId()->getValue(),
                'name' => $editableRequestSql->getName(),
                'sql' => $editableRequestSql->getSql(),
            ];
        } catch (SqlRequestException $e) {
            return [];
        }
    }
}
