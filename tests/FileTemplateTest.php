<?php

namespace codesaur\Template\Tests;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use InvalidArgumentException;

use codesaur\Template\FileTemplate;

/**
 * FileTemplate классын unit test.
 *
 * @package codesaur\Template\Tests
 */
class FileTemplateTest extends TestCase
{
    /**
     * Тест файлын зам.
     */
    private string $testTemplatePath;

    /**
     * Тест файлын агуулга.
     */
    private string $testTemplateContent = 'Hello, {{ name }}!';

    /**
     * Test setUp: тест файл үүсгэх.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->testTemplatePath = sys_get_temp_dir() . '/test_template_' . uniqid() . '.html';
        file_put_contents($this->testTemplatePath, $this->testTemplateContent);
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
     * Файлаас template уншиж рэндэр хийх тест.
     */
    public function testFileTemplateRendering(): void
    {
        $template = new FileTemplate($this->testTemplatePath, ['name' => 'World']);
        $this->assertEquals('Hello, World!', $template->output());
    }

    /**
     * file() метод ашиглан файл зааж өгөх тест.
     */
    public function testFileMethod(): void
    {
        $template = new FileTemplate('', []);
        $template->file($this->testTemplatePath);
        $template->set('name', 'Test');
        $this->assertEquals('Hello, Test!', $template->output());
    }

    /**
     * getFileName() метод тест.
     */
    public function testGetFileNameMethod(): void
    {
        $template = new FileTemplate($this->testTemplatePath, []);
        $this->assertEquals($this->testTemplatePath, $template->getFileName());
    }

    /**
     * getFileSource() метод тест.
     */
    public function testGetFileSourceMethod(): void
    {
        $template = new FileTemplate($this->testTemplatePath, []);
        $this->assertEquals($this->testTemplateContent, $template->getFileSource());
    }

    /**
     * Олдохгүй файлын хувьд exception шидэх тест.
     */
    public function testFileNotFoundThrowsException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('not found');

        $template = new FileTemplate('/nonexistent/file.html', []);
        $template->getFileSource();
    }

    /**
     * Хоосон файл замын хувьд exception шидэх тест.
     */
    public function testEmptyFilePathThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Must provide filename');

        $template = new FileTemplate('', []);
        $template->file('');
    }

    /**
     * FileTemplate нь MemoryTemplate-ийн бүх функцүүдийг өвлөж авсан эсэхийг тест.
     */
    public function testInheritsMemoryTemplateMethods(): void
    {
        $template = new FileTemplate($this->testTemplatePath, ['name' => 'Initial']);
        $this->assertEquals('Hello, Initial!', $template->output());

        $template->set('name', 'Updated');
        $this->assertEquals('Hello, Updated!', $template->output());

        $this->assertTrue($template->has('name'));
        $this->assertEquals('Updated', $template->get('name'));
    }

    /**
     * Олон түвшний хувьсагч файл template-д ажиллах тест.
     */
    public function testNestedVariablesInFileTemplate(): void
    {
        $content = 'User: {{ user.name }}, Email: {{ user.email }}';
        $filePath = sys_get_temp_dir() . '/nested_test_' . uniqid() . '.html';
        file_put_contents($filePath, $content);

        try {
            $vars = [
                'user' => [
                    'name' => 'John',
                    'email' => 'john@example.com'
                ]
            ];
            $template = new FileTemplate($filePath, $vars);
            $this->assertEquals('User: John, Email: john@example.com', $template->output());
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Конструкторт хоосон файл зам өгөхөд exception шидэхгүй тест.
     * 
     * Хоосон файл замаар FileTemplate үүсгэх боломжтой, гэхдээ output() эсвэл
     * getFileSource() дуудахад exception шидэнэ.
     */
    public function testEmptyFilePathInConstructorDoesNotThrowException(): void
    {
        $template = new FileTemplate('', []);
        $this->assertInstanceOf(FileTemplate::class, $template);
        $this->assertEquals('', $template->getFileName());
        
        // output() дуудахад exception шидэнэ (файл заагаагүй)
        $this->expectException(RuntimeException::class);
        $template->output();
    }

    /**
     * getFileSource() метод файл заагаагүй үед exception шидэх тест.
     */
    public function testGetFileSourceThrowsExceptionWhenFileNameIsEmpty(): void
    {
        $template = new FileTemplate('', []);
        $this->assertEquals('', $template->getFileName());
        
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Must provide filename');
        $template->getFileSource();
    }

    /**
     * Файлын агуулгыг дахин унших тест (динамик өөрчлөлт).
     */
    public function testFileContentReReadOnOutput(): void
    {
        $template = new FileTemplate($this->testTemplatePath, ['name' => 'First']);
        $this->assertEquals('Hello, First!', $template->output());

        // Файлын агуулгыг өөрчлөх
        file_put_contents($this->testTemplatePath, 'Updated: {{ name }}!');
        $template->set('name', 'Second');
        $this->assertEquals('Updated: Second!', $template->output());
    }
}

