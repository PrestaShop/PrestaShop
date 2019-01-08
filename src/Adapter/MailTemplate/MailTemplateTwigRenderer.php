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

use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateParametersBuilderInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\MailTemplateTransformationInterface;
use Symfony\Component\Templating\EngineInterface;
use Language;

/**
 * MailTemplateTwigRenderer is the basic implementation of MailTemplateRendererInterface
 * using the twig engine.
 */
class MailTemplateTwigRenderer implements MailTemplateRendererInterface
{
    /** @var EngineInterface */
    private $engine;

    /** @var MailTemplateParametersBuilderInterface */
    private $parametersBuilder;

    /**
     * @param EngineInterface $engine
     * @param MailTemplateParametersBuilderInterface $parametersBuilder
     */
    public function __construct(
        EngineInterface $engine,
        MailTemplateParametersBuilderInterface $parametersBuilder
    ) {
        $this->engine = $engine;
        $this->parametersBuilder = $parametersBuilder;
    }

    /**
     * @param MailTemplateInterface $template
     * @param Language $language
     *
     * @return string
     */
    public function render(MailTemplateInterface $template, Language $language)
    {
        $parameters = $this->parametersBuilder->buildParameters($template, $language);

        return $this->engine->render($template->getPath(), $parameters);
    }

    /**
     * @inheritDoc
     */
    public function addTransformation(MailTemplateTransformationInterface $transformer)
    {
        // TODO: Implement addTransformation() method.
    }
}
