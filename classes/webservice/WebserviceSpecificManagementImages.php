<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class WebserviceSpecificManagementImagesCore implements WebserviceSpecificManagementInterface
{
	protected $objOutput;
	protected $output;
	protected $wsObject;

	/**
	 * @var string The extension of the image to display
	 */
	protected $imgExtension = 'jpg';

	/**
	 * @var array The type of images (general, categories, manufacturers, suppliers, stores...)
	 */
	protected $imageTypes = array(
		'general' => array(
			'header' => array(),
			'mail' => array(),
			'invoice' => array(),
			'store_icon' => array(),
		),
		'products' => array(),
		'categories' => array(),
		'manufacturers' => array(),
		'suppliers' => array(),
		'stores' => array()
	);

	/**
	 * @var string The image type (product, category, general,...)
	 */
	protected $imageType = null;

	/**
	 * @var int The maximum size supported when uploading images, in bytes
	 */
	protected $imgMaxUploadSize = 3000000;

	/**
	 * @var array The list of supported mime types
	 */
	protected $acceptedImgMimeTypes = array('image/gif', 'image/jpg', 'image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');

	/**
	 * @var string The product image declination id
	 */
	protected $productImageDeclinationId = null;

	/**
	 * @var boolean If the current image management has to manage a "default" image (i.e. "No product available")
	 */
	protected $defaultImage = false;

	/**
	 * @var string The file path of the image to display. If not null, the image will be displayed, even if the XML output was not empty
	 */
	public $imgToDisplay = null;
	public $imageResource = null;

	// ------------------------------------------------
	// GETTERS & SETTERS
	// ------------------------------------------------

	public function setObjectOutput(WebserviceOutputBuilderCore $obj)
	{
		$this->objOutput = $obj;
		return $this;
	}

	public function getObjectOutput()
	{
		return $this->objOutput;
	}

	public function setWsObject(WebserviceRequestCore $obj)
	{
		$this->wsObject = $obj;
		return $this;
	}

	public function getWsObject()
	{
		return $this->wsObject;
	}

	/*
	* This method need $this->imgToDisplay to be set if output don't needs to be XML
	*/
	public function getContent()
	{
		if ($this->output != '')
			return $this->objOutput->getObjectRender()->overrideContent($this->output);
		// display image content if needed
		else if ($this->imgToDisplay)
		{
			$imageResource = false;
			$types = array('jpg' => array('function' => 'imagecreatefromjpeg', 'Content-Type' => 'image/jpeg'),
							'gif' => array('function' => 'imagecreatefromgif', 'Content-Type' => 'image/gif')
							);

			if (array_key_exists($this->imgExtension, $types))
				$imageResource = @$types[$this->imgExtension]['function']($this->imgToDisplay);

			if(!$imageResource)
				throw new WebserviceException(sprintf('Unable to load the image "%s"', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $this->imgToDisplay)), array(47, 500));
			else
			{
				if (array_key_exists($this->imgExtension, $types))
					$this->objOutput->setHeaderParams('Content-Type', $types[$this->imgExtension]['Content-Type']);
				return file_get_contents($this->imgToDisplay);
			}
		}
	}

	public function manage()
	{
		$this->manageImages();
		return $this->wsObject->getOutputEnabled();
	}

	/**
	 * Management of images URL segment
	 *
	 * @return boolean
	 */
	protected function manageImages()
	{
		/*
		 * available cases api/... :
		 *
		 *   images ("types_list") (N-1)
		 *   	GET    (xml)
		 *   images/general ("general_list") (N-2)
		 *   	GET    (xml)
		 *   images/general/[header,+] ("general") (N-3)
		 *   	GET    (bin)
		 *   	PUT    (bin)
		 *
		 *
		 *   images/[categories,+] ("normal_list") (N-2) ([categories,+] = categories, manufacturers, ...)
		 *   	GET    (xml)
		 *   images/[categories,+]/[1,+] ("normal") (N-3)
		 *   	GET    (bin)
		 *   	PUT    (bin)
		 *   	DELETE
		 *   	POST   (bin) (if image does not exists)
		 *   images/[categories,+]/[1,+]/[small,+] ("normal_resized") (N-4)
		 *   	GET    (bin)
		 *   images/[categories,+]/default ("display_list_of_langs") (N-3)
		 *   	GET    (xml)
		 *   images/[categories,+]/default/[en,+] ("normal_default_i18n")  (N-4)
		 *   	GET    (bin)
		 *   	POST   (bin) (if image does not exists)
		 *      PUT    (bin)
		 *      DELETE
		 *   images/[categories,+]/default/[en,+]/[small,+] ("normal_default_i18n_resized")  (N-5)
		 *   	GET    (bin)
		 *
		 *   images/product ("product_list")  (N-2)
		 *   	GET    (xml) (list of image)
		 *   images/product/[1,+] ("product_description")  (N-3)
		 *   	GET    (xml) (legend, declinations, xlink to images/product/[1,+]/bin)
		 *   images/product/[1,+]/bin ("product_bin")  (N-4)
		 *   	GET    (bin)
		 *      POST   (bin) (if image does not exists)
		 *   images/product/[1,+]/[1,+] ("product_declination")  (N-4)
		 *   	GET    (bin)
		 *   	POST   (xml) (legend)
		 *   	PUT    (xml) (legend)
		 *      DELETE
		 *   images/product/[1,+]/[1,+]/bin ("product_declination_bin") (N-5)
		 *   	POST   (bin) (if image does not exists)
		 *   	GET    (bin)
		 *   	PUT    (bin)
		 *   images/product/[1,+]/[1,+]/[small,+] ("product_declination_resized") (N-5)
		 *   	GET    (bin)
		 *   images/product/default ("product_default") (N-3)
		 *   	GET    (bin)
		 *   images/product/default/[en,+] ("product_default_i18n") (N-4)
		 *   	GET    (bin)
		 *      POST   (bin)
		 *      PUT   (bin)
		 *      DELETE
		 *   images/product/default/[en,+]/[small,+] ("product_default_i18n_resized") (N-5)
		 * 		GET    (bin)
		 *
		 */


		/* Declinated
		 *ok    GET    (bin)
		 *ok images/product ("product_list")  (N-2)
		 *ok	GET    (xml) (list of image)
		 *ok images/product/[1,+] ("product_description")  (N-3)
		 *   	GET    (xml) (legend, declinations, xlink to images/product/[1,+]/bin)
		 *ok images/product/[1,+]/bin ("product_bin")  (N-4)
		 *ok 	GET    (bin)
		 *      POST   (bin) (if image does not exists)
		 *ok images/product/[1,+]/[1,+] ("product_declination")  (N-4)
		 *ok 	GET    (bin)
		 *   	POST   (xml) (legend)
		 *   	PUT    (xml) (legend)
		 *      DELETE
		 *ok images/product/[1,+]/[1,+]/bin ("product_declination_bin") (N-5)
		 *   	POST   (bin) (if image does not exists)
		 *ok 	GET    (bin)
		 *   	PUT    (bin)
		 *   images/product/[1,+]/[1,+]/[small,+] ("product_declination_resized") (N-5)
		 *ok 	GET    (bin)
		 *ok images/product/default ("product_default") (N-3)
		 *ok 	GET    (bin)
		 *ok images/product/default/[en,+] ("product_default_i18n") (N-4)
		 *ok 	GET    (bin)
		 *      POST   (bin)
		 *      PUT   (bin)
		 *      DELETE
		 *ok images/product/default/[en,+]/[small,+] ("product_default_i18n_resized") (N-5)
		 *ok	GET    (bin)
		 *
		 * */


		// Pre configuration...
		if(isset($this->wsObject->urlSegment))
			for ($i = 1; $i < 6; $i++)
				if (count($this->wsObject->urlSegment) == $i)
					$this->wsObject->urlSegment[$i] = '';

		$this->imageType = $this->wsObject->urlSegment[1];

		switch ($this->wsObject->urlSegment[1])
		{
			// general images management : like header's logo, invoice logo, etc...
			case 'general':
				return $this->manageGeneralImages();
				break;
			// normal images management : like the most entity images (categories, manufacturers..)...
			case 'categories':
			case 'manufacturers':
			case 'suppliers':
			case 'stores':
				switch ($this->wsObject->urlSegment[1])
				{
					case 'categories':
						$directory = _PS_CAT_IMG_DIR_;
						break;
					case 'manufacturers':
						$directory = _PS_MANU_IMG_DIR_;
						break;
					case 'suppliers':
						$directory = _PS_SUPP_IMG_DIR_;
						break;
					case 'stores':
						$directory = _PS_STORE_IMG_DIR_;
						break;
				}
				return $this->manageDeclinatedImages($directory);
				break;

			// product image management : many image for one entity (product)
			case 'products':
				return $this->manageProductImages();
				break;

			// images root node management : many image for one entity (product)
			case '':
				$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image_types', array());
				foreach (array_keys($this->imageTypes) as $imageTypeName)
				{
					$more_attr = array(
						'xlink_resource' => $this->wsObject->wsUrl.$this->wsObject->urlSegment[0].'/'.$imageTypeName,
						'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true',
						'upload_allowed_mimetypes' => implode(', ', $this->acceptedImgMimeTypes)
					);
					$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($imageTypeName, array(), $more_attr, false);
				}
				$this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image_types', array());
				return true;
				break;

			default:
				$exception = new WebserviceException(sprintf('Image of type "%s" does not exists', $this->wsObject->urlSegment[1]), array(48, 400));
				throw $exception->setDidYouMean($this->wsObject->urlSegment[1], array_keys($this->imageTypes));
		}
	}
