<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Class CleanHtmlValidator is responsible for validating the html content to prevent from having javascript events
 * or script tags.
 */
final class CleanHtmlValidator extends ConstraintValidator
{
    private const EMBEDDABLE_HTML_PATTERN = '/<[\s]*(i?frame|form|input|embed|object)/ims';

    /**
     * @var bool
     */
    private $allowEmbeddableHtml;

    public function __construct(bool $allowEmbeddableHtml)
    {
        $this->allowEmbeddableHtml = $allowEmbeddableHtml;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof CleanHtml) {
            throw new UnexpectedTypeException($constraint, CleanHtml::class);
        }

        if (!$value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $containsScriptTags = preg_match('/<[\s]*script/ims', $value) || preg_match('/.*script\:/ims', $value);
        $containsJavascriptEvents = preg_match('/(' . $this->getJavascriptEvents() . ')[\s]*=/ims', $value);

        $iframe = !$this->allowEmbeddableHtml && preg_match(self::EMBEDDABLE_HTML_PATTERN, $value);

        // any html attribute starting with "on" (event attributes), as a second layer protection
        $eventAttributeRegex = '/<\s*\w+[^>]*\s(on\w+)=["\'][^"\']*["\']/ims';
        // RLO characters detection
        $rloCharacters = "\xE2\x80\xAE";

        if ($containsScriptTags || $containsJavascriptEvents || $iframe || preg_match($eventAttributeRegex, $value) || str_contains($value, $rloCharacters)) {
            $this->context->buildViolation($constraint->message)
                ->setTranslationDomain('Admin.Notifications.Error')
                ->setParameter('%s', $this->formatValue($value))
                ->addViolation()
            ;
        }
    }

    /**
     * Gets javascript events separated by pipeline which are used in preg match pattern to determine if string
     * contains a javascript event. E.g onchange= is valid call for js event.
     *
     * @return string
     */
    private function getJavascriptEvents()
    {
        $events = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange';
        $events .= '|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave|onerror|onselect|onreset|onabort|ondragdrop|onresize|onactivate|onafterprint|onmoveend';
        $events .= '|onafterupdate|onbeforeactivate|onbeforecopy|onbeforecut|onbeforedeactivate|onbeforeeditfocus|onbeforepaste|onbeforeprint|onbeforeunload|onbeforeupdate|onmove';
        $events .= '|onbounce|oncellchange|oncontextmenu|oncontrolselect|oncopy|oncut|ondataavailable|ondatasetchanged|ondatasetcomplete|ondeactivate|ondrag|ondragend|ondragenter|ondragexit|onmousewheel';
        $events .= '|ondragleave|ondragover|ondragstart|ondrop|onerrorupdate|onfilterchange|onfinish|onfocusin|onfocusout|onhashchange|onhelp|oninput|onlosecapture|onmessage|onmouseup|onmovestart';
        $events .= '|onoffline|ononline|onpaste|onpropertychange|onreadystatechange|onresizeend|onresizestart|onrowenter|onrowexit|onrowsdelete|onrowsinserted|onscroll|onsearch|onselectionchange';
        $events .= '|onselectstart|onstart|onstop|onanimationcancel|onanimationend|onanimationiteration|onanimationstart';
        $events .= '|onpointerover|onpointerenter|onpointerdown|onpointermove|onpointerup|onpointerout|onpointerleave|onpointercancel|ongotpointercapture|onlostpointercapture';
        $events .= '|onpagehide|onpageshow|onautocomplete|onautocompleteerror|oncanplay|oncanplaythrough|onclose|oncuechange|ondurationchange|onemptied|onended|oninvalid|onloadeddata';
        $events .= '|onloadedmetadata|onloadstart|onpause|onplay|onplaying|onpopstate|onprogress|onratechange|onreset|onseeked|onseeking|onshow|onsort|onstalled|onstorage|onsuspend|ontimeupdate';
        $events .= '|ontoggle|onvolumechange|onwaiting';

        return $events;
    }
}
