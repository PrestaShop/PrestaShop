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
