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

namespace PrestaShopBundle\Entity\Repository;

use CMS;
use PrestaShop\PrestaShop\Adapter\Util\Entity\EntityNameDuplicator;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CmsPageSettings;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDuplicateCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class CmsPageRepository extends AbstractObjectModelRepository
{
    /**
     * @var EntityNameDuplicator
     */
    private $entityNameDuplicator;

    /**
     * @param EntityNameDuplicator $entityNameDuplicator
     */
    public function __construct(
      EntityNameDuplicator $entityNameDuplicator
    ) {
        $this->entityNameDuplicator = $entityNameDuplicator;
    }

    /**
     * Duplicates a CMS page
     *
     * @param CmsPageId $cmsPageId
     *
     * @return CmsPageId
     *
     * @throws CmsPageNotFoundException
     * @throws CannotDuplicateCmsPageException
     */
    public function duplicate(CmsPageId $cmsPageId): CmsPageId
    {
        /** @var Cms $cms */
        $cms = $this->getObjectModel(
          $cmsPageId->getValue(),
          Cms::class,
          CmsPageNotFoundException::class
        );

        // Reset object ID, make it inactive and save it
        $cms->id = null;
        $cms->meta_title = $this->entityNameDuplicator->getNewLocalizedNames(
            $cms->meta_title,
            CmsPageSettings::MAX_TITLE_LENGTH
        );
        $cms->active = false;
        $id = $this->addObjectModel($cms, CannotDuplicateCmsPageException::class);

        return new CmsPageId($id);
    }
}
