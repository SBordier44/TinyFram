<?php

namespace App\Shop\Upload;

use Framework\Upload;
use const {
	WEB_PATH, DIRECTORY_SEPARATOR
};

class ProductImageUpload extends Upload
{
	/**
	 * @var string
	 */
	protected $path = WEB_PATH . DIRECTORY_SEPARATOR . 'uploads/products';
	/**
	 * @var array
	 */
	protected $formats = [
		'thumb' => [320, 180]
	];
}