/**
	 * Management of general images
	 *
	 * @return boolean
	 */
	protected function manageGeneralImages()
	{
		$path = '';
		$alternative_path = '';
		switch ($this->wsObject->urlSegment[2])
		{
			// Set the image path on display in relation to the header image
			case 'header':
				if (in_array($this->wsObject->method, array('GET','HEAD','PUT')))
					$path = _PS_IMG_DIR_.'logo.jpg';
				else
					throw new WebserviceException('This method is not allowed with general image resources.', array(49, 405));
				break;

			// Set the image path on display in relation to the mail image
			case 'mail':
				if (in_array($this->wsObject->method, array('GET','HEAD','PUT')))
				{
					$path = _PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL');
					$alternative_path = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
				}
				else
					throw new WebserviceException('This method is not allowed with general image resources.', array(50, 405));
				break;

			// Set the image path on display in relation to the invoice image
			case 'invoice':
				if (in_array($this->wsObject->method, array('GET','HEAD','PUT')))
				{
					$path = _PS_IMG_DIR_.Configuration::get('PS_LOGO_INVOICE');
					$alternative_path = _PS_IMG_DIR_.Configuration::get('PS_LOGO');
				}
				else
					throw new WebserviceException('This method is not allowed with general image resources.', array(51, 405));
				break;

			// Set the image path on display in relation to the icon store image
			case 'store_icon':
				if (in_array($this->wsObject->method, array('GET','HEAD','PUT')))
				{
					$path = _PS_IMG_DIR_.Configuration::get('PS_STORES_ICON');
					$this->imgExtension = 'gif';
				}
				else
					throw new WebserviceException('This method is not allowed with general image resources.', array(52, 405));
				break;

			// List the general image types
			case '':
				$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('general_image_types', array());
				foreach (array_keys($this->imageTypes['general']) as $generalImageTypeName)
				{
					$more_attr = array(
						'xlink_resource' => $this->wsObject->wsUrl.$this->wsObject->urlSegment[0].'/'.$this->wsObject->urlSegment[1].'/'.$generalImageTypeName,
						'get' => 'true', 'put' => 'true', 'post' => 'false', 'delete' => 'false', 'head' => 'true',
						'upload_allowed_mimetypes' => implode(', ', $this->acceptedImgMimeTypes)
					);
					$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader($generalImageTypeName, array(), $more_attr, false);
				}
				$this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('general_image_types', array());
				return true;
				break;

			// If the image type does not exist...
			default:
				$exception = new WebserviceException(sprintf('General image of type "%s" does not exists', $this->wsObject->urlSegment[2]), array(53, 400));
				throw $exception->setDidYouMean($this->wsObject->urlSegment[2], array_keys($this->imageTypes['general']));
		}
		// The general image type is valid, now we try to do action in relation to the method
		switch($this->wsObject->method)
		{
			case 'GET':
			case 'HEAD':
				$this->imgToDisplay = ($path != '' && file_exists($path)) ? $path : $alternative_path;
				return true;
				break;
			case 'PUT':

				if ($this->writePostedImageOnDisk($path, null, null))
				{
					if ($this->wsObject->urlSegment[2] == 'header')
					{
						list($width, $height, $type, $attr) = getimagesize(_PS_IMG_DIR_.'logo.jpg');
						Configuration::updateValue('SHOP_LOGO_WIDTH', (int)round($width));
						Configuration::updateValue('SHOP_LOGO_HEIGHT', (int)round($height));
					}
					$this->imgToDisplay = $path;
					return true;
				}
				else
					throw new WebserviceException('Error while copying image to the directory', array(54, 400));
				break;
		}
	}

	protected function manageDefaultDeclinatedImages($directory, $normal_image_sizes)
	{
		$this->defaultImage = true;
		// Get the language iso code list
		$langList = Language::getIsoIds(true);
		$langs = array();
		$defaultLang = Configuration::get('PS_LANG_DEFAULT');
		foreach ($langList as $lang)
		{
			if ($lang['id_lang'] == $defaultLang)
				$defaultLang = $lang['iso_code'];
			$langs[] = $lang['iso_code'];
		}

		// Display list of languages
		if($this->wsObject->urlSegment[3] == '' && $this->wsObject->method == 'GET')
		{
			$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('languages', array());
			foreach ($langList as $lang)
			{
				$more_attr = array(
					'xlink_resource' => $this->wsObject->wsUrl.'images/'.$this->imageType.'/default/'.$lang['iso_code'],
					'get' => 'true', 'put' => 'true', 'post' => 'true', 'delete' => 'true', 'head' => 'true',
					'upload_allowed_mimetypes' => implode(', ', $this->acceptedImgMimeTypes),
					'iso'=>$lang['iso_code']
				);
				$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('language', array(), $more_attr, false);
			}

			$this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('languages', array());
			return true;
		}
		else
		{
			$lang_iso = $this->wsObject->urlSegment[3];
			$image_size = $this->wsObject->urlSegment[4];
			if ($image_size != '')
				$filename = $directory.$lang_iso.'-default-'.$image_size.'.jpg';
			else
				$filename = $directory.$lang_iso.'.jpg';
			$filename_exists = file_exists($filename);
			return $this->manageDeclinatedImagesCRUD($filename_exists, $filename, $normal_image_sizes, $directory);// @todo : [feature] @see todo#1
		}
	}

	protected function manageListDeclinatedImages($directory, $normal_image_sizes)
	{
		// Check if method is allowed
		if ($this->wsObject->method != 'GET')
			throw new WebserviceException('This method is not allowed for listing category images.', array(55, 405));

		$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image_types', array());
		foreach ($normal_image_sizes as $image_size)
			$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image_type', array(), array('id' => $image_size['id_image_type'], 'name' => $image_size['name'], 'xlink_resource'=>$this->wsObject->wsUrl.'image_types/'.$image_size['id_image_type']), false);
		$this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image_types', array());
		$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('images', array());

		if ($this->imageType == 'products')
		{
			$ids = array();
			$images = Image::getAllImages();
			foreach ($images as $image)
				$ids[] = $image['id_product'];
			$ids = array_unique($ids, SORT_NUMERIC);
			asort($ids);
			foreach ($ids as $id)
				$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$id), false);
		}
		else
		{
		$nodes = scandir($directory);
		foreach ($nodes as $node)
			{
			// avoid too much preg_match...
			if ($node != '.' && $node != '..' && $node != '.svn')
			{
					if ($this->imageType != 'products')
				{
					preg_match('/^(\d+)\.jpg*$/Ui', $node, $matches);
					if (isset($matches[1]))
					{
						$id = $matches[1];
						$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id' => $id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$id), false);
					}
				}
			}
			}
		}
		$this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('images', array());
		return true;
	}

	protected function manageEntityDeclinatedImages($directory, $normal_image_sizes)
	{
		$normal_image_size_names = array();
		foreach ($normal_image_sizes as $normal_image_size)
			$normal_image_size_names[] = $normal_image_size['name'];
		// If id is detected
		$object_id = $this->wsObject->urlSegment[2];
		if (!Validate::isUnsignedId($object_id))
			throw new WebserviceException('The image id is invalid. Please set a valid id or the "default" value', array(60, 400));

		// For the product case
		if ($this->imageType == 'products')
		{
			// Get available image ids
			$available_image_ids = array();

			// New Behavior
			$languages = Language::getLanguages();
			foreach ($languages as $language)
				foreach (Image::getImages($language['id_lang'], $object_id) as $image)
					$available_image_ids[] = $image['id_image'];


			// If an image id is specified
			if ($this->wsObject->urlSegment[3] != '')
			{
				if ($this->wsObject->urlSegment[3] == 'bin')
				{
					$currentProduct = new Product($object_id);
					$this->wsObject->urlSegment[3] = $currentProduct->getCoverWs();
				}
				if (!Validate::isUnsignedId($object_id) || !in_array($this->wsObject->urlSegment[3], $available_image_ids))
					throw new WebserviceException('This image id does not exist', array(57, 400));
				else
				{

					// Check for new image system
					$image_id = $this->wsObject->urlSegment[3];
					$path = implode('/', str_split((string)$image_id));
					$image_size = $this->wsObject->urlSegment[4];

					if (file_exists($directory.$path.'/'.$image_id.(strlen($this->wsObject->urlSegment[4]) > 0 ? '-'.$this->wsObject->urlSegment[4] : '').'.jpg'))
					{
						$filename = $directory.$path.'/'.$image_id.(strlen($this->wsObject->urlSegment[4]) > 0 ? '-'.$this->wsObject->urlSegment[4] : '').'.jpg';
						$orig_filename = $directory.$path.'/'.$image_id.'.jpg';
					}
					else // else old system or not exists
					{
					$orig_filename = $directory.$object_id.'-'.$image_id.'.jpg';
					$filename = $directory.$object_id.'-'.$image_id.'-'.$image_size.'.jpg';
				}
			}
			}
			// display the list of declinated images
			else if ($this->wsObject->method == 'GET' || $this->wsObject->method == 'HEAD')
			{
				if ($available_image_ids)
				{
					$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('image', array(), array('id'=>$object_id));
					foreach ($available_image_ids as $available_image_id)
						$this->output .= $this->objOutput->getObjectRender()->renderNodeHeader('declination', array(), array('id'=>$available_image_id, 'xlink_resource'=>$this->wsObject->wsUrl.'images/'.$this->imageType.'/'.$object_id.'/'.$available_image_id), false);
					$this->output .= $this->objOutput->getObjectRender()->renderNodeFooter('image', array());
				}
				else
				{
					$this->objOutput->setStatus(404);
					$this->wsObject->setOutputEnabled(false);
				}
			}

		}
		// for all other cases
		else
		{
			$orig_filename = $directory.$object_id.'.jpg';
			$image_size = $this->wsObject->urlSegment[3];
			$filename = $directory.$object_id.'-'.$image_size.'.jpg';
		}


		// in case of declinated images list of a product is get
		if ($this->output != '')
			return true;

		// If a size was given try to display it
		elseif (isset($image_size) && $image_size != '')
		{

			// Check the given size
			if ($this->imageType == 'products' && $image_size == 'bin')
				$filename = $directory.$object_id.'-'.$image_id.'.jpg';
			elseif (!in_array($image_size, $normal_image_size_names))
			{
				$exception = new WebserviceException('This image size does not exist', array(58, 400));
				throw $exception->setDidYouMean($image_size, $normal_image_size_names);
			}
			if (!file_exists($filename))
				throw new WebserviceException('This image does not exist on disk', array(59, 500));

			// Display the resized specific image
			$this->imgToDisplay = $filename;
			return true;
		}
		// Management of the original image (GET, PUT, POST, DELETE)
		elseif (isset($orig_filename))
		{
			$orig_filename_exists = file_exists($orig_filename);
			return $this->manageDeclinatedImagesCRUD($orig_filename_exists, $orig_filename, $normal_image_sizes, $directory);
		}
		else
		{
			return $this->manageDeclinatedImagesCRUD(false, '', $normal_image_sizes, $directory);
	}
	}

	/**
	 * Management of normal images (as categories, suppliers, manufacturers and stores)
	 *
	 * @param string $directory the file path of the root of the images folder type
	 * @return boolean
	 */
	protected function manageDeclinatedImages($directory)
	{
		// Get available image sizes for the current image type
		$normal_image_sizes = ImageType::getImagesTypes($this->imageType);
		switch ($this->wsObject->urlSegment[2])
		{
			// Match the default images
			case 'default':
				return $this->manageDefaultDeclinatedImages($directory, $normal_image_sizes);
				break;
			// Display the list of images
			case '':
				return $this->manageListDeclinatedImages($directory, $normal_image_sizes);
				break;
			default:
				return $this->manageEntityDeclinatedImages($directory, $normal_image_sizes);
				break;
		}

	}

	protected function manageProductImages()
	{
		$this->manageDeclinatedImages(_PS_PROD_IMG_DIR_);
	}

	/**
	 * Management of normal images CRUD
	 *
	 * @param boolean $filename_exists if the filename exists
	 * @param string $filename the image path
	 * @param array $imageSizes The
	 * @param string $directory
	 * @return boolean
	 */
	protected function manageDeclinatedImagesCRUD($filename_exists, $filename, $imageSizes, $directory)
	{
		switch ($this->wsObject->method)
		{
			// Display the image
			case 'GET':
			case 'HEAD':
				if ($filename_exists)
					$this->imgToDisplay = $filename;
				else
					throw new WebserviceException('This image does not exist on disk', array(61, 500));
				break;
			// Modify the image
			case 'PUT':
				if ($filename_exists)
					if ($this->writePostedImageOnDisk($filename, null, null, $imageSizes, $directory))
					{
						$this->imgToDisplay = $filename;
						return true;
					}
					else
						throw new WebserviceException('Unable to save this image.', array(62, 500));
				else
					throw new WebserviceException('This image does not exist on disk', array(63, 500));
				break;
			// Delete the image
			case 'DELETE':
				if ($filename_exists)
				{
					// Delete products image in DB
					if ($this->imageType == 'products')
					{
						$image = new Image((int)$this->wsObject->urlSegment[3]);
						return $image->delete();
					}
					else
					return $this->deleteImageOnDisk($filename, $imageSizes, $directory);
				}
				else
					throw new WebserviceException('This image does not exist on disk', array(64, 500));
				break;
			// Add the image
			case 'POST':
				if ($filename_exists)
					throw new WebserviceException('This image already exists. To modify it, please use the PUT method', array(65, 400));
				else
				{
					if ($this->writePostedImageOnDisk($filename, null, null, $imageSizes, $directory))
						return true;
					else
						throw new WebserviceException('Unable to save this image', array(66, 500));
				}
				break;
			default :
				throw new WebserviceException('This method is not allowed', array(67, 405));
		}
	}

	/**
	 * 	Delete the image on disk
	 *
	 * @param string $filePath the image file path
	 * @param array $imageTypes The differents sizes
	 * @param string $parentPath The parent path
	 * @return boolean
	 */
	protected function deleteImageOnDisk($filePath, $imageTypes = null, $parentPath = null)
	{
		$this->wsObject->setOutputEnabled(false);
		if (file_exists($filePath))
		{
			// delete image on disk
			@unlink($filePath);
			// Delete declinated image if needed
			if ($imageTypes)
			{
				foreach ($imageTypes as $imageType)
				{
					if ($this->defaultImage) // @todo products images too !!
						$declination_path = $parentPath.$this->wsObject->urlSegment[3].'-default-'.$imageType['name'].'.jpg';
					else
						$declination_path = $parentPath.$this->wsObject->urlSegment[2].'-'.$imageType['name'].'.jpg';
					if (!@unlink($declination_path))
					{
						$this->objOutput->setStatus(204);
						return false;
					}
				}
			}
			return true;
		}
		else
		{
			$this->objOutput->setStatus(204);
			return false;
		}
	}

	/**
	 * Write the image on disk
	 *
	 * @param string $basePath
	 * @param string $newPath
	 * @param int $destWidth
	 * @param int $destHeight
	 * @param array $imageTypes
	 * @param string $parentPath
	 * @return string
	 */
	protected function writeImageOnDisk($basePath, $newPath, $destWidth = null, $destHeight = null, $imageTypes = null, $parentPath = null)
	{
		list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($basePath);
		if (!$sourceWidth)
			throw new WebserviceException('Image width was null', array(68, 400));
		if ($destWidth == null) $destWidth = $sourceWidth;
		if ($destHeight == null) $destHeight = $sourceHeight;
		switch ($type)
		{
			case 1:
				$sourceImage = imagecreatefromgif($basePath);
				break;
			case 3:
				$sourceImage = imagecreatefrompng($basePath);
				break;
			case 2:
			default:
				$sourceImage = imagecreatefromjpeg($basePath);
				break;
		}

		$widthDiff = $destWidth / $sourceWidth;
		$heightDiff = $destHeight / $sourceHeight;

		if ($widthDiff > 1 && $heightDiff > 1)
		{
			$nextWidth = $sourceWidth;
			$nextHeight = $sourceHeight;
		}
		else
		{
			if ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 2 || ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 && $widthDiff > $heightDiff))
			{
				$nextHeight = $destHeight;
				$nextWidth = (int)(($sourceWidth * $nextHeight) / $sourceHeight);
				$destWidth = ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destWidth : $nextWidth);
			}
			else
			{
				$nextWidth = $destWidth;
				$nextHeight = (int)($sourceHeight * $destWidth / $sourceWidth);
				$destHeight = ((int)(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destHeight : $nextHeight);
			}
		}

		$borderWidth = (int)(($destWidth - $nextWidth) / 2);
		$borderHeight = (int)(($destHeight - $nextHeight) / 2);

		// Build the image
		if (
			!($destImage = imagecreatetruecolor($destWidth, $destHeight)) ||
			!($white = imagecolorallocate($destImage, 255, 255, 255)) ||
			!imagefill($destImage, 0, 0, $white) ||
			!imagecopyresampled($destImage, $sourceImage, $borderWidth, $borderHeight, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight) ||
			!imagecolortransparent($destImage, $white)
		)
			throw new WebserviceException(sprintf('Unable to build the image "%s".', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $newPath)), array(69, 500));

		// Write it on disk
		$imaged = false;
		switch ($this->imgExtension)
		{
			case 'gif':
				$imaged = imagegif($destImage, $newPath);
				break;
			case 'png':
				$imaged = imagepng($destImage, $newPath, 7);
				break;
			case 'jpeg':
			default:
				$imaged = imagejpeg($destImage, $newPath, 90);
				break;
		}
		imagedestroy($destImage);
		if (!$imaged)
			throw new WebserviceException(sprintf('Unable to write the image "%s".', str_replace(_PS_ROOT_DIR_, '[SHOP_ROOT_DIR]', $newPath)), array(70, 500));

		// Write image declinations if present
		if ($imageTypes)
		{
			foreach ($imageTypes as $imageType)
			{
				if ($this->defaultImage)
					$declination_path = $parentPath.$this->wsObject->urlSegment[3].'-default-'.$imageType['name'].'.jpg';
				else
				{
					if ($this->imageType == 'products')
					{
						$declination_path = $parentPath.$this->wsObject->urlSegment[2].'-'.$this->productImageDeclinationId.'-'.$imageType['name'].'.jpg';
					}
					else
						$declination_path = $parentPath.$this->wsObject->urlSegment[2].'-'.$imageType['name'].'.jpg';
				}
				if (!$this->writeImageOnDisk($basePath, $declination_path, $imageType['width'], $imageType['height']))
					throw new WebserviceException(sprintf('Unable to save the declination "%s" of this image.', $imageType['name']), array(71, 500));
			}
		}
		return $newPath;
	}

	/**
	 * Write the posted image on disk
	 *
	 * @param string $sreceptionPath
	 * @param int $destWidth
	 * @param int $destHeight
	 * @param array $imageTypes
	 * @param string $parentPath
	 * @return boolean
	 */
	protected function writePostedImageOnDisk($receptionPath, $destWidth = null, $destHeight = null, $imageTypes = null, $parentPath = null)
	{
		if ($this->wsObject->method == 'PUT')
		{
			if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'])
			{
				$file = $_FILES['image'];
				if ($file['size'] > $this->imgMaxUploadSize)
					throw new WebserviceException(sprintf('The image size is too large (maximum allowed is %d KB)', ($this->imgMaxUploadSize/1000)), array(72, 400));
				// Get mime content type
				$mime_type = false;
				if (Tools::isCallable('finfo_open'))
				{
					$const = defined('FILEINFO_MIME_TYPE') ? FILEINFO_MIME_TYPE : FILEINFO_MIME;
					$finfo = finfo_open($const);
					$mime_type = finfo_file($finfo, $file['tmp_name']);
					finfo_close($finfo);
				}
				elseif (Tools::isCallable('mime_content_type'))
					$mime_type = mime_content_type($file['tmp_name']);
				elseif (Tools::isCallable('exec'))
					$mime_type = trim(exec('file -b --mime-type '.escapeshellarg($file['tmp_name'])));
				if (empty($mime_type) || $mime_type == 'regular file')
					$mime_type = $file['type'];
				if (($pos = strpos($mime_type, ';')) !== false)
					$mime_type = substr($mime_type, 0, $pos);

				// Check mime content type
				if(!$mime_type || !in_array($mime_type, $this->acceptedImgMimeTypes))
					throw new WebserviceException('This type of image format not recognized, allowed formats are: '.implode('", "', $this->acceptedImgMimeTypes), array(73, 400));
				// Check error while uploading
				elseif ($file['error'])
					throw new WebserviceException('Error while uploading image. Please change your server\'s settings', array(74, 400));

				// Try to copy image file to a temporary file
				if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($_FILES['image']['tmp_name'], $tmpName))
					throw new WebserviceException('Error while copying image to the temporary directory', array(75, 400));
				// Try to copy image file to the image directory
				else
				{
					$result = $this->writeImageOnDisk($tmpName, $receptionPath, $destWidth, $destHeight, $imageTypes, $parentPath);
				}
				@unlink($tmpName);
				return $result;
			}
			else
				throw new WebserviceException('Please set an "image" parameter with image data for value', array(76, 400));
		}
		elseif ($this->wsObject->method == 'POST')
		{
			if (isset($_FILES['image']['tmp_name']) && $_FILES['image']['tmp_name'])
			{
				$file = $_FILES['image'];
				if ($file['size'] > $this->imgMaxUploadSize)
					throw new WebserviceException(sprintf('The image size is too large (maximum allowed is %d KB)', ($this->imgMaxUploadSize/1000)), array(72, 400));
				require_once(_PS_ROOT_DIR_.'/images.inc.php');
				if ($error = ImageManager::validateUpload($file))
					throw new WebserviceException('Image upload error : '.$error, array(76, 400));
				if (isset($file['tmp_name']) && $file['tmp_name'] != null)
				{
					if ($this->imageType == 'products')
					{
						$product = new Product((int)$this->wsObject->urlSegment[2]);
						if (!Validate::isLoadedObject($product))
							throw new WebserviceException('Product '.(int)$this->wsObject->urlSegment[2].' doesn\'t exists', array(76, 400));
						$image = new Image();
						$image->id_product = (int)($product->id);
						$image->position = Image::getHighestPosition($product->id) + 1;

						if (!Image::getCover((int)$product->id))
							$image->cover = 1;
						else
							$image->cover = 0;
							
						if (!$image->add())
							throw new WebserviceException('Error while creating image', array(76, 400));
						if (!Validate::isLoadedObject($product))
							throw new WebserviceException('Product '.(int)$this->wsObject->urlSegment[2].' doesn\'t exists', array(76, 400));
					}

					// copy image
					if (!isset($file['tmp_name']))
						return false;
					if ($error = ImageManager::validateUpload($file, $this->imgMaxUploadSize))
						throw new WebserviceException('Bad image : '.$error, array(76, 400));

					if ($this->imageType == 'products')
					{
						$image = new Image($image->id);
						if (!(Configuration::get('PS_OLD_FILESYSTEM') && file_exists(_PS_PROD_IMG_DIR_.$product->id.'-'.$image->id.'.jpg')))
							$image->createImgFolder();

						if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($file['tmp_name'], $tmpName))
							throw new WebserviceException('An error occurred during the image upload', array(76, 400));
						elseif (!ImageManager::resize($tmpName, _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format))
							throw new WebserviceException('An error occurred while copying image', array(76, 400));
						else
						{
							$imagesTypes = ImageType::getImagesTypes('products');
							foreach ($imagesTypes AS $imageType)
								if (!ImageManager::resize($tmpName, _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'-'.stripslashes($imageType['name']).'.'.$image->image_format, $imageType['width'], $imageType['height'], $image->image_format))
									$this->_errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
						}
						@unlink($tmpName);
						$this->imgToDisplay = _PS_PROD_IMG_DIR_.$image->getExistingImgPath().'.'.$image->image_format;
						$this->objOutput->setFieldsToDisplay('full');
						$this->output = $this->objOutput->renderEntity($image, 1);
						$image_content = array('sqlId' => 'content', 'value' => base64_encode(file_get_contents($this->imgToDisplay)), 'encode' => 'base64');
						$this->output .= $this->objOutput->objectRender->renderField($image_content);
					}
					elseif (in_array($this->imageType, array('categories', 'manufacturers', 'suppliers', 'stores')))
					{
						if (!($tmpName = tempnam(_PS_TMP_IMG_DIR_, 'PS')) || !move_uploaded_file($file['tmp_name'], $tmpName))
							throw new WebserviceException('An error occurred during the image upload', array(76, 400));
						elseif (!ImageManager::resize($tmpName, $receptionPath))
							throw new WebserviceException('An error occurred while copying image', array(76, 400));
						$imagesTypes = ImageType::getImagesTypes($this->imageType);
						foreach ($imagesTypes as $imageType)
							if (!ImageManager::resize($tmpName, $parentPath.$this->wsObject->urlSegment[2].'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']))
								$this->_errors[] = Tools::displayError('An error occurred while copying image:').' '.stripslashes($imageType['name']);
						@unlink(_PS_TMP_IMG_DIR_.$tmpName);
						$this->imgToDisplay = $receptionPath;
					}
					return true;
				}
			}
		}
		else
			throw new WebserviceException('Method '.$this->wsObject->method.' is not allowed for an image resource', array(77, 405));
	}
}
