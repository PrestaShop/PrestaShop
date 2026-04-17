<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\ImageType;

class ImageTypeRepository extends EntityRepository
{
    /**
     * Get an image type by its name.
     *
     * @param string $typeName
     *
     * @return ImageType|null return null if feature flag cannot be found
     */
    public function getByName(string $typeName): ?ImageType
    {
        return $this->findOneBy(['name' => $typeName]);
    }

    /**
     * Save an image type into database.
     *
     * @param ImageType $imageType
     *
     * @return ImageType
     */
    public function save(ImageType $imageType): ImageType
    {
        $this->getEntityManager()->persist($imageType);
        $this->getEntityManager()->flush();

        return $imageType;
    }

    /**
     * Delete an image type into database.
     *
     * @param ImageType $imageType
     *
     * @return void
     */
    public function delete(ImageType $imageType): void
    {
        $this->getEntityManager()->remove($imageType);
        $this->getEntityManager()->flush();
    }
}
