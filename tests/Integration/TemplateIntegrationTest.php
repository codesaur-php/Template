<?php

namespace codesaur\Template\Tests\Integration;

use PHPUnit\Framework\TestCase;

use codesaur\Template\MemoryTemplate;
use codesaur\Template\FileTemplate;

/**
 * Template classes-ийн integration test.
 *
 * Бодит файл системтэй ажиллах, олон template хамтдаа
 * ажиллах, бодит use case-уудыг шалгана.
 *
 * @package codesaur\Template\Tests\Integration
 */
class TemplateIntegrationTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = sys_get_temp_dir() . '/template_integration_' . uniqid();
        mkdir($this->testDir, 0777, true);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->testDir)) {
            $this->removeDirectory($this->testDir);
        }
        parent::tearDown();
    }

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
     * MemoryTemplate -> FileTemplate дамжин ажиллах integration тест.
     */
    public function testTemplateInheritanceChain(): void
    {
        // MemoryTemplate - энгийн {{ key }} орлуулга
        $memoryTemplate = new MemoryTemplate('Hello, {{ name }}!', ['name' => 'World']);
        $this->assertEquals('Hello, World!', $memoryTemplate->output());

        // FileTemplate - файлаас уншиж, бүрэн engine ажиллуулах
        $filePath = $this->testDir . '/template.html';
        file_put_contents($filePath, 'Welcome, {{ user }}!');
        $fileTemplate = new FileTemplate($filePath, ['user' => 'Narankhuu']);
        $this->assertEquals('Welcome, Narankhuu!', $fileTemplate->output());

        // FileTemplate - for loop
        $advancedPath = $this->testDir . '/advanced.html';
        file_put_contents($advancedPath, '{% for item in items %}{{ item }} {% endfor %}');
        $advancedTemplate = new FileTemplate($advancedPath, ['items' => ['a', 'b', 'c']]);
        $this->assertEquals('a b c ', $advancedTemplate->output());
    }

    public function testMultipleTemplateFiles(): void
    {
        $headerPath = $this->testDir . '/header.html';
        file_put_contents($headerPath, '<header>{{ title }}</header>');

        $contentPath = $this->testDir . '/content.html';
        file_put_contents($contentPath, '<main>{{ content }}</main>');

        $footerPath = $this->testDir . '/footer.html';
        file_put_contents($footerPath, '<footer>{{ year }}</footer>');

        $header = new FileTemplate($headerPath, ['title' => 'My Site']);
        $content = new FileTemplate($contentPath, ['content' => 'Welcome']);
        $footer = new FileTemplate($footerPath, ['year' => '2025']);

        $this->assertEquals('<header>My Site</header>', $header->output());
        $this->assertEquals('<main>Welcome</main>', $content->output());
        $this->assertEquals('<footer>2025</footer>', $footer->output());
    }

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

    public function testAdvancedFeatures(): void
    {
        $templatePath = $this->testDir . '/advanced.html';
        $templateContent = <<<'TPL'
{% if users|length > 0 %}
    <ul>
    {% for user in users %}
        <li>{{ user.name }} ({{ user.age }})</li>
    {% endfor %}
    </ul>
{% else %}
    <p>No users found.</p>
{% endif %}
TPL;
        file_put_contents($templatePath, $templateContent);

        $template = new FileTemplate($templatePath, [
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

    public function testDynamicVariableUpdates(): void
    {
        $templatePath = $this->testDir . '/dynamic.html';
        file_put_contents($templatePath, 'Count: {{ count }}, Status: {{ status }}');

        $template = new FileTemplate($templatePath, ['count' => 0, 'status' => 'inactive']);
        $this->assertStringContainsString('Count: 0', $template->output());

        $template->set('count', 10);
        $template->set('status', 'active');
        $output = $template->output();
        $this->assertStringContainsString('Count: 10', $output);
        $this->assertStringContainsString('Status: active', $output);
    }

    public function testTemplateFileContentChanges(): void
    {
        $templatePath = $this->testDir . '/changeable.html';
        file_put_contents($templatePath, 'Version 1: {{ value }}');

        $template = new FileTemplate($templatePath, ['value' => 'test']);
        $this->assertEquals('Version 1: test', $template->output());

        file_put_contents($templatePath, 'Version 2: {{ value }}');
        $this->assertEquals('Version 2: test', $template->output());
    }

    public function testCustomFilterIntegration(): void
    {
        $templatePath = $this->testDir . '/filter.html';
        file_put_contents($templatePath, 'Number: {{ number|int }}');

        $template = new FileTemplate($templatePath, ['number' => '42.5']);
        $this->assertEquals('Number: 42', $template->output());
    }

    public function testCustomFunctionIntegration(): void
    {
        $templatePath = $this->testDir . '/function.html';
        file_put_contents($templatePath, '{{ greet("World") }}');

        $template = new FileTemplate($templatePath, []);
        $template->addFunction('greet', fn($name) => "Hello, $name!");
        $this->assertEquals('Hello, World!', $template->output());
    }

    public function testRealWorldScenario(): void
    {
        $pagePath = $this->testDir . '/page.html';
        $pageContent = <<<'TPL'
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
            <small>Огноо: {{ page.date|date("Y-m-d") }}</small>
        {% endif %}
    </main>
</body>
</html>
TPL;
        file_put_contents($pagePath, $pageContent);

        $template = new FileTemplate($pagePath, [
            'site' => ['title' => 'My Website'],
            'page' => [
                'title' => 'Нүүр хуудас',
                'content' => 'Тавтай морилно уу!',
                'showDate' => true,
                'date' => '2026-03-28'
            ],
            'menu' => [
                ['url' => '/', 'label' => 'Нүүр'],
                ['url' => '/about', 'label' => 'Бидний тухай'],
                ['url' => '/contact', 'label' => 'Холбоо барих']
            ]
        ]);

        $output = $template->output();
        $this->assertStringContainsString('<title>My Website - Нүүр хуудас</title>', $output);
        $this->assertStringContainsString('<h1>Нүүр хуудас</h1>', $output);
        $this->assertStringContainsString('Тавтай морилно уу!', $output);
        $this->assertStringContainsString('Огноо: 2026-03-28', $output);
    }

    public function testTemplateCachingSimulation(): void
    {
        $templatePath = $this->testDir . '/cache.html';
        file_put_contents($templatePath, '{{ content }}');

        $template1 = new FileTemplate($templatePath, ['content' => 'First']);
        $template2 = new FileTemplate($templatePath, ['content' => 'Second']);

        $this->assertEquals('First', $template1->output());
        $this->assertEquals('Second', $template2->output());
    }
}
