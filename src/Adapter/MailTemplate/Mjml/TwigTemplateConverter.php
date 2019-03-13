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

namespace PrestaShop\PrestaShop\Adapter\MailTemplate\Mjml;


use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\MailTemplate\Mjml\MjmlConverter;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Templating\EngineInterface;

class TwigTemplateConverter
{
    /** @var EngineInterface */
    private $engine;

    /** @var MjmlConverter */
    private $mjmlConverter;

    /** @var string */
    private $tempDir;

    /** @var Filesystem */
    private $fileSystem;

    /** @var string */
    private $templateContent;

    /**
     * @param TwigEngine $engine
     * @param MjmlConverter $mjmlConverter
     * @param string $tempDir
     */
    public function __construct(
        TwigEngine $engine,
        MjmlConverter $mjmlConverter,
        $tempDir = ''
    ) {
        $this->engine = $engine;
        $this->mjmlConverter = $mjmlConverter;
        $this->tempDir = empty($tempDir) ? sys_get_temp_dir() . '/mjml_twig_converter' : $tempDir;
        $this->fileSystem = new Filesystem();
    }

    public function convertLayoutTemplate($mjmlTemplatePath, $mjmlTheme, $newTheme)
    {
        if (!file_exists($mjmlTemplatePath)) {
            throw new FileNotFoundException(sprintf('Could not find mjml template %s', $mjmlTemplatePath));
        }

        $this->templateContent = file_get_contents($mjmlTemplatePath);

        //Replace theme and include files extensions
        $this->templateContent = preg_replace('#/'.$mjmlTheme.'/#', '/'.$newTheme.'/', $this->templateContent);
        $this->templateContent = preg_replace('#mjml\.twig#', 'html.twig', $this->templateContent);

        //This block doesn't need mj-raw as it is in a mj-title tag so we temporarily reformat it
        $this->templateContent = preg_replace('#{% block title %}(.*){% endblock %}#', '%%title \1%%', $this->templateContent);
        //All twig tag must be included in mj-raw tags
        $this->templateContent = preg_replace('#{% (.*?) %}#', '<mj-raw>%% \1 %%</mj-raw>', $this->templateContent);

        $twigLayout = $this->mjmlConverter->convert($this->templateContent);

        //Transform back twig blocks
        $twigLayout = preg_replace('#%% (.*?) %%#', '{% \1 %}', $twigLayout);

        //Add EOL and indentation for clarity
        $twigLayout = preg_replace('/{% block (.*?) %}/', "\n                  {% block \\1 %}\n", $twigLayout);
        $twigLayout = preg_replace('/{% include (.*?) %}/', "                      {% include \\1 %}\n", $twigLayout);
        $twigLayout = preg_replace('/{% endblock %}/', "                  {% endblock %}\n", $twigLayout);

        //Transform back title block
        $twigLayout = preg_replace('#%%title (.*)%%#', '{% block title %}\1{% endblock %}', $twigLayout);
var_dump($twigLayout);
        //Add the styles block in the header
        $dom = new \DOMDocument();
        $dom->loadHTML($twigLayout);
        $blockNode = $dom->createTextNode("    {% block styles %}\n    {% endblock %}\n");
        /** @var \DOMElement $head */
        $head = $dom->getElementsByTagName('head')->item(0);
        $head->appendChild($blockNode);

        return $dom->saveHTML();
    }

    public function convertComponentTemplate($mjmlTemplatePath, $mjmlTheme)
    {
        if (!file_exists($mjmlTemplatePath)) {
            throw new FileNotFoundException(sprintf('Could not find mjml template %s', $mjmlTemplatePath));
        }

        $this->templateContent = file_get_contents($mjmlTemplatePath);
        $templateName = basename($mjmlTemplatePath);
        $this->templateContent = "{% extends '@MailThemes/".$mjmlTheme."/components/layout.mjml.twig' %}

{% block content %}
$this->templateContent
{% endblock %}

{% block header %}{% endblock %}
{% block footer %}{% endblock %}
";

        $convertedLayout = $this->convertLayout($templateName);

        return $convertedLayout['content'];
    }

