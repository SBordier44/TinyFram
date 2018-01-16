<?php

namespace Tests\Framework\Twig;

use Framework\Twig\FormExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{
    
    /**
     * @var FormExtension
     */
    private $formExtension;
    
    public function setUp()
    {
        $this->formExtension = new FormExtension();
    }
    
    public function testField()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre');
        $this->assertSimilar('
            <div class="form-group">
              <label for="name">Titre</label>
              <input class="form-control" name="name" id="name" type="text" value="demo">
            </div>
        ', $html);
    }
    
    public function assertSimilar(string $expected, string $actual)
    {
        static::assertEquals($this->trim($expected), $this->trim($actual));
    }
    
    private function trim(string $string)
    {
        $lines = explode(PHP_EOL, $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }
    
    public function testFieldWithClass()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['class' => 'demo']);
        $this->assertSimilar('
            <div class="form-group">
              <label for="name">Titre</label>
              <input class="form-control demo" name="name" id="name" type="text" value="demo">
            </div>
        ', $html);
    }
    
    public function testTextarea()
    {
        $html = $this->formExtension->field([], 'name', 'demo', 'Titre', ['type' => 'textarea']);
        $this->assertSimilar('
            <div class="form-group">
              <label for="name">Titre</label>
              <textarea class="form-control" name="name" id="name">demo</textarea>
            </div>
        ', $html);
    }
    
    public function testFieldWithErrors()
    {
        $context = ['errors' => ['name' => 'erreur']];
        $html    = $this->formExtension->field($context, 'name', 'demo', 'Titre');
        $this->assertSimilar('
            <div class="form-group has-danger">
              <label for="name">Titre</label>
              <input class="form-control form-control-danger" name="name" id="name" type="text" value="demo">
              <small class="form-text text-muted">erreur</small>
            </div>
        ', $html);
    }
    
    public function testSelect()
    {
        $html = $this->formExtension->field([], 'name', 2, 'Titre', ['options' => [1 => 'Demo', '2' => 'Demo2']]);
        $this->assertSimilar('<div class="form-group">
              <label for="name">Titre</label>
              <select class="form-control" name="name" id="name">
              <option value="1">Demo</option>
              <option value="2" selected>Demo2</option>
              </select>
            </div>', $html);
    }
}
