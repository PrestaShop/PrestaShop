<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

    /** @var bool */
    private $hasGiftWrapping;

    /** @var string */
    private $moduleDirectory;

    /**
     * @param Environment $twig
     * @param LayoutVariablesBuilderInterface $variablesBuilder
     * @param HookDispatcherInterface $hookDispatcher
     * @param bool $hasGiftWrapping
     * @param string $moduleDirectory Absolute path of the modules folder, used to resolve layouts declared by modules
     *
     * @throws TypeException
     */
    public function __construct(
        Environment $twig,
        LayoutVariablesBuilderInterface $variablesBuilder,
        HookDispatcherInterface $hookDispatcher,
        bool $hasGiftWrapping,
        string $moduleDirectory = ''
    ) {
        $this->twig = $twig;
        $this->variablesBuilder = $variablesBuilder;
        $this->hookDispatcher = $hookDispatcher;
        $this->transformations = new TransformationCollection();
        $this->hasGiftWrapping = $hasGiftWrapping;
        $this->moduleDirectory = $moduleDirectory;
    }

    /**
     * @param LayoutInterface $layout
     * @param LanguageInterface $language
     *
     * @return string
     *
     * @throws TypeException
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
     * @return string
     *
     * @throws FileNotFoundException
     * @throws TypeException
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
        $layoutVariables['giftWrapping'] = $this->hasGiftWrapping;
        if (MailTemplateInterface::HTML_TYPE === $templateType) {
            $layoutPath = !empty($layout->getHtmlPath()) ? $layout->getHtmlPath() : $layout->getTxtPath();
        } else {
            $layoutPath = !empty($layout->getTxtPath()) ? $layout->getTxtPath() : $layout->getHtmlPath();
        }

        try {
            $renderedTemplate = $this->twig->render($this->getTwigPath($layoutPath), $layoutVariables);
        } catch (LoaderError) {
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
            if ($transformation::class == 'PrestaShop\PrestaShop\Core\MailTemplate\Transformation\CSSInlineTransformation' && $themeName == 'modern') {
                continue;
            }
            if ($templateType !== $transformation->getType()) {
                continue;
            }

            $templateTransformations->add($transformation);
        }

        // This hook allows to add/remove transformations during a layout rendering
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

    /**
     * Layouts added by a module through the actionListMailThemes hook are usually declared with the
     * absolute file path documented on Layout::__construct, but Twig only resolves namespaced paths,
     * so such a layout is reported as missing. Convert an absolute path inside the modules folder to
     * the @Modules namespace, which is what FolderThemeScanner already builds for module themes.
     *
     * @param string $layoutPath
     *
     * @return string
     */
    private function getTwigPath(string $layoutPath): string
    {
        if ('' === $this->moduleDirectory || !str_starts_with($layoutPath, $this->moduleDirectory)) {
            return $layoutPath;
        }

        return '@Modules/' . ltrim(substr($layoutPath, strlen($this->moduleDirectory)), '/');
    }
}
