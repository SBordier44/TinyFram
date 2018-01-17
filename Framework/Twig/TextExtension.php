<?php

namespace Framework\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class TextExtension extends Twig_Extension
{
    /**
     * @return Twig_SimpleFilter[]
     */
    public function getFilters(): array
    {
        return [
            new Twig_SimpleFilter('excerpt', [$this, 'excerpt'])
        ];
    }
    
    /**
     * @param string $content
     * @param int    $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string
    {
        if (null === $content) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            $excerpt   = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }
        return $content;
    }
}
