{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{$export_precontent}{foreach from=$export_headers item=header}{$text_delimiter}{$header}{$text_delimiter};{/foreach}

{foreach from=$export_content item=line}
{foreach from=$line item=content}{$text_delimiter}{$content}{$text_delimiter};{/foreach}

{/foreach}