    public function convertChildTemplate($mjmlTemplatePath, $newTheme)
    {
        if (!file_exists($mjmlTemplatePath)) {
            throw new FileNotFoundException(sprintf('Could not find mjml template %s', $mjmlTemplatePath));
        }

        $this->templateContent = file_get_contents($mjmlTemplatePath);
        $templateName = basename($mjmlTemplatePath);

        $this->templateContent .= "
        {% block header %}{% endblock %}
        {% block footer %}{% endblock %}
";

        //Replace parent layout with conversion layout
        $mjmlLayout = $this->getParentLayout();
        $twigLayout = $this->convertTwigLayoutPath($mjmlLayout, $newTheme);
        $layoutTile = $this->getLayoutTitle();

        $convertedLayout = $this->convertLayout($templateName);
        $layoutContent = $convertedLayout['content'];
        $layoutStyles = $convertedLayout['styles'];

        return "{% extends $twigLayout %}
        
{% block title %}$layoutTile{% endblock %}

{% block content %}
$layoutContent
{% endblock %}

{% block styles %}
{{ parent() }}
$layoutStyles
{% endblock %}
";
    }

    /**
     * @param string $templateName
     *
     * @return array
     * @throws \Twig\Error\Error
     */
    private function convertLayout($templateName)
    {
        //Print the conversion layout in a file and renders it (Twig needs a file as input)
        $conversionTemplatePath = $this->tempDir.'/'.$templateName;
        if (!is_dir($this->tempDir)) {
            $this->fileSystem->mkdir($this->tempDir);
        }

        file_put_contents($conversionTemplatePath, $this->templateContent);
        $renderedLayout = $this->engine->render($conversionTemplatePath);
        file_put_contents($conversionTemplatePath.'.mjml', $renderedLayout);

        //Convert the conversion template (MJML code is compiled and the template contains mj-raw tags to include the twig tags)
        $convertedTemplate = $this->mjmlConverter->convert($renderedLayout);

        //MJML returns a full html template, get only the body content
        $crawler = new Crawler($convertedTemplate);
        /** @var \DOMElement $body */
        $body = $crawler->filter('.wrapper-container table tr td')->getNode(0);
        $innerHtml = '';
        /** @var \DOMElement $childNode */
        foreach ($body->childNodes as $childNode) {
            $innerHtml .= $childNode->ownerDocument->saveHTML($childNode);
        }

        //Add a few EOL for clarity
        $innerHtml = preg_replace('/{% extends (.*?) %}/', "{% extends \\1 %}\n\n", $innerHtml);
        $innerHtml = preg_replace('/{% block (.*?) %}/', "{% block \\1 %}\n", $innerHtml);
        $innerHtml = preg_replace('/{% endblock %}/', "\n{% endblock %}\n\n", $innerHtml);
        $innerHtml = trim($innerHtml)."\n";

        //Each converted template has its own style rules, so we need to extract them as well
        $crawler = new Crawler($convertedTemplate);
        /** @var Crawler $stylesCrawler */
        $stylesCrawler = $crawler->filter('head style');
        $templateStyles = '';
        /** @var \DOMElement $childNode */
        foreach ($stylesCrawler as $childNode) {
            $templateStyles .= $childNode->ownerDocument->saveHTML($childNode);
        }
        $templateStyles = trim($templateStyles)."\n";

        return [
            'content' => $innerHtml,
            'styles' => $templateStyles,
        ];
    }

    /**
     * @return string
     */
    private function getParentLayout()
    {
        preg_match('/{% extends (.*?) %}/', $this->templateContent, $matches);
        if (count($matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    private function getLayoutTheme($layoutPath)
    {
        preg_match('#@MailThemes/(.*?)/#', $layoutPath, $matches);
        if (count($matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @return string
     */
    private function getLayoutTitle()
    {
        preg_match('/{% block title %}(.*){% endblock %}/', $this->templateContent, $matches);
        if (count($matches)) {
            return $matches[1];
        }

        return '';
    }

    /**
     * @param string $layoutPath
     * @param string $newTheme
     *
     * @return string|string[]|null
     */
    private function convertTwigLayoutPath($layoutPath, $newTheme)
    {
        $mjmlTheme = $this->getLayoutTheme($layoutPath);
        $twigLayoutPath = preg_replace('#@MailThemes/'.$mjmlTheme.'/#', '@MailThemes/'.$newTheme.'/', $layoutPath);
        $twigLayoutPath = preg_replace('#mjml.twig#', 'html.twig', $twigLayoutPath);

        return $twigLayoutPath;
    }

}
