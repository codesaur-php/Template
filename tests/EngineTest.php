<?php

namespace codesaur\Template\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;

use codesaur\Template\FileTemplate;

/**
 * FileTemplate template engine-ийн unit test.
 *
 * Twig-тэй нийцтэй синтакс (if, for, filter, macro гм.)
 * FileTemplate-д шууд хэрэгжсэн. Энэ тест нь тэр чадамжуудыг шалгана.
 *
 * @package codesaur\Template\Tests
 */
class EngineTest extends TestCase
{
    private string $testTemplatePath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testTemplatePath = sys_get_temp_dir() . '/engine_test_' . uniqid() . '.html';
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testTemplatePath)) {
            unlink($this->testTemplatePath);
        }
        parent::tearDown();
    }

    public function testSimpleRendering(): void
    {
        file_put_contents($this->testTemplatePath, 'Hello, {{ name }}!');
        $template = new FileTemplate($this->testTemplatePath, ['name' => 'World']);
        $this->assertEquals('Hello, World!', $template->output());
    }

    public function testForLoop(): void
    {
        $content = '{% for item in items %}{{ item }} {% endfor %}';
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, ['items' => ['a', 'b', 'c']]);
        $this->assertEquals('a b c ', $template->output());
    }

    public function testIfStatement(): void
    {
        $content = '{% if flag %}Yes{% else %}No{% endif %}';
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, ['flag' => true]);
        $this->assertEquals('Yes', $template->output());

        $template->set('flag', false);
        $this->assertEquals('No', $template->output());
    }

    public function testAddCustomFilter(): void
    {
        file_put_contents($this->testTemplatePath, '{{ value|uppercase }}');
        $template = new FileTemplate($this->testTemplatePath, ['value' => 'hello']);
        $template->addFilter('uppercase', fn($s) => strtoupper($s));
        $this->assertEquals('HELLO', $template->output());
    }

    public function testBuiltInIntFilter(): void
    {
        file_put_contents($this->testTemplatePath, '{{ value|int }}');
        $template = new FileTemplate($this->testTemplatePath, ['value' => '123']);
        $this->assertEquals('123', $template->output());
    }

    public function testBuiltInJsonDecodeFilter(): void
    {
        $json = '{"name":"John","age":30}';
        file_put_contents($this->testTemplatePath, '{{ json|json_decode|json_encode }}');
        $template = new FileTemplate($this->testTemplatePath, ['json' => $json]);
        $decoded = json_decode($template->output(), true);
        $this->assertEquals('John', $decoded['name']);
        $this->assertEquals(30, $decoded['age']);
    }

    public function testAddCustomFunction(): void
    {
        file_put_contents($this->testTemplatePath, '{{ greet("World") }}');
        $template = new FileTemplate($this->testTemplatePath, []);
        $template->addFunction('greet', fn($name) => "Hello, $name!");
        $this->assertEquals('Hello, World!', $template->output());
    }

    public function testComplexTemplate(): void
    {
        $content = <<<'TPL'
{% for user in users %}
  {{ user.name }} ({{ user.age }})
{% endfor %}
TPL;
        file_put_contents($this->testTemplatePath, $content);
        $vars = [
            'users' => [
                ['name' => 'John', 'age' => 30],
                ['name' => 'Jane', 'age' => 25]
            ]
        ];
        $template = new FileTemplate($this->testTemplatePath, $vars);
        $output = $template->output();
        $this->assertStringContainsString('John (30)', $output);
        $this->assertStringContainsString('Jane (25)', $output);
    }

    public function testFilterChain(): void
    {
        file_put_contents($this->testTemplatePath, '{{ value|int }}');
        $template = new FileTemplate($this->testTemplatePath, ['value' => '42']);
        $this->assertEquals('42', $template->output());
    }

    public function testFileNotFoundThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('not found');
        $template = new FileTemplate('/nonexistent/file.html', []);
        $template->output();
    }

    public function testAutoescapeDisabled(): void
    {
        $html = '<strong>{{ content }}</strong>';
        file_put_contents($this->testTemplatePath, $html);
        $template = new FileTemplate($this->testTemplatePath, ['content' => '<em>test</em>']);
        $output = $template->output();
        $this->assertStringContainsString('<em>test</em>', $output);
    }

    public function testEscapeFilter(): void
    {
        file_put_contents($this->testTemplatePath, '{{ content|e }}');
        $template = new FileTemplate($this->testTemplatePath, ['content' => '<script>alert(1)</script>']);
        $output = $template->output();
        $this->assertStringNotContainsString('<script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }

    public function testLoopVariables(): void
    {
        $content = '{% for item in items %}{{ loop.index }}:{{ item }}{% if not loop.last %},{% endif %}{% endfor %}';
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, ['items' => ['a', 'b', 'c']]);
        $this->assertEquals('1:a,2:b,3:c', $template->output());
    }

    public function testSetVariable(): void
    {
        $content = '{% set greeting = "Hello" %}{{ greeting }}!';
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, []);
        $this->assertEquals('Hello!', $template->output());
    }

    public function testTernaryOperator(): void
    {
        file_put_contents($this->testTemplatePath, "{{ flag ? 'yes' : 'no' }}");
        $template = new FileTemplate($this->testTemplatePath, ['flag' => true]);
        $this->assertEquals('yes', $template->output());
    }

    public function testNullCoalescing(): void
    {
        file_put_contents($this->testTemplatePath, "{{ name ?? 'default' }}");
        $template = new FileTemplate($this->testTemplatePath, []);
        $this->assertEquals('default', $template->output());
    }

    public function testIsDefinedTest(): void
    {
        $content = '{% if name is defined %}yes{% else %}no{% endif %}';
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, ['name' => 'test']);
        $this->assertEquals('yes', $template->output());
    }

    public function testStartsWithOperator(): void
    {
        $content = "{% if url starts with 'http' %}link{% else %}path{% endif %}";
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, ['url' => 'https://example.com']);
        $this->assertEquals('link', $template->output());
    }

    public function testConcatOperator(): void
    {
        file_put_contents($this->testTemplatePath, "{{ first ~ ' ' ~ last }}");
        $template = new FileTemplate($this->testTemplatePath, ['first' => 'John', 'last' => 'Doe']);
        $this->assertEquals('John Doe', $template->output());
    }

    public function testHashLiteral(): void
    {
        $content = "{% set data = {'key': 'value'} %}{{ data.key }}";
        file_put_contents($this->testTemplatePath, $content);
        $template = new FileTemplate($this->testTemplatePath, []);
        $this->assertEquals('value', $template->output());
    }

    public function testDateFilter(): void
    {
        file_put_contents($this->testTemplatePath, "{{ d|date('Y') }}");
        $template = new FileTemplate($this->testTemplatePath, ['d' => '2026-03-28']);
        $this->assertEquals('2026', $template->output());
    }

    public function testSliceFilter(): void
    {
        file_put_contents($this->testTemplatePath, "{{ text|slice(0, 5) }}");
        $template = new FileTemplate($this->testTemplatePath, ['text' => 'Hello World']);
        $this->assertEquals('Hello', $template->output());
    }
}
