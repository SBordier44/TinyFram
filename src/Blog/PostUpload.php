<?php

namespace App\Blog;

use Framework\Upload;

class PostUpload extends Upload
{
	/**
	 * @var string
	 */
	protected $path = 'public/uploads/posts';
	/**
	 * @var array
	 */
	protected $formats = [
		'thumb' => [320, 180]
	];
}
