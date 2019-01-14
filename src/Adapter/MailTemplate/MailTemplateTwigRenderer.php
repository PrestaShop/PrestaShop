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

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailLayoutVariablesBuilderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationInterface;
use Symfony\Component\Templating\EngineInterface;
use Language;

/**
 * MailTemplateTwigRenderer is a basic implementation of MailTemplateRendererInterface
 * using the twig engine.
 */
class MailTemplateTwigRenderer implements MailTemplateRendererInterface
{
    /** @var EngineInterface */
    private $engine;

    /** @var MailLayoutVariablesBuilderInterface */
    private $variablesBuilder;

    /** @var TransformationInterface[] */
    private $transformations;

    /**
     * @param EngineInterface $engine
     * @param MailLayoutVariablesBuilderInterface $variablesBuilder
     */
    public function __construct(
        EngineInterface $engine,
        MailLayoutVariablesBuilderInterface $variablesBuilder
    ) {
        $this->engine = $engine;
        $this->variablesBuilder = $variablesBuilder;
        $this->transformations = new TransformationCollection();
    }

    /**
     * @param MailLayoutInterface $layout
     * @param Language $language
     *
     * @return string
     */
    public function renderHtml(MailLayoutInterface $layout, Language $language)
    {
        return $this->render($layout, $language, MailTemplateInterface::HTML_TYPE);
    }

    /**
     * @param MailLayoutInterface $layout
     * @param Language $language
     *
     * @return string
     */
    public function renderTxt(MailLayoutInterface $layout, Language $language)
    {
        return $this->render($layout, $language, MailTemplateInterface::TXT_TYPE);
    }

    /**
     * @param MailLayoutInterface $layout
     * @param Language $language
     * @param string $templateType
     *
     * @return string
     */
    private function render(
        MailLayoutInterface $layout,
        Language $language,
        $templateType
    ) {
        $parameters = $this->variablesBuilder->buildVariables($layout, $language);
        if (MailTemplateInterface::HTML_TYPE === $templateType) {
            $layoutPath = !empty($layout->getHtmlPath()) ? $layout->getHtmlPath() : $layout->getTxtPath();
        } else {
            $layoutPath = !empty($layout->getTxtPath()) ? $layout->getTxtPath() : $layout->getHtmlPath();
        }

        $renderedTemplate = $this->engine->render($layoutPath, $parameters);
        /** @var TransformationInterface $transformation */
        foreach ($this->transformations as $transformation) {
            if ($templateType !== $transformation->getType()) {
                continue;
            }

            $renderedTemplate = $transformation
                ->setLanguage($language)
                ->apply($renderedTemplate, $parameters)
            ;
        }

        return $renderedTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function addTransformation(TransformationInterface $transformation)
    {
        $this->transformations[] = $transformation;

        return $this;
    }
}
