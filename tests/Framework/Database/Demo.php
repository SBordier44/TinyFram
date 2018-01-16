<?php

namespace Tests\Framework\Database;

class Demo
{
	private $slug;
	
	public function getslug()
	{
		return $this->slug;
	}
	
	public function setSlug($slug)
	{
		$this->slug = $slug . 'demo';
	}
}
