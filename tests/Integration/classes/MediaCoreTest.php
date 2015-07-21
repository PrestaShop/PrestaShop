<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Unit\Classes;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Configuration;
use Media;

class MediaCoreTest extends IntegrationTestCase
{
    protected $domain;

    public function testCorrectJQueryNoConflictURL()
    {
        $domain = Configuration::get('PS_SHOP_DOMAIN');
        $result = Media::getJqueryPath('1.11');
        $this->assertEquals(true, in_array('http://'.$domain.__PS_BASE_URI__.'js/jquery/jquery.noConflict.php?version=1.11', $result));
    }

    protected function setUp()
    {
        $this->domain = Configuration::get('PS_SHOP_DOMAIN');
    }

    public function isCssInputsProvider()
    {
        return array(
            array('http://wwww.google.com/images/nav_logo.png', '', 'http://wwww.google.com/images/nav_logo.png', false),
            array('url(http://wwww.google.com/images/nav_logo1.png)', '', 'url(http://wwww.google.com/images/nav_logo1.png)', false),
            array('url("http://wwww.google.com/images/nav_logo2.png")', '', 'url("http://wwww.google.com/images/nav_logo2.png")', false),
            array(' url(\'http://wwww.google.com/images/nav_logo3.png\')', '', 'url(\'http://wwww.google.com/images/nav_logo3.png\')', false),
            array('background: url(http://wwww.google.com/images/nav_logo4.png)', '', 'background:url(http://wwww.google.com/images/nav_logo4.png)', false),
            array('url(https://wwww.google.com/images/nav_logo5.png)', '', 'url(https://wwww.google.com/images/nav_logo5.png)', false),
            array('url(data://wwww.google.com/images/nav_logo6.png)', '', 'url(data://wwww.google.com/images/nav_logo6.png)', false),
            array('url(\'https://wwww.google.com/images/nav_logo7.png\')', '', 'url(\'https://wwww.google.com/images/nav_logo7.png\')', false),
            array('url(\'data://wwww.google.com/images/nav_logo8.png\')', '', 'url(\'data://wwww.google.com/images/nav_logo8.png\')', false),
            array('url("https://wwww.google.com/images/nav_logo9.png")', '', 'url("https://wwww.google.com/images/nav_logo9.png")', false),
            array('url("data://wwww.google.com/images/nav_logo10.png")', '', 'url("data://wwww.google.com/images/nav_logo10.png")', false),
            array('url(//wwww.google.com/images/nav_logo11.png)', '', 'url(//wwww.google.com/images/nav_logo11.png)', false),
            array('url("//wwww.google.com/images/nav_logo12.png")', '', 'url("//wwww.google.com/images/nav_logo12.png")', false),
            array('url(\'//wwww.google.com/images/nav_logo13.png\')', '', 'url(\'//wwww.google.com/images/nav_logo13.png\')', false),
            array('url(http://cdn.server/uri/img/contact-form.png)', '/path/', 'url(http://cdn.server/uri/img/contact-form.png)', false),
            array(' url(../img/contact-form1.png)', '/themes/default-bootstrap/css/contact-form.css', 'url(http://server/themes/default-bootstrap/css/../img/contact-form1.png)', true),
            array(' url(./contact-form2.png)', '/themes/default-bootstrap/css/contact-form.css', 'url(http://server/themes/default-bootstrap/css/./contact-form2.png)', true),
            array('url(/img/contact-form3.png)', '/themes/default-bootstrap/css/contact-form.css', 'url(http://server/img/contact-form3.png)', true),
            array('url(\'../img/contact-form4.png\')', '/themes/default-bootstrap/css/contact-form.css', 'url(\'http://server/themes/default-bootstrap/css/../img/contact-form4.png\')', true),
            array(' url(\'./contact-form5.png\')', '/themes/default-bootstrap/css/contact-form.css', 'url(\'http://server/themes/default-bootstrap/css/./contact-form5.png\')', true),
            array('url(\'/img/contact-form6.png\')', '/themes/default-bootstrap/css/contact-form.css', 'url(\'http://server/img/contact-form6.png\')', true),
            array('url("../img/contact-form7.png")', '/themes/default-bootstrap/css/contact-form.css', 'url("http://server/themes/default-bootstrap/css/../img/contact-form7.png")', true),
            array('url("./contact-form8.png")', '/themes/default-bootstrap/css/contact-form.css', 'url("http://server/themes/default-bootstrap/css/./contact-form8.png")', true),
            array('url("/img/contact-form9.png")', '/themes/default-bootstrap/css/contact-form.css', 'url("http://server/img/contact-form9.png")', true),
        );
    }

