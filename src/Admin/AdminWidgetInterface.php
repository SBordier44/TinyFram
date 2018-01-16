<?php

namespace App\Admin;

interface AdminWidgetInterface
{
	/**
	 * @return string
	 */
	public function render(): string;
	
	/**
	 * @return string
	 */
	public function renderMenu(): string;
}
