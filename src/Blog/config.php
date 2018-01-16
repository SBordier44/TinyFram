<?php

use App\Blog\BlogWidget;
use function DI\{
	add, get
};

return [
	'blog.prefix'   => '/blog',
	'admin.widgets' => add([
		get(BlogWidget::class)
	])
];
