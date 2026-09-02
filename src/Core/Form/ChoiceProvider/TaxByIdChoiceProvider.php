<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Provides individual tax choices (id => "name (rate%)").
 */
final class TaxByIdChoiceProvider implements FormChoiceProviderInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly int $langId,
    ) {
    }

    /**
     * @return array<string, int>
     */
    public function getChoices(): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('t.id_tax', 'tl.name', 't.rate')
            ->from($this->dbPrefix . 'tax', 't')
            ->innerJoin('t', $this->dbPrefix . 'tax_lang', 'tl', 't.id_tax = tl.id_tax AND tl.id_lang = :langId')
            ->where('t.active = 1')
            ->setParameter('langId', $this->langId)
            ->orderBy('tl.name', 'ASC')
        ;

        $taxes = $qb->executeQuery()->fetchAllAssociative();
        $choices = [];

        foreach ($taxes as $tax) {
            $label = sprintf('%s (%s%%)', $tax['name'], $tax['rate']);
            $choices[$label] = (int) $tax['id_tax'];
        }

        return $choices;
    }
}
