<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
