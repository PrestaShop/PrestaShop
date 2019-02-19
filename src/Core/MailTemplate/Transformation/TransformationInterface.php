<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate\Transformation;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;

/**
 * TransformationInterface is used by the MailTemplateRendererInterface to apply a
 * transformation on the generated template (textify html, inline css, add a css or
 * an image per language).
 */
interface TransformationInterface
{
    /**
     * @param string $templateContent
     * @param array $templateVariables
     *
     * @return string
     */
    public function apply($templateContent, array $templateVariables);

    /**
     * Returns the type of templates this transformation is associated with,
     * either html or txt, so that the renderer knows if it has to be applied
     * or not
     *
     * @return string
     */
    public function getType();

    /**
     * @param LanguageInterface $language
     *
     * @return $this
     */
    public function setLanguage(LanguageInterface $language);
}
