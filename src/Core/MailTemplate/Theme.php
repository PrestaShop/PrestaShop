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

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;

/**
 * Class MailTheme basic immutable implementation of MailThemeInterface.
 */
class Theme implements ThemeInterface
{
    /** @var string */
    private $name;

    /**
     * @var LayoutCollectionInterface
     */
    private $layouts;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->layouts = new LayoutCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return LayoutCollectionInterface
     */
    public function getLayouts()
    {
        return $this->layouts;
    }

    /**
     * @param LayoutCollectionInterface $layouts
     *
     * @return $this
     */
    public function setLayouts($layouts)
    {
        $this->layouts = $layouts;

        return $this;
    }
}
