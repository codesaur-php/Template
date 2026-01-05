<?php

namespace codesaur\Template\Tests;

use PHPUnit\Framework\TestCase;

use codesaur\Template\MemoryTemplate;
use codesaur\Template\FileTemplate;

/**
 * Memory usage tests for template rendering.
 *
 * These tests verify that template rendering doesn't consume excessive memory,
 * especially with large templates.
 *
 * @package codesaur\Template\Tests
 */
class MemoryTest extends TestCase
{
    /**
     * Test memory usage with a large MemoryTemplate.
     * 
     * This test creates a template with many variables and checks
     * that memory usage remains reasonable.
     */
    public function testLargeMemoryTemplateMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        // Create a large template with 1000 variables
        $templateContent = '';
        $vars = [];
        
        for ($i = 0; $i < 1000; $i++) {
            $varName = 'var' . $i;
            $templateContent .= '{{ ' . $varName . ' }}';
            $vars[$varName] = 'Value' . $i;
        }
        
        // Create template and render
        $template = new MemoryTemplate($templateContent, $vars);
        $output = $template->output();
        
        // Get memory after rendering
        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Memory should be reasonable (less than 10MB for 1000 variables)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 10MB for 1000 variables');
        $this->assertNotEmpty($output);
        
        // Clean up
        unset($template, $output, $vars, $templateContent);
    }

    /**
     * Test memory usage with a large FileTemplate.
     * 
     * This test creates a large template file and checks
     * that memory usage remains reasonable when reading and rendering.
     */
    public function testLargeFileTemplateMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        // Create a large template file with 500 variables
        $templateContent = '';
        $vars = [];
        
        for ($i = 0; $i < 500; $i++) {
            $varName = 'item' . $i;
            $templateContent .= '<div>{{ ' . $varName . ' }}</div>';
            $vars[$varName] = 'Item ' . $i;
        }
        
        $filePath = sys_get_temp_dir() . '/memory_test_' . uniqid() . '.html';
        file_put_contents($filePath, $templateContent);
        
        try {
            // Create template and render
            $template = new FileTemplate($filePath, $vars);
            $output = $template->output();
            
            // Get memory after rendering
            $finalMemory = memory_get_usage(true);
            $memoryUsed = $finalMemory - $initialMemory;
            
            // Memory should be reasonable (less than 10MB for 500 variables)
            $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 10MB for 500 variables');
            $this->assertNotEmpty($output);
            
            // Clean up
            unset($template, $output, $vars, $templateContent);
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Test memory usage with very long template content.
     * 
     * This test creates a template with very long content (100KB+)
     * and checks memory usage.
     */
    public function testVeryLongTemplateContentMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        // Create a very long template (approximately 100KB)
        $templateContent = '';
        $vars = ['name' => 'TestUser', 'message' => 'Hello World'];
        
        // Repeat a pattern 10000 times to create a large template
        $pattern = '<p>Hello {{ name }}, this is a test message: {{ message }}</p>';
        for ($i = 0; $i < 10000; $i++) {
            $templateContent .= $pattern;
        }
        
        // Create template and render
        $template = new MemoryTemplate($templateContent, $vars);
        $output = $template->output();
        
        // Get memory after rendering
        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Memory should be reasonable (less than 5MB for 100KB template)
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 5MB for 100KB template');
        $this->assertNotEmpty($output);
        
        // Clean up
        unset($template, $output, $vars, $templateContent);
    }

    /**
     * Test memory usage with multiple template instances.
     * 
     * This test creates multiple template instances and checks
     * that memory usage scales reasonably.
     */
    public function testMultipleTemplateInstancesMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        $templates = [];
        $outputs = [];
        
        // Create 100 template instances
        for ($i = 0; $i < 100; $i++) {
            $templateContent = 'Template {{ number }}: {{ message }}';
            $vars = ['number' => $i, 'message' => 'Test message ' . $i];
            
            $template = new MemoryTemplate($templateContent, $vars);
            $templates[] = $template;
            $outputs[] = $template->output();
        }
        
        // Get memory after creating all templates
        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Memory should be reasonable (less than 5MB for 100 templates)
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 5MB for 100 template instances');
        $this->assertCount(100, $templates);
        $this->assertCount(100, $outputs);
        
        // Clean up
        unset($templates, $outputs);
    }

    /**
     * Test memory usage with deeply nested variables.
     * 
     * This test creates a template with deeply nested variable structures
     * and checks memory usage.
     */
    public function testDeeplyNestedVariablesMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        // Create template with deeply nested variables
        $templateContent = '';
        $vars = [];
        
        // Create 5 levels of nesting with 10 items each
        for ($level1 = 0; $level1 < 10; $level1++) {
            $level1Key = 'level1_' . $level1;
            $vars[$level1Key] = [];
            
            for ($level2 = 0; $level2 < 10; $level2++) {
                $level2Key = 'level2_' . $level2;
                $vars[$level1Key][$level2Key] = [];
                
                for ($level3 = 0; $level3 < 10; $level3++) {
                    $level3Key = 'level3_' . $level3;
                    $vars[$level1Key][$level2Key][$level3Key] = "Value_{$level1}_{$level2}_{$level3}";
                    $templateContent .= '{{ ' . $level1Key . '.' . $level2Key . '.' . $level3Key . ' }}';
                }
            }
        }
        
        // Create template and render
        $template = new MemoryTemplate($templateContent, $vars);
        $output = $template->output();
        
        // Get memory after rendering
        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Memory should be reasonable (less than 5MB for deeply nested structures)
        $this->assertLessThan(5 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 5MB for deeply nested variables');
        $this->assertNotEmpty($output);
        
        // Clean up
        unset($template, $output, $vars, $templateContent);
    }

    /**
     * Test memory usage with large FileTemplate file.
     * 
     * This test creates a very large template file (500KB+)
     * and checks memory usage when reading and rendering.
     */
    public function testLargeFileTemplateFileMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        // Create a very large template file (approximately 500KB)
        $templateContent = '';
        $vars = ['title' => 'Large Template Test', 'content' => 'Memory Test Content'];
        
        // Repeat a pattern 50000 times
        $pattern = '<div class="item"><h3>{{ title }}</h3><p>{{ content }}</p></div>';
        for ($i = 0; $i < 50000; $i++) {
            $templateContent .= $pattern;
        }
        
        $filePath = sys_get_temp_dir() . '/large_memory_test_' . uniqid() . '.html';
        file_put_contents($filePath, $templateContent);
        
        try {
            // Create template and render
            $template = new FileTemplate($filePath, $vars);
            $output = $template->output();
            
            // Get memory after rendering
            $finalMemory = memory_get_usage(true);
            $memoryUsed = $finalMemory - $initialMemory;
            
            // Memory should be reasonable (less than 15MB for 500KB file)
            $this->assertLessThan(15 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 15MB for 500KB template file');
            $this->assertNotEmpty($output);
            
            // Clean up
            unset($template, $output, $vars, $templateContent);
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Test memory usage with repeated renders of the same template.
     * 
     * This test checks that memory usage doesn't grow unbounded
     * when rendering the same template multiple times.
     */
    public function testRepeatedRendersMemoryUsage(): void
    {
        // Get initial memory usage
        $initialMemory = memory_get_usage(true);
        
        $templateContent = '';
        $vars = [];
        
        // Create template with 100 variables
        for ($i = 0; $i < 100; $i++) {
            $varName = 'var' . $i;
            $templateContent .= '{{ ' . $varName . ' }}';
            $vars[$varName] = 'Value' . $i;
        }
        
        $template = new MemoryTemplate($templateContent, $vars);
        
        // Render 1000 times
        for ($i = 0; $i < 1000; $i++) {
            $output = $template->output();
            $this->assertNotEmpty($output);
        }
        
        // Get memory after repeated renders
        $finalMemory = memory_get_usage(true);
        $memoryUsed = $finalMemory - $initialMemory;
        
        // Memory should be reasonable (less than 10MB for 1000 renders)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage should be less than 10MB for 1000 repeated renders');
        
        // Clean up
        unset($template, $output, $vars, $templateContent);
    }

    /**
     * Test memory usage comparison between MemoryTemplate and FileTemplate.
     * 
     * This test compares memory usage of MemoryTemplate vs FileTemplate
     * with the same content.
     */
    public function testMemoryTemplateVsFileTemplateMemoryUsage(): void
    {
        $templateContent = 'Hello {{ name }}, welcome to {{ place }}!';
        $vars = ['name' => 'User', 'place' => 'Template Engine'];
        
        // Test MemoryTemplate
        $initialMemory = memory_get_usage(true);
        $memoryTemplate = new MemoryTemplate($templateContent, $vars);
        $memoryOutput = $memoryTemplate->output();
        $memoryTemplateMemory = memory_get_usage(true) - $initialMemory;
        
        // Test FileTemplate
        $filePath = sys_get_temp_dir() . '/comparison_test_' . uniqid() . '.html';
        file_put_contents($filePath, $templateContent);
        
        try {
            $initialMemory = memory_get_usage(true);
            $fileTemplate = new FileTemplate($filePath, $vars);
            $fileOutput = $fileTemplate->output();
            $fileTemplateMemory = memory_get_usage(true) - $initialMemory;
            
            // Both should produce the same output
            $this->assertEquals($memoryOutput, $fileOutput);
            
            // Both should use reasonable memory (less than 1MB for small template)
            $this->assertLessThan(1024 * 1024, $memoryTemplateMemory, 'MemoryTemplate should use less than 1MB');
            $this->assertLessThan(1024 * 1024, $fileTemplateMemory, 'FileTemplate should use less than 1MB');
            
            // Clean up
            unset($memoryTemplate, $fileTemplate, $memoryOutput, $fileOutput);
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}

