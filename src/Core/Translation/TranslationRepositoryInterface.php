<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Translation;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ObjectRepository;

/**
 * Interface TranslationRepositoryInterface allows to fetch a TranslationInterface
 * via different methods.
 */
interface TranslationRepositoryInterface extends ObjectRepository
{
    /**
     * @param mixed $alias
     * @param mixed|null $indexBy
     *
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null);
}
