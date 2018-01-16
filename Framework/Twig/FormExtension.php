<?php

namespace Framework\Twig;

use Twig_Extension;
use Twig_SimpleFunction;
use const DATE_ATOM;

class FormExtension extends Twig_Extension
{
	/**
	 * @return array
	 */
	public function getFunctions(): array
	{
		return [
			new Twig_SimpleFunction('field', [$this, 'field'], [
				'is_safe'       => ['html'],
				'needs_context' => true
			])
		];
	}
	
	/**
	 * @param array       $context Contexte de la vue Twig
	 * @param string      $key     Clef du champs
	 * @param mixed       $value   Valeur du champs
	 * @param string|null $label   Label à utiliser
	 * @param array       $options
	 * @param array       $attributes
	 * @return string
	 */
	public function field(
		array $context,
		string $key,
		$value,
		?string $label = null,
		array $options = [],
		array $attributes = []
	): string {
		$type       = $options['type'] ?? 'text';
		$error      = $this->getErrorHtml($context, $key);
		$class      = 'form-group';
		$value      = $this->convertValue($value);
		$attributes = array_merge([
			'class' => trim('form-control ' . ($options['class'] ?? '')),
			'name'  => $key,
			'id'    => $key
		], $attributes);
		if ($error) {
			$class               .= ' has-danger';
			$attributes['class'] .= ' form-control-danger';
		}
		if ($type === 'textarea') {
			$input = $this->textarea($value, $attributes);
		} elseif ($type === 'file') {
			$input = $this->file($attributes);
		} elseif ($type === 'checkbox') {
			$input = $this->checkbox($value, $attributes);
		} elseif (array_key_exists('options', $options)) {
			$input = $this->select($value, $options['options'], $attributes);
		} else {
			$attributes['type'] = $options['type'] ?? 'text';
			$input              = $this->input($value, $attributes);
		}
		return '<div class="' . $class . '">
              <label for="name">' . $label . '</label>
              ' . $input . '
              ' . $error . '
            </div>';
	}
	
	/**
	 * @param $context
	 * @param $key
	 * @return string
	 */
	private function getErrorHtml($context, $key): string
	{
		$error = $context['errors'][$key] ?? false;
		if ($error) {
			return '<small class="form-text text-muted">' . $error . '</small>';
		}
		return '';
	}
	
	/**
	 * @param $value
	 * @return string
	 */
	private function convertValue($value): string
	{
		if ($value instanceof \DateTime) {
			return $value->format(DATE_ATOM);
		}
		return (string)$value;
	}
	
	/**
	 * @param null|string $value
	 * @param array       $attributes
	 * @return string
	 */
	private function textarea(?string $value, array $attributes): string
	{
		return '<textarea ' . $this->getHtmlFromArray($attributes) . ">{$value}</textarea>";
	}
	
	/**
	 * @param array $attributes
	 * @return string
	 */
	private function getHtmlFromArray(array $attributes): string
	{
		$htmlParts = [];
		foreach ($attributes as $key => $value) {
			if ($value === true) {
				$htmlParts[] = (string)$key;
			} elseif ($value !== false) {
				$htmlParts[] = "$key=\"$value\"";
			}
		}
		return implode(' ', $htmlParts);
	}
	
	/**
	 * @param $attributes
	 * @return string
	 */
	private function file($attributes): string
	{
		return '<input type="file" ' . $this->getHtmlFromArray($attributes) . '>';
	}
	
	/**
	 * @param null|string $value
	 * @param array       $attributes
	 * @return string
	 */
	private function checkbox(?string $value, array $attributes): string
	{
		$html = '<input type="hidden" name="' . $attributes['name'] . '" value="0">';
		if ($value) {
			$attributes['checked'] = true;
		}
		return $html . '<input type="checkbox" ' . $this->getHtmlFromArray($attributes) . ' value="1">';
	}
	
	/**
	 * @param null|string $value
	 * @param array       $options
	 * @param array       $attributes
	 * @return string
	 */
	private function select(?string $value, array $options, array $attributes): string
	{
		$htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
			$params = ['value' => $key, 'selected' => $key === $value];
			return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
		}, '');
		return '<select ' . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";
	}
	
	/**
	 * @param null|string $value
	 * @param array       $attributes
	 * @return string
	 */
	private function input(?string $value, array $attributes): string
	{
		return '<input ' . $this->getHtmlFromArray($attributes) . ' value="' . $value . '">';
	}
}
