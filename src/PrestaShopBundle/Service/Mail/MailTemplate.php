<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Service\Mail;

class MailTemplate implements MailTemplateInterface
{
    /** @var string */
    private $theme;

    /** @var string */
    private $type;

    /** @var string */
    private $name;

    /** @var string */
    private $path;

    /** @var string|null */
    private $module;

    /**
     * MailTemplate constructor.
     *
     * @param string $theme
     * @param string $type
     * @param string $name
     * @param string $path
     * @param string|null $module
     */
    public function __construct(
        $theme,
        $type,
        $name,
        $path,
        $module = null
    ) {
        $this->theme = $theme;
        $this->type = $type;
        $this->name = $name;
        $this->path = $path;
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getModule()
    {
        return $this->module;
    }
}
