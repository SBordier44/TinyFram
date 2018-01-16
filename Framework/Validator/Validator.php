<?php

namespace Framework\Validator;

use Framework\Database\Table;
use Psr\Http\Message\UploadedFileInterface;

class Validator
{
	/**
	 * @var array
	 */
	public const MIME_TYPES = [
		'png'  => 'image/png',
		'jpg'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'gif'  => 'image/gif',
		'svg'  => 'image/svg+xml',
		'mp4'  => 'video/mp4',
		'mp3'  => 'audio/mpeg',
		'waw'  => 'audio/x-waw',
		'avi'  => 'video/x-msvideo',
		'flv'  => 'video/x-flv',
		'pdf'  => 'application/pdf',
		'txt'  => 'text/plain',
		'css'  => 'text/css',
		'html' => 'text/html',
		'js'   => 'application/javascript',
		'xml'  => 'text/xml',
		'zip'  => 'application/zip',
		'json' => 'application/json',
		'swf'  => 'application/x-shockwave-flash'
	];
	/**
	 * @var array
	 */
	private $params;
	/**
	 * @var string[]
	 */
	private $errors = [];
	
	/**
	 * Validator constructor.
	 * @param array $params
	 */
	public function __construct(array $params)
	{
		$this->params = $params;
	}
	
	/**
	 * @param mixed[] ...$keys
	 * @return Validator
	 */
	public function required(...$keys): self
	{
		if (\is_array($keys[0])) {
			$keys = $keys[0];
		}
		foreach ($keys as $key) {
			$value = $this->getValue($key);
			if (null === $value) {
				$this->addError($key, 'required');
			}
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return mixed|null
	 */
	private function getValue(string $key)
	{
		if (array_key_exists($key, $this->params)) {
			return $this->params[$key];
		}
		return null;
	}
	
	/**
	 * @param string $key
	 * @param string $rule
	 * @param array  $attributes
	 */
	private function addError(string $key, string $rule, array $attributes = []): void
	{
		$this->errors[$key] = new ValidationError($key, $rule, $attributes);
	}
	
	/**
	 * @param string[] ...$keys
	 * @return Validator
	 */
	public function notEmpty(string ...$keys): self
	{
		foreach ($keys as $key) {
			$value = $this->getValue($key);
			if (null === $value || empty($value)) {
				$this->addError($key, 'empty');
			}
		}
		return $this;
	}
	
	/**
	 * @param string   $key
	 * @param int|null $min
	 * @param int|null $max
	 * @return Validator
	 */
	public function length(string $key, ?int $min, ?int $max = null): self
	{
		$value  = $this->getValue($key);
		$length = mb_strlen($value);
		if (null !== $min && null !== $max && ($length < $min || $length > $max)) {
			$this->addError($key, 'betweenLength', [$min, $max]);
			return $this;
		}
		if (null !== $min && $length < $min) {
			$this->addError($key, 'minLength', [$min]);
			return $this;
		}
		if (null !== $max && $length > $max) {
			$this->addError($key, 'maxLength', [$max]);
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return Validator
	 */
	public function slug(string $key): self
	{
		$value   = $this->getValue($key);
		$pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
		if (null !== $value && !preg_match($pattern, $value)) {
			$this->addError($key, 'slug');
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return Validator
	 */
	public function numeric(string $key): self
	{
		$value = $this->getValue($key);
		if (!is_numeric($value)) {
			$this->addError($key, 'numeric');
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param string $format
	 * @return Validator
	 */
	public function dateTime(string $key, string $format = 'Y-m-d H:i:s'): self
	{
		$value  = $this->getValue($key);
		$date   = \DateTime::createFromFormat($format, $value);
		$errors = \DateTime::getLastErrors();
		if (false === $date || $errors['error_count'] > 0 || $errors['warning_count'] > 0) {
			$this->addError($key, 'datetime', [$format]);
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param string $table
	 * @param \PDO   $pdo
	 * @return Validator
	 */
	public function exists(string $key, string $table, \PDO $pdo): self
	{
		$value     = $this->getValue($key);
		$statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
		$statement->execute([$value]);
		if ($statement->fetchColumn() === false) {
			$this->addError($key, 'exists', [$table]);
		}
		return $this;
	}
	
	/**
	 * @param string       $key
	 * @param string|Table $table
	 * @param \PDO         $pdo
	 * @param int|null     $exclude
	 * @return Validator
	 */
	public function unique(string $key, $table, ?\PDO $pdo = null, ?int $exclude = null): self
	{
		if ($table instanceof Table) {
			$pdo   = $table->getPdo();
			$table = $table->getTable();
		}
		$value  = $this->getValue($key);
		$query  = "SELECT id FROM $table WHERE $key = ?";
		$params = [$value];
		if ($exclude !== null) {
			$query    .= ' AND id != ?';
			$params[] = $exclude;
		}
		$statement = $pdo->prepare($query);
		$statement->execute($params);
		if ($statement->fetchColumn() !== false) {
			$this->addError($key, 'unique', [$value]);
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return Validator
	 */
	public function uploaded(string $key): self
	{
		$file = $this->getValue($key);
		if ($file === null || $file->getError() !== UPLOAD_ERR_OK) {
			$this->addError($key, 'uploaded');
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return Validator
	 */
	public function email(string $key): self
	{
		$value = $this->getValue($key);
		if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
			$this->addError($key, 'email');
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @return Validator
	 */
	public function confirm(string $key): self
	{
		$value        = $this->getValue($key);
		$valueConfirm = $this->getValue($key . '_confirm');
		if ($valueConfirm !== $value) {
			$this->addError($key, 'confirm');
		}
		return $this;
	}
	
	/**
	 * @param string $key
	 * @param array  $extensions
	 * @return Validator
	 */
	public function extension(string $key, array $extensions): self
	{
		/** @var UploadedFileInterface $file */
		$file = $this->getValue($key);
		if ($file !== null && $file->getError() === UPLOAD_ERR_OK) {
			$type         = $file->getClientMediaType();
			$extension    = mb_strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
			$expectedType = self::MIME_TYPES[$extension] ?? null;
			if ($expectedType !== $type || !\in_array($extension, $extensions, true)) {
				$this->addError($key, 'filetype', [implode(',', $extensions)]);
			}
		}
		return $this;
	}
	
	/**
	 * @return bool
	 */
	public function isValid(): bool
	{
		return empty($this->errors);
	}
	
	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}
}
