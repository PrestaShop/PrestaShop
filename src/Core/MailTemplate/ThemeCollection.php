<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;

/**
 * Class MailThemeCollection is a collection of MailThemeInterface elements.
 */
class ThemeCollection extends AbstractTypedCollection implements ThemeCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getType()
    {
        return ThemeInterface::class;
    }

    /**
     * @param string $themeName
     *
     * @return ThemeInterface|null
     */
    public function getByName($themeName)
    {
        /* @phpstan-ignore-next-line $theme can't be null as we are in a typed collection */
        return $this->filter(function (ThemeInterface $theme) use ($themeName) {
            return $themeName === $theme->getName();
        })->first();
    }
}
