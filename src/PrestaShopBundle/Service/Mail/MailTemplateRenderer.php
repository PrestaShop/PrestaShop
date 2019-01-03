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

use Symfony\Component\Templating\EngineInterface;

class MailTemplateRenderer
{
    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param EngineInterface $engine
     */
    public function __construct(EngineInterface $engine) {
        $this->engine = $engine;
    }

    /**
     * @param MailTemplateInterface $template
     * @param null $language
     *
     * @return string
     */
    public function render(MailTemplateInterface $template, $language = null)
    {
        $locale = null !== $language ? $language : $this->defaultLocale;
        $parameters = [
            '_locale' => $locale,
        ];

        return $this->engine->render($template->getPath(), $parameters);
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * @param string $defaultLocale
     * @return $this
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }
}
