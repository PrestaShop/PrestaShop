<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 2018-12-21
 * Time: 14:19
 */

namespace PrestaShopBundle\Service\Mail;


use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Templating\EngineInterface;

class MailTemplateGenerator
{
    /**
     * @var EngineInterface
     */
    private $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }

    public function generateTemplates($theme, $language)
    {

    }
}
