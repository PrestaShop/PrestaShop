<?php
class TMFooterlinks extends Module
{
 	protected $maxImageSize = 307200;
	protected $_xml;
	protected $_defaultLanguage;
	protected $_languages;
 	function __construct()
 	{
 	 	$this->name = 'tmfooterlinks';
		$this->tab = 'front_office_features';
 	 	$this->version = '1.5';
	 	parent::__construct();
		$this->page = basename(__FILE__, '.php');
	 	$this->displayName = $this->l('TM Footerlinks');
	 	$this->description = $this->l('Displays block with links in footer');
		
		/* initiate values for translation */
		$this->_defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
		$this->_languages = Language::getLanguages();
		/* put xml in cache */
		$this->_xml = $this->_getXml();
 	}
    function install()
    {
        if (!parent::install() OR !$this->registerHook('footer'))
            return false;
        return true;
    }
	function putContent($xml_data, $key, $field)
	{
/*
	$field = htmlspecialchars($field);
*/
	if ((int)ini_get('magic_quotes_gpc')==1 && ((int)ini_get('magic_quotes_sybase')==0)) {$field=stripslashes($field);}
		if (!$field)
			return 0;
		return ("\n".'		<'.$key.'>'.htmlspecialchars($field, ENT_QUOTES, 'UTF-8').'</'.$key.'>');
/*
		return ("\n".'		<'.$key.'>'.$field = str_replace("&","&amp;",$field).'</'.$key.'>');
*/
	}
 	function getContent()
 	{
        global $cookie;
        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $isoUser = Language::getIsoById(intval($cookie->id_lang));
       	 	
 	 	/* display the module name */
 	 	$this->_html = '<h2>'.$this->displayName.' '.$this->version.'</h2>';
 	 	/* update the editorial xml */
 	 	if (isset($_POST['submitUpdate']))
 	 	{
			// Generate new XML data
 	 	 	$newXml = '<?xml version=\'1.0\' encoding=\'utf-8\' ?>'."\n";
			$newXml .= '<links>'."\n";
			$i = 0;
			foreach ($_POST['link'] as $link)
			{
				$newXml .= '	<link>';
				foreach ($link AS $key => $field)
				{
					if ($line = $this->putContent($newXml, $key, $field))
						$newXml .= $line;
				}
				/* upload the image */
				if (isset($_FILES['link_'.$i.'_img']) AND isset($_FILES['link_'.$i.'_img']['tmp_name']) AND !empty($_FILES['link_'.$i.'_img']['tmp_name']))
				{
					Configuration::set('PS_IMAGE_GENERATION_METHOD', 1);
					if ($error = checkImage($_FILES['link_'.$i.'_img'], $this->maxImageSize))
						$this->_html .= $error;
				elseif (!$tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS') OR !move_uploaded_file($_FILES['link_'.$i.'_img']['tmp_name'], $tmpName))
					return false;
                elseif (!imageResize($tmpName, dirname(__FILE__).'/slides/slide_0'.$i.'.jpg'))
					$this->_html .= $this->displayError($this->l('An error occurred during the image upload.'));
                unlink($tmpName);
				}
				if ($line = $this->putContent($newXml, 'img', 'slides/slide_0'.$i.'.jpg'))
					$newXml .= $line;
				$newXml .= "\n".'	</link>'."\n";
				$i++;
			}
			$newXml .= '</links>'."\n";
			/* write it into the editorial xml file */
			if ($fd = @fopen(dirname(__FILE__).'/links.xml', 'w'))
			{
				if (!@fwrite($fd, $newXml))
					$this->_html .= $this->displayError($this->l('Unable to write to the editor file.'));
				if (!@fclose($fd))
					$this->_html .= $this->displayError($this->l('Can\'t close the editor file.'));
			}
			else
				$this->_html .= $this->displayError($this->l('Unable to update the editor file.<br />Please check the editor file\'s writing permissions.'));
 	 	}
		if (Tools::isSubmit('submitUpdate'))
		{
				$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />'.$this->l('Settings updated').'</div>';
		}
 		/* display the editorial's form */
 	 	$this->_displayForm();
 	 	return $this->_html;
 	}
	static private function getXmlFilename()
	{
		return 'links.xml';
	}
	private function _getXml()
	{
		if (file_exists(dirname(__FILE__).'/'.$this->getXmlFilename()))
		{
			if ($xml = @simplexml_load_file(dirname(__FILE__).'/'.$this->getXmlFilename()))
				return $xml;
		}
		return false;
	}
	public function _getFormItem($i, $last)
	{
		global $cookie;
		$this->_xml = $this->_getXml();
		$isoUser = Language::getIsoById(intval($cookie->id_lang));
		$divLangName = 'texts'.$i;
		$output = '
			<div class="item" id="item'.$i.'" style="position:relative;">
				<h3>'.$this->l('Column #').($i+1).'</h3>
				<input type="hidden" name="item_'.$i.'_item" value="" />';
				
		$output .= '<fieldset style="font-size:1em;">';
		$output .= '<div style="position:absolute;">';
		$output .= $this->displayFlags($this->_languages, $this->_defaultLanguage, $divLangName , 'texts'.$i, true);
		$output .= '</div>';
		
		foreach ($this->_languages as $language)
		{
			
		$output .= '<div id="texts'.$i.'_'.$language['id_lang'].'" style="display:'.($language['id_lang'] == $this->_defaultLanguage ? 'block' : 'none').';">';
		
		$output .= '
				<label>'.$this->l('Column title').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field1_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field1_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';
				
		$output .= '
				<label>'.$this->l('Link #1 text').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field2_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field2_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #1 URL').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field3_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field3_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #2 text').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field4_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field4_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #2 URL').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field5_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field5_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #3 text').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field6_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field6_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #3 URL').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field7_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field7_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #4 text').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field8_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field8_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #4 URL').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field9_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field9_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #5 text').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field10_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field10_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '
				<label>'.$this->l('Link #5 URL').'</label>
				<div class="margin-form" style="padding-left:0">
					<input type="text" name="link['.$i.'][field11_'.$language['id_lang'].']" size="64" value="'.$this->_xml->link[$i]->{'field11_'.$language['id_lang']}.'" />
					<p style="clear: both"></p>
				</div>';

		$output .= '</div>';
		
		}
		$output .= '</fieldset>';
		
		$output .= '
				<div class="clear pspace"></div>
				'.($i >= 0 ? '<!--<a href="javascript:{}" onclick="removeDiv(\'item'.$i.'\')" style="color:#EA2E30"><img src="'._PS_ADMIN_IMG_.'delete.gif" alt="'.$this->l('delete').'" />'.$this->l('Delete this item').'</a>-->' : '').'
			<hr/></div>';
		return $output;
	}
 	private function _displayForm()
 	{
        global $cookie;
        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $isoUser = Language::getIsoById(intval($cookie->id_lang));
 	 	/* xml loading */
 	 	$xml = false;
 	 	if (file_exists(dirname(__FILE__).'/links.xml'))
		  	if (!$xml = @simplexml_load_file(dirname(__FILE__).'/links.xml'))
		  		$this->_html .= $this->displayError($this->l('Your links file is empty.'));
		        $this->_html .= '
		<script type="text/javascript">
		function removeDiv(id)
		{
			$("#"+id).fadeOut("slow");
			$("#"+id).remove();
		}
		function cloneIt(cloneId) {
			var currentDiv = $(".item:last");
			var id = ($(currentDiv).size()) ? $(currentDiv).attr("id").match(/[0-9]/gi) : -1;
			var nextId = parseInt(id) + 1;
			$.get("'._MODULE_DIR_.$this->name.'/ajax.php?id="+nextId, function(data) {
				$("#items").append(data);
			});
			$("#"+cloneId).remove();
		}
		</script>
		<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data">
			<fieldset style="width: 800px;">
        		<legend><img src="'.$this->_path.'logo.gif" alt="" title="" /> '.$this->displayName.'</legend>
					<div id="items">';
						$i = 0;
						foreach ($xml->link as $link)
						{
							$last = ($i == (count($xml->link)-1) ? true : false);
							$this->_html .= $this->_getFormItem($i, $last);
							$i++;
						}
						$this->_html .= '
				</div>
<!--				<a id="clone'.$i.'" href="javascript:cloneIt(\'clone'.$i.'\')" style="color:#488E41"><img src="'._PS_ADMIN_IMG_.'add.gif" alt="'.$this->l('add').'" /><b>'.$this->l('Add a new item').'</b></a>-->';
		$this->_html .= '
				<div class="margin-form clear">
					<div class="clear pspace"></div>
					<div class="margin-form">
						 <input type="submit" name="submitUpdate" value="'.$this->l('Save').'" class="button" />
					</div>
				</div>
					
				</fieldset>
			</form>';
 	}
 	function hookFooter($params)
 	{
        global $cookie;
        /* Languages preliminaries */
        $defaultLanguage = intval(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages();
        $iso = Language::getIsoById($defaultLanguage);
        $isoUser = Language::getIsoById(intval($cookie->id_lang));
 	 	if (file_exists(dirname(__FILE__).'/links.xml'))
 	 		if ($xml = simplexml_load_file(dirname(__FILE__).'/links.xml'))
 	 		{
 	 		 	global $cookie, $smarty;
				$smarty->assign(array(
					'xml' => $xml,
					'field1' => 'field1_'.$cookie->id_lang,
					'field2' => 'field2_'.$cookie->id_lang,
					'field3' => 'field3_'.$cookie->id_lang,
					'field4' => 'field4_'.$cookie->id_lang,
					'field5' => 'field5_'.$cookie->id_lang,
					'field6' => 'field6_'.$cookie->id_lang,
					'field7' => 'field7_'.$cookie->id_lang,
					'field8' => 'field8_'.$cookie->id_lang,
					'field9' => 'field9_'.$cookie->id_lang,
					'field10' => 'field10_'.$cookie->id_lang,
					'field11' => 'field11_'.$cookie->id_lang,
					'this_path' => $this->_path
				));
				return $this->display(__FILE__, 'tmfooterlinks.tpl');
			}
		return false;
 	}
}
?>