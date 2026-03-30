<?php

namespace codesaur\Template\Tests;

use PHPUnit\Framework\TestCase;

use codesaur\Template\MemoryTemplate;

/**
 * MemoryTemplate классын unit test.
 *
 * @package codesaur\Template\Tests
 */
class MemoryTemplateTest extends TestCase
{
    /**
     * Энгийн хувьсагч орлуулах тест.
     */
    public function testSimpleVariableReplacement(): void
    {
        $template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
        $this->assertEquals('Hello, World!', $template->output());
    }

    /**
     * Whitespace-тай tag-уудыг зөв таних тест.
     */
    public function testWhitespaceHandling(): void
    {
        $template = new MemoryTemplate('{{   key   }}', ['key' => 'value']);
        $this->assertEquals('value', $template->output());
    }

    /**
     * Whitespace-гүй tag-уудыг зөв таних тест.
     */
    public function testNoWhitespaceHandling(): void
    {
        $template = new MemoryTemplate('{{key}}', ['key' => 'value']);
        $this->assertEquals('value', $template->output());
    }

    /**
     * Олон түвшний хувьсагч (nested array) тест.
     */
    public function testNestedVariableReplacement(): void
    {
        $vars = [
            'user' => [
                'profile' => [
                    'email' => 'test@example.com'
                ]
            ]
        ];
        $template = new MemoryTemplate('Email: {{ user.profile.email }}', $vars);
        $this->assertEquals('Email: test@example.com', $template->output());
    }

    /**
     * Олон хувьсагч орлуулах тест.
     */
    public function testMultipleVariables(): void
    {
        $vars = [
            'title' => 'Test Title',
            'content' => 'Test Content'
        ];
        $template = new MemoryTemplate('{{ title }}: {{ content }}', $vars);
        $this->assertEquals('Test Title: Test Content', $template->output());
    }

    /**
     * Олдохгүй хувьсагч хоосон string болох тест.
     */
    public function testMissingVariableRendersEmpty(): void
    {
        $template = new MemoryTemplate('Hello, {{ missing }}!', []);
        $this->assertEquals('Hello, !', $template->output());
    }

    /**
     * set() метод ашиглан хувьсагч нэмэх тест.
     */
    public function testSetMethod(): void
    {
        $template = new MemoryTemplate('{{ name }}', []);
        $template->set('name', 'John');
        $this->assertEquals('John', $template->output());
    }

    /**
     * setVars() метод ашиглан олон хувьсагч нэмэх тест.
     */
    public function testSetVarsMethod(): void
    {
        $template = new MemoryTemplate('{{ a }} {{ b }}', []);
        $template->setVars(['a' => 'Hello', 'b' => 'World']);
        $this->assertEquals('Hello World', $template->output());
    }

    /**
     * get() метод ашиглан хувьсагч авах тест.
     */
    public function testGetMethod(): void
    {
        $template = new MemoryTemplate('', ['key' => 'value']);
        $value = &$template->get('key');
        $this->assertEquals('value', $value);
    }

    /**
     * get() метод олдохгүй хувьсагчийн хувьд null буцаах тест.
     */
    public function testGetMethodReturnsNullForMissingKey(): void
    {
        $template = new MemoryTemplate('', []);
        $value = &$template->get('missing');
        $this->assertNull($value);
    }

    /**
     * getVars() метод тест.
     */
    public function testGetVarsMethod(): void
    {
        $vars = ['a' => 1, 'b' => 2];
        $template = new MemoryTemplate('', $vars);
        $this->assertEquals($vars, $template->getVars());
    }

    /**
     * getSource() метод тест.
     */
    public function testGetSourceMethod(): void
    {
        $html = 'Hello, {{ name }}!';
        $template = new MemoryTemplate($html, []);
        $this->assertEquals($html, $template->getSource());
    }

    /**
     * source() метод тест.
     */
    public function testSourceMethod(): void
    {
        $template = new MemoryTemplate('Old', []);
        $template->source('New');
        $this->assertEquals('New', $template->getSource());
    }

    /**
     * __toString() метод тест.
     */
    public function testToStringMethod(): void
    {
        $template = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
        $this->assertEquals('Hello, World!', (string) $template);
    }

    /**
     * Тоон утга stringify хийх тест.
     */
    public function testNumericStringify(): void
    {
        $template = new MemoryTemplate('{{ number }}', ['number' => 123]);
        $this->assertEquals('123', $template->output());
    }

