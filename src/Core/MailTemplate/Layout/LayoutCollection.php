<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;

class LayoutCollection extends AbstractTypedCollection implements LayoutCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return LayoutInterface::class;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(LayoutCollectionInterface $collection)
    {
        /** @var LayoutInterface $newLayout */
        foreach ($collection as $newLayout) {
            if (null !== ($oldLayout = $this->getLayout($newLayout->getName(), $newLayout->getModuleName()))) {
                $this->replace($oldLayout, $newLayout);
            } else {
                $this->add($newLayout);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function replace(LayoutInterface $oldLayout, LayoutInterface $newLayout)
    {
        if (!$this->contains($oldLayout)) {
            return false;
        }

        $oldLayoutIndex = $this->indexOf($oldLayout);
        $this->offsetSet($oldLayoutIndex, $newLayout);

        return $this->contains($newLayout);
    }

    /**
     * {@inheritdoc}
     */
    public function getLayout($layoutName, $moduleName)
    {
        /** @var LayoutInterface $layout */
        foreach ($this as $layout) {
            if ($layoutName === $layout->getName() && $moduleName === $layout->getModuleName()) {
                return $layout;
            }
        }

        return null;
    }
}
