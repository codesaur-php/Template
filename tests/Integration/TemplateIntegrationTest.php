<?php

namespace codesaur\Template\Tests\Integration;

use PHPUnit\Framework\TestCase;

use codesaur\Template\MemoryTemplate;
use codesaur\Template\FileTemplate;
use codesaur\Template\TwigTemplate;

/**
 * Template classes-ийн integration test.
 *
 * Энэ тест нь бодит файл системтэй ажиллах, олон template-үүд
 * хамтдаа ажиллах, бодит use case-уудыг шалгана.
 *
 * @package codesaur\Template\Tests\Integration
 */
class TemplateIntegrationTest extends TestCase
{
    /**
     * Integration test-ийн фолдер.
     */
    private string $testDir;

    /**
     * Test setUp: integration test фолдер үүсгэх.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/template_integration_' . uniqid();
        mkdir($this->testDir, 0777, true);
    }

    /**
     * Test tearDown: integration test фолдер устгах.
     */
    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $this->removeDirectory($this->testDir);
        }
        parent::tearDown();
    }

    /**
     * Фолдер болон түүний агуулгыг рекурсив байдлаар устгах.
     */
    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    /**
     * MemoryTemplate → FileTemplate → TwigTemplate дамжин ажиллах integration тест.
     */
    public function testTemplateInheritanceChain(): void
    {
        // 1. MemoryTemplate ашиглан энгийн template
        $memoryTemplate = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
        $this->assertEquals('Hello, World!', $memoryTemplate->output());

        // 2. FileTemplate ашиглан файлаас template унших
        $filePath = $this->testDir . '/template.html';
        file_put_contents($filePath, 'Welcome, {{ user }}!');
        $fileTemplate = new FileTemplate($filePath, ['user' => 'Narankhuu']);
        $this->assertEquals('Welcome, Narankhuu!', $fileTemplate->output());

        // 3. TwigTemplate ашиглан advanced template
        $twigPath = $this->testDir . '/twig_template.html';
        file_put_contents($twigPath, '{% for item in items %}{{ item }} {% endfor %}');
        $twigTemplate = new TwigTemplate($twigPath, ['items' => ['a', 'b', 'c']]);
        $this->assertEquals('a b c ', $twigTemplate->output());
    }

    /**
     * Олон template файлууд хамтдаа ажиллах integration тест.
     */
    public function testMultipleTemplateFiles(): void
    {
        // Header template
        $headerPath = $this->testDir . '/header.html';
        file_put_contents($headerPath, '<header>{{ title }}</header>');

        // Content template
        $contentPath = $this->testDir . '/content.html';
        file_put_contents($contentPath, '<main>{{ content }}</main>');

        // Footer template
        $footerPath = $this->testDir . '/footer.html';
        file_put_contents($footerPath, '<footer>{{ year }}</footer>');

        // Бүх template-үүдийг рэндэр хийх
        $header = new FileTemplate($headerPath, ['title' => 'My Site']);
        $content = new FileTemplate($contentPath, ['content' => 'Welcome']);
        $footer = new FileTemplate($footerPath, ['year' => '2025']);

        $this->assertEquals('<header>My Site</header>', $header->output());
        $this->assertEquals('<main>Welcome</main>', $content->output());
        $this->assertEquals('<footer>2025</footer>', $footer->output());
    }

    /**
     * Nested template structure integration тест.
     */
    public function testNestedTemplateStructure(): void
    {
        $layoutPath = $this->testDir . '/layout.html';
        $layoutContent = <<<'HTML'
<!DOCTYPE html>
<html>
<head>
    <title>{{ page.title }}</title>
</head>
<body>
    <h1>{{ page.heading }}</h1>
    <div>{{ page.content }}</div>
</body>
</html>
HTML;
        file_put_contents($layoutPath, $layoutContent);

        $vars = [
            'page' => [
                'title' => 'Test Page',
                'heading' => 'Welcome',
                'content' => 'This is a test page.'
            ]
        ];

        $template = new FileTemplate($layoutPath, $vars);
        $output = $template->output();

        $this->assertStringContainsString('<title>Test Page</title>', $output);
        $this->assertStringContainsString('<h1>Welcome</h1>', $output);
        $this->assertStringContainsString('This is a test page.', $output);
    }

    /**
     * TwigTemplate advanced features integration тест.
     */
    public function testTwigTemplateAdvancedFeatures(): void
    {
        $templatePath = $this->testDir . '/advanced.html';
        $templateContent = <<<'TWIG'
{% if users|length > 0 %}
    <ul>
    {% for user in users %}
        <li>{{ user.name }} ({{ user.age }})</li>
    {% endfor %}
    </ul>
{% else %}
    <p>No users found.</p>
{% endif %}
TWIG;
        file_put_contents($templatePath, $templateContent);

        $template = new TwigTemplate($templatePath, [
            'users' => [
                ['name' => 'John', 'age' => 30],
                ['name' => 'Jane', 'age' => 25]
            ]
        ]);

        $output = $template->output();
        $this->assertStringContainsString('John (30)', $output);
        $this->assertStringContainsString('Jane (25)', $output);
        $this->assertStringContainsString('<ul>', $output);
        $this->assertStringNotContainsString('No users found', $output);
    }

    /**
     * Template variables динамик өөрчлөх integration тест.
     */
    public function testDynamicVariableUpdates(): void
    {
        $templatePath = $this->testDir . '/dynamic.html';
        file_put_contents($templatePath, 'Count: {{ count }}, Status: {{ status }}');

        $template = new FileTemplate($templatePath, ['count' => 0, 'status' => 'inactive']);

        // Эхний рэндэр
        $this->assertStringContainsString('Count: 0', $template->output());
        $this->assertStringContainsString('Status: inactive', $template->output());

        // Хувьсагчдыг өөрчлөх
        $template->set('count', 10);
        $template->set('status', 'active');

        // Дахин рэндэр
        $output = $template->output();
        $this->assertStringContainsString('Count: 10', $output);
        $this->assertStringContainsString('Status: active', $output);
    }

    /**
     * Template file-ийн агуулгыг өөрчлөх integration тест.
     */
    public function testTemplateFileContentChanges(): void
    {
        $templatePath = $this->testDir . '/changeable.html';
        file_put_contents($templatePath, 'Version 1: {{ value }}');

        $template = new FileTemplate($templatePath, ['value' => 'test']);
        $this->assertEquals('Version 1: test', $template->output());

        // Файлын агуулгыг өөрчлөх
        file_put_contents($templatePath, 'Version 2: {{ value }}');
        $this->assertEquals('Version 2: test', $template->output());
    }

    /**
     * TwigTemplate custom filter integration тест.
     */
    public function testTwigCustomFilterIntegration(): void
    {
        $templatePath = $this->testDir . '/filter.html';
        file_put_contents($templatePath, 'Number: {{ number|int }}');

        $template = new TwigTemplate($templatePath, ['number' => '42.5']);
        $this->assertEquals('Number: 42', $template->output());
    }

    /**
     * TwigTemplate custom function integration тест.
     */
    public function testTwigCustomFunctionIntegration(): void
    {
        $templatePath = $this->testDir . '/function.html';
        file_put_contents($templatePath, '{{ greet("World") }}');

        $template = new TwigTemplate($templatePath, []);

        $template->addFunction(new \Twig\TwigFunction('greet', function ($name) {
            return "Hello, $name!";
        }));

        $this->assertEquals('Hello, World!', $template->output());
    }

    /**
     * Complex real-world scenario integration тест.
     */
    public function testRealWorldScenario(): void
    {
        // Бодит веб хуудасны template
        $pagePath = $this->testDir . '/page.html';
        $pageContent = <<<'TWIG'
<!DOCTYPE html>
<html lang="mn">
<head>
    <meta charset="UTF-8">
    <title>{{ site.title }} - {{ page.title }}</title>
</head>
<body>
    <nav>
        {% for item in menu %}
            <a href="{{ item.url }}">{{ item.label }}</a>
        {% endfor %}
    </nav>
    <main>
        <h1>{{ page.title }}</h1>
        <p>{{ page.content }}</p>
        {% if page.showDate %}
            <small>Огноо: {{ "now"|date("Y-m-d") }}</small>
        {% endif %}
    </main>
</body>
</html>
TWIG;
        file_put_contents($pagePath, $pageContent);

        $template = new TwigTemplate($pagePath, [
            'site' => [
                'title' => 'My Website'
            ],
            'page' => [
                'title' => 'Нүүр хуудас',
                'content' => 'Тавтай морилно уу!',
                'showDate' => true
            ],
            'menu' => [
                ['url' => '/', 'label' => 'Нүүр'],
                ['url' => '/about', 'label' => 'Бидний тухай'],
                ['url' => '/contact', 'label' => 'Холбоо барих']
            ]
        ]);

        $output = $template->output();

        // Шалгалтууд
        $this->assertStringContainsString('<title>My Website - Нүүр хуудас</title>', $output);
        $this->assertStringContainsString('<h1>Нүүр хуудас</h1>', $output);
        $this->assertStringContainsString('Тавтай морилно уу!', $output);
        $this->assertStringContainsString('Нүүр', $output);
        $this->assertStringContainsString('Бидний тухай', $output);
        $this->assertStringContainsString('Холбоо барих', $output);
        $this->assertStringContainsString('Огноо:', $output);
    }

    /**
     * Template caching simulation integration тест.
     */
    public function testTemplateCachingSimulation(): void
    {
        $templatePath = $this->testDir . '/cache.html';
        file_put_contents($templatePath, '{{ content }}');

        // Эхний рэндэр
        $template1 = new FileTemplate($templatePath, ['content' => 'First']);
        $output1 = $template1->output();

        // Хоёр дахь рэндэр (өөр хувьсагч)
        $template2 = new FileTemplate($templatePath, ['content' => 'Second']);
        $output2 = $template2->output();

        $this->assertEquals('First', $output1);
        $this->assertEquals('Second', $output2);
        $this->assertNotEquals($output1, $output2);
    }
}