    /**
     * Boolean утга stringify хийх тест.
     */
    public function testBooleanStringify(): void
    {
        $template = new MemoryTemplate('{{ flag }}', ['flag' => true]);
        $this->assertEquals('1', $template->output());
    }

    /**
     * render() метод тест (output буцаах).
     */
    public function testRenderMethod(): void
    {
        $template = new MemoryTemplate('Hello', []);
        ob_start();
        $template->render();
        $output = ob_get_clean();
        $this->assertEquals('Hello', $output);
    }

    /**
     * Цогц тест: олон түвшний массив, олон хувьсагч.
     */
    public function testComplexTemplate(): void
    {
        $vars = [
            'user' => [
                'name' => 'John',
                'age' => 30
            ],
            'message' => 'Welcome'
        ];
        $html = '{{ message }}, {{ user.name }}! You are {{ user.age }} years old.';
        $template = new MemoryTemplate($html, $vars);
        $this->assertEquals('Welcome, John! You are 30 years old.', $template->output());
    }

    // ==================== Filter & Function ====================

    /**
     * Custom filter-ийг template дотор ашиглах тест.
     */
    public function testCustomFilterInTemplate(): void
    {
        $template = new MemoryTemplate('{{ name|myrev }}', ['name' => 'hello']);
        $template->addFilter('myrev', fn($v) => \strrev((string) $v));
        $this->assertEquals('olleh', $template->output());
    }

    /**
     * Custom function-ийг template дотор ашиглах тест.
     */
    public function testCustomFunctionInTemplate(): void
    {
        $template = new MemoryTemplate('{{ sum(3, 7) }}', []);
        $template->addFunction('sum', fn($a, $b) => $a + $b);
        $this->assertEquals('10', $template->output());
    }

    // ==================== Engine features in MemoryTemplate ====================

    /**
     * MemoryTemplate дээр for loop ажиллах тест.
     */
    public function testForLoop(): void
    {
        $template = new MemoryTemplate(
            '{% for item in items %}{{ item }} {% endfor %}',
            ['items' => ['a', 'b', 'c']]
        );
        $this->assertEquals('a b c ', $template->output());
    }

    /**
     * MemoryTemplate дээр if/else ажиллах тест.
     */
    public function testIfElse(): void
    {
        $template = new MemoryTemplate(
            '{% if show %}yes{% else %}no{% endif %}',
            ['show' => true]
        );
        $this->assertEquals('yes', $template->output());

        $template->set('show', false);
        $this->assertEquals('no', $template->output());
    }

    /**
     * MemoryTemplate дээр built-in filter ашиглах тест.
     */
    public function testBuiltInFilter(): void
    {
        $template = new MemoryTemplate('{{ name|upper }}', ['name' => 'hello']);
        $this->assertEquals('HELLO', $template->output());
    }

    /**
     * MemoryTemplate дээр set tag ашиглах тест.
     */
    public function testSetTag(): void
    {
        $template = new MemoryTemplate('{% set greeting = "Hi" %}{{ greeting }}!', []);
        $this->assertEquals('Hi!', $template->output());
    }

    /**
     * MemoryTemplate дээр ternary operator ашиглах тест.
     */
    public function testTernaryOperator(): void
    {
        $template = new MemoryTemplate("{{ flag ? 'yes' : 'no' }}", ['flag' => true]);
        $this->assertEquals('yes', $template->output());
    }

    /**
     * MemoryTemplate дээр null coalescing operator ашиглах тест.
     */
    public function testNullCoalescing(): void
    {
        $template = new MemoryTemplate("{{ name ?? 'default' }}", []);
        $this->assertEquals('default', $template->output());
    }

    /**
     * MemoryTemplate дээр escape filter ашиглах тест.
     */
    public function testEscapeFilter(): void
    {
        $template = new MemoryTemplate('{{ html|e }}', ['html' => '<script>alert(1)</script>']);
        $output = $template->output();
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    /**
     * MemoryTemplate дээр loop хувьсагчид ашиглах тест.
     */
    public function testLoopVariables(): void
    {
        $template = new MemoryTemplate(
            '{% for item in items %}{{ loop.index }}:{{ item }}{% if not loop.last %},{% endif %}{% endfor %}',
            ['items' => ['a', 'b', 'c']]
        );
        $this->assertEquals('1:a,2:b,3:c', $template->output());
    }
}
