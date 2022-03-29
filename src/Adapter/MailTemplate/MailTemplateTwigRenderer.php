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

namespace PrestaShop\PrestaShop\Adapter\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutVariablesBuilderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\TransformationInterface;
use Twig\Environment;
use Twig\Error\LoaderError;

/**
 * MailTemplateTwigRenderer is a basic implementation of MailTemplateRendererInterface
 * using the twig engine.
 */
class MailTemplateTwigRenderer implements MailTemplateRendererInterface
{
    /** @var Environment */
    private $twig;

    /** @var LayoutVariablesBuilderInterface */
    private $variablesBuilder;

    /** @var HookDispatcherInterface */
    private $hookDispatcher;

    /** @var TransformationCollection */
    private $transformations;

    /**
     * @param Environment $twig
     * @param LayoutVariablesBuilderInterface $variablesBuilder
     * @param HookDispatcherInterface $hookDispatcher
     *
     * @throws TypeException
     */
    public function __construct(
        Environment $twig,
        LayoutVariablesBuilderInterface $variablesBuilder,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->twig = $twig;
        $this->variablesBuilder = $variablesBuilder;
        $this->hookDispatcher = $hookDispatcher;
        $this->transformations = new TransformationCollection();
    }

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @throws TypeException
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws TypeException
     */
    public function renderHtml(LayoutInterface $layout, LanguageInterface $language)
    {
        return $this->render($layout, $language, MailTemplateInterface::HTML_TYPE);
    }

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @throws FileNotFoundException
     * @throws TypeException
     *
     * @return string
     */
    public function renderTxt(LayoutInterface $layout, LanguageInterface $language)
    {
        return $this->render($layout, $language, MailTemplateInterface::TXT_TYPE);
    }

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     * @param string $templateType
     *
     * @return string
     *
     * @throws FileNotFoundException
     * @throws TypeException
     */
    private function render(
        LayoutInterface $layout,
        LanguageInterface $language,
        $templateType
    ) {
        $layoutVariables = $this->variablesBuilder->buildVariables($layout, $language);
        $layoutVariables['templateType'] = $templateType;
        if (MailTemplateInterface::HTML_TYPE === $templateType) {
            $layoutPath = !empty($layout->getHtmlPath()) ? $layout->getHtmlPath() : $layout->getTxtPath();
        } else {
            $layoutPath = !empty($layout->getTxtPath()) ? $layout->getTxtPath() : $layout->getHtmlPath();
        }

        try {
            $renderedTemplate = $this->twig->render($layoutPath, $layoutVariables);
        } catch (LoaderError $e) {
            throw new FileNotFoundException(sprintf('Could not find layout file: %s', $layoutPath));
        }

        $templateTransformations = $this->getMailLayoutTransformations($layout, $templateType);
        /** @var TransformationInterface $transformation */
        foreach ($templateTransformations as $transformation) {
            $renderedTemplate = $transformation
                ->setLanguage($language)
                ->apply($renderedTemplate, $layoutVariables)
            ;
        }

        return $renderedTemplate;
    }

    /**
     * @param LayoutInterface $mailLayout
     * @param string $templateType
     *
     * @return TransformationCollection
     *
     * @throws TypeException
     */
    private function getMailLayoutTransformations(LayoutInterface $mailLayout, $templateType)
    {
        $themeName = '';
        $htmlPath = $mailLayout->getHtmlPath();
        if ($htmlPath !== null && preg_match('#mails/themes/([^/]+)/#', $htmlPath, $matches)) {
            $themeName = $matches[1];
        }
        $templateTransformations = new TransformationCollection();
        /** @var TransformationInterface $transformation */
        foreach ($this->transformations as $transformation) {
            if (get_class($transformation) == 'PrestaShop\PrestaShop\Core\MailTemplate\Transformation\CSSInlineTransformation' && $themeName == 'modern') {
                continue;
            }
            if ($templateType !== $transformation->getType()) {
                continue;
            }

            $templateTransformations->add($transformation);
        }

        //This hook allows to add/remove transformations during a layout rendering
        $this->hookDispatcher->dispatchWithParameters(
            MailTemplateRendererInterface::GET_MAIL_LAYOUT_TRANSFORMATIONS,
            [
                'mailLayout' => $mailLayout,
                'templateType' => $templateType,
                'layoutTransformations' => $templateTransformations,
            ]
        );

        return $templateTransformations;
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
