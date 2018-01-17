<?php

namespace Framework\Renderer;

class PHPRenderer implements RendererInterface
{
    public const DEFAULT_NAMESPACE = '__MAIN';
    /**
     * @var array
     */
    private $paths = [];
    /**
     * @var array
     */
    private $globals = [];
    
    /**
     * PHPRenderer constructor.
     * @param null|string $defaultPath
     */
    public function __construct(?string $defaultPath = null)
    {
        if (null !== $defaultPath) {
            $this->addPath($defaultPath);
        }
    }
    
    /**
     * @param string      $namespace
     * @param null|string $path
     */
    public function addPath(string $namespace, ?string $path = null): void
    {
        if (null === $path) {
            $this->paths[self::DEFAULT_NAMESPACE] = $namespace;
        } else {
            $this->paths[$namespace] = $path;
        }
    }
    
    /**
     * @param string $view
     * @param array  $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }
        ob_start();
        $renderer = $this;
        extract($this->globals, EXTR_OVERWRITE);
        extract($params, EXTR_OVERWRITE);
        require($path);
        return ob_get_clean();
    }
    
    /**
     * @param string $view
     * @return bool
     */
    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }
    
    /**
     * @param string $view
     * @return string
     */
    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
    
    /**
     * @param string $view
     * @return string
     */
    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }
    
    /**
     * @param string $key
     * @param mixed  $value
     */
    public function addGlobal(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }
}
