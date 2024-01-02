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

namespace PrestaShop\PrestaShop\Adapter\QuickAccess\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\QuickAccess\QuickAccessRepositoryInterface;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class QuickAccessRepository extends AbstractObjectModelRepository implements QuickAccessRepositoryInterface
{
    public function __construct(
        private Connection $connection,
        private string $dbPrefix
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll(LanguageId $languageId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('q.id_quick_access, q.new_window, q.link, ql.name')
            ->from($this->dbPrefix . 'quick_access', 'q')
            ->innerJoin(
                'q',
                $this->dbPrefix . 'quick_access_lang',
                'ql',
                'q.id_quick_access = ql.id_quick_access'
            )
            ->where('ql.id_lang = :languageId')
            ->addOrderBy('ql.name', 'ASC')
            ->setParameter('languageId', $languageId->getValue())
        ;

        return $qb->execute()->fetchAllAssociative();
    }
}
