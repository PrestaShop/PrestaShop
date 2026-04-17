<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
