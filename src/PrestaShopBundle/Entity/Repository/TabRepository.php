<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use PrestaShopBundle\Entity\Tab;

class TabRepository extends EntityRepository
{
    /**
     * @param $moduleName
     *
     * @return array
     */
    public function findByModule($moduleName)
    {
        return $this->findBy(['module' => $moduleName]);
    }

    /**
     * @param $idParent
     *
     * @return array
     */
    public function findByParentId($idParent)
    {
        return $this->findBy(['idParent' => $idParent]);
    }

    /**
     * @param $className
     *
     * @return \PrestaShopBundle\Entity\Tab|null
     */
    public function findOneByClassName($className)
    {
        return $this->findOneBy(['className' => $className]);
    }

    /**
     * @param $className
     *
     * @return int|null
     */
    public function findOneIdByClassName($className)
    {
        $tab = $this->findOneByClassName($className);
        if ($tab) {
            return $tab->getId();
        }

        return null;
    }

    /**
     * Changes tab status.
     *
     * @param string $className tab's class name
     * @param bool $status wanted status for the tab
     *
     * @throws \InvalidArgumentException
     */
    public function changeStatusByClassName($className, $status)
    {
        if (!is_bool($status)) {
            throw new \InvalidArgumentException(sprintf('Invalid type: bool expected, got %s', gettype($status)));
        }

        /** @var Tab $tab */
        $tab = $this->findOneByClassName($className);

        if (null !== $tab) {
            $tab->setActive($status);
            $this->getEntityManager()->persist($tab);
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param string $moduleName
     * @param bool $enabled
     */
    public function changeEnabledByModuleName($moduleName, $enabled)
    {
        $tabs = $this->findByModule($moduleName);
        /** @var Tab $tab */
        foreach ($tabs as $tab) {
            $tab->setEnabled($enabled);
        }
        $this->getEntityManager()->flush();
    }
}
