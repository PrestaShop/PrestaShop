<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportRunNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportRunId;
use PrestaShopBundle\Entity\ImportRun;

/**
 * The only class that persists/loads {@see ImportRun}. Handlers delegate to it; they never touch
 * the EntityManager directly.
 */
class ImportRunRepository extends EntityRepository
{
    public function add(ImportRun $importRun): void
    {
        $em = $this->getEntityManager();
        $em->persist($importRun);
        $em->flush();
    }

    public function save(ImportRun $importRun): void
    {
        $em = $this->getEntityManager();
        $em->persist($importRun);
        $em->flush();
    }

    /**
     * @throws ImportRunNotFoundException
     */
    public function getById(ImportRunId $importRunId): ImportRun
    {
        $importRun = $this->find($importRunId->getValue());

        if (!$importRun instanceof ImportRun) {
            throw new ImportRunNotFoundException(sprintf('Import run "%s" was not found.', $importRunId->getValue()));
        }

        return $importRun;
    }
}
