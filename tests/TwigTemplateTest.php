<?php

namespace codesaur\Template\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Twig\TwigFilter;
use Twig\TwigFunction;

use codesaur\Template\TwigTemplate;

/**
 * TwigTemplate классын unit test.
 *
 * @package codesaur\Template\Tests
 */
class TwigTemplateTest extends TestCase
{
    /**
     * Тест файлын зам.
     */
    private string $testTemplatePath;

    /**
     * Test setUp: тест файл үүсгэх.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->testTemplatePath = sys_get_temp_dir() . '/twig_test_' . uniqid() . '.html';
    }

    /**
     * Test tearDown: тест файл устгах.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->testTemplatePath)) {
            unlink($this->testTemplatePath);
        }
        parent::tearDown();
    }

    /**
     * Энгийн Twig template рэндэр хийх тест.
     */
    public function testSimpleTwigRendering(): void
    {
        file_put_contents($this->testTemplatePath, 'Hello, {{ name }}!');
        $template = new TwigTemplate($this->testTemplatePath, ['name' => 'World']);
        $this->assertEquals('Hello, World!', $template->output());
    }

    /**
     * Twig for loop ашиглах тест.
     */
    public function testTwigForLoop(): void
    {
        $content = '{% for item in items %}{{ item }} {% endfor %}';
        file_put_contents($this->testTemplatePath, $content);
        $template = new TwigTemplate($this->testTemplatePath, ['items' => ['a', 'b', 'c']]);
        $this->assertEquals('a b c ', $template->output());
    }

    /**
     * Twig if statement ашиглах тест.
     */
    public function testTwigIfStatement(): void
    {
        $content = '{% if flag %}Yes{% else %}No{% endif %}';
        file_put_contents($this->testTemplatePath, $content);
        $template = new TwigTemplate($this->testTemplatePath, ['flag' => true]);
        $this->assertEquals('Yes', $template->output());

        $template->set('flag', false);
        $this->assertEquals('No', $template->output());
    }

    /**
     * Custom filter нэмэх тест.
     */
    public function testAddCustomFilter(): void
    {
        file_put_contents($this->testTemplatePath, '{{ value|uppercase }}');
        $template = new TwigTemplate($this->testTemplatePath, ['value' => 'hello']);
        
        $template->addFilter(new TwigFilter('uppercase', function ($string) {
            return strtoupper($string);
        }));

        $this->assertEquals('HELLO', $template->output());
    }

    /**
     * Built-in int filter тест.
     */
    public function testBuiltInIntFilter(): void
    {
        file_put_contents($this->testTemplatePath, '{{ value|int }}');
        $template = new TwigTemplate($this->testTemplatePath, ['value' => '123']);
        $this->assertEquals('123', $template->output());
    }

    /**
     * Built-in json_decode filter тест.
     */
    public function testBuiltInJsonDecodeFilter(): void
    {
        $json = '{"name":"John","age":30}';
        file_put_contents($this->testTemplatePath, '{{ json|json_decode|json_encode }}');
        $template = new TwigTemplate($this->testTemplatePath, ['json' => $json]);
        $decoded = json_decode($template->output(), true);
        $this->assertEquals('John', $decoded['name']);
        $this->assertEquals(30, $decoded['age']);
    }

    /**
     * Custom function нэмэх тест.
     */
    public function testAddCustomFunction(): void
    {
        file_put_contents($this->testTemplatePath, '{{ greet("World") }}');
        $template = new TwigTemplate($this->testTemplatePath, []);
        
        $template->addFunction(new TwigFunction('greet', function ($name) {
            return "Hello, $name!";
        }));

        $this->assertEquals('Hello, World!', $template->output());
    }

    /**
     * addGlobal() метод тест.
     */
    public function testAddGlobal(): void
    {
        file_put_contents($this->testTemplatePath, '{{ app_name }}');
        $template = new TwigTemplate($this->testTemplatePath, []);
        $template->addGlobal('app_name', 'MyApp');
        $this->assertEquals('MyApp', $template->output());
    }

    /**
     * getEnvironment() метод тест.
     */
    public function testGetEnvironment(): void
    {
        $template = new TwigTemplate($this->testTemplatePath, []);
        $environment = $template->getEnvironment();
        $this->assertInstanceOf(\Twig\Environment::class, $environment);
    }

    /**
     * FileTemplate-ийн функцүүдийг өвлөж авсан эсэхийг тест.
     */
    public function testInheritsFileTemplateMethods(): void
    {
        file_put_contents($this->testTemplatePath, '{{ name }}');
        $template = new TwigTemplate($this->testTemplatePath, ['name' => 'Test']);
        $this->assertEquals($this->testTemplatePath, $template->getFileName());
        $this->assertEquals('{{ name }}', $template->getFileSource());
    }

    /**
     * Олон түвшний Twig template тест.
     */
    public function testComplexTwigTemplate(): void
    {
        $content = <<<'TWIG'
{% for user in users %}
  {{ user.name }} ({{ user.age }})
{% endfor %}
TWIG;
        file_put_contents($this->testTemplatePath, $content);
        $vars = [
            'users' => [
                ['name' => 'John', 'age' => 30],
                ['name' => 'Jane', 'age' => 25]
            ]
        ];
        $template = new TwigTemplate($this->testTemplatePath, $vars);
        $output = $template->output();
        $this->assertStringContainsString('John (30)', $output);
        $this->assertStringContainsString('Jane (25)', $output);
    }

    /**
     * Twig filter chain тест.
     */
    public function testTwigFilterChain(): void
    {
        \file_put_contents($this->testTemplatePath, '{{ value|int }}');
        $template = new TwigTemplate($this->testTemplatePath, ['value' => '42']);
        $this->assertEquals('42', $template->output());
    }

    /**
     * Олдохгүй файлын хувьд exception шидэх тест.
     */
    public function testFileNotFoundThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('not found');

        $template = new TwigTemplate('/nonexistent/file.html', []);
        $template->output();
    }

    /**
     * Twig синтакс алдаа гарвал exception шидэх тест.
     */
    public function testTwigSyntaxErrorThrowsException(): void
    {
        \file_put_contents($this->testTemplatePath, '{% invalid syntax %}');
        $template = new TwigTemplate($this->testTemplatePath, []);

        $this->expectException(\Twig\Error\SyntaxError::class);
        $template->output();
    }

    /**
     * Autoescape false тохиргоо тест (HTML шууд гарах).
     */
    public function testAutoescapeDisabled(): void
    {
        $html = '<strong>{{ content }}</strong>';
        \file_put_contents($this->testTemplatePath, $html);
        $template = new TwigTemplate($this->testTemplatePath, ['content' => '<em>test</em>']);
        $output = $template->output();
        // Autoescape false тул HTML tag-ууд escape хийгдэхгүй
        $this->assertStringContainsString('<em>test</em>', $output);
    }
}