    /**
     * @dataProvider isCssInputsProvider
     */
    public function testMinifyCSS($input, $fileuri, $output)
    {
        $output = str_replace('//server/', '//'.$this->domain.'/', $output);
        $return = Media::minifyCSS($input, $fileuri, $import_url);
        $this->assertEquals($output, $return, 'MinifyCSS failed for data input : '.$input.'; Expected : '.$output.'; Returns : '.$return);
    }

    /**
     * @dataProvider isCssInputsProvider
     */
    public function testReplaceByAbsoluteURLPattern($input, $fileuri, $output, $expected)
    {
        $return = preg_match(Media::$pattern_callback, $input, $matches);
        $this->assertEquals((bool)$expected, (bool)$return, 'ReplaceByAbsoluteURLPattern failed for data input : '.$input.(isset($matches[2]) && $matches[2] ? '; Matches : '.$matches[2] : ''));
    }

    public function isJsInputsProvider()
    {
        return array(
            array('<script>test 1</script>', '<script>/* <![CDATA[ */;test 1;/* ]]> */</script>'),
            array('<script type="text/javascript">test 2</script>', '<script type="text/javascript">/* <![CDATA[ */;test 2;/* ]]> */</script>'),
            array('<script type="javascript">test 3</script>', '<script type="javascript">/* <![CDATA[ */;test 3;/* ]]> */</script>'),
            array('<script type= "javascript" nonsense>test 4</script>', '<script type= "javascript" nonsense>/* <![CDATA[ */;test 4;/* ]]> */</script>'),
            array('<script language="JavaScript" type="text/javascript">test 5</script>', '<script language="JavaScript" type="text/javascript">/* <![CDATA[ */;test 5;/* ]]> */</script>'),
            array('<script class="myJS" type="text/javascript">test 6</script>', '<script class="myJS" type="text/javascript">/* <![CDATA[ */;test 6;/* ]]> */</script>'),
            array('<scripttype="text/javascript"> test 7</script>', '<scripttype="text/javascript"> test 7</script>'),
            array('<script type="application/javascript">test 8</script>', '<script type="application/javascript">/* <![CDATA[ */;test 8;/* ]]> */</script>'),
            array('<script type=\'application/javascript\'>test 9</script>', '<script type=\'application/javascript\'>/* <![CDATA[ */;test 9;/* ]]> */</script>'),
            array('<script type=\'text/javascript\'>test 10</script>', '<script type=\'text/javascript\'>/* <![CDATA[ */;test 10;/* ]]> */</script>'),
            array('<script type=\'javascript\'>test 11</script>', '<script type=\'javascript\'>/* <![CDATA[ */;test 11;/* ]]> */</script>'),
            array('<script type="application/ld+json">{"@context": https://schema.org","@type": "Product","name": "[the name of the product]","aggregateRating": {"@type": "AggregateRating","ratingValue": "[rating]","reviewCount": "[number of reviews]"}}</script>', '<script type="application/ld+json">{"@context": https://schema.org","@type": "Product","name": "[the name of the product]","aggregateRating": {"@type": "AggregateRating","ratingValue": "[rating]","reviewCount": "[number of reviews]"}}</script>'),
        );
    }

    /**
     * @dataProvider isJsInputsProvider
     */
    public function testPackJSinHTML($input, $output)
    {
        $return = Media::packJSinHTML($input);
        $this->assertEquals($output, $return, 'packJSinHTML failed for data input='.$input);
    }
}
