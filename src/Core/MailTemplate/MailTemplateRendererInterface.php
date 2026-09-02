<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationInterface;

/**
 * MailTemplateRendererInterface is used to render a specific MailLayoutInterface
 * with the specified LanguageInterface.
 */
interface MailTemplateRendererInterface
{
    public const GET_MAIL_LAYOUT_TRANSFORMATIONS = 'actionGetMailLayoutTransformations';

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @return string
     */
    public function renderTxt(LayoutInterface $layout, LanguageInterface $language);

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @return string
     */
    public function renderHtml(LayoutInterface $layout, LanguageInterface $language);

    /**
     * Adds a transformer to the renderer, when template is rendered all transformers
     * matching its type (html or txt) are applied to the output content.
     *
     * @param TransformationInterface $transformer
     *
     * @return $this
     */
    public function addTransformation(TransformationInterface $transformer);
}
