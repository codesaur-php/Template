<?php

namespace codesaur\Template\Tests;

use PHPUnit\Framework\TestCase;

use codesaur\Template\MemoryTemplate;
use codesaur\Template\FileTemplate;

/**
 * Performance tests for template rendering with large templates.
 *
 * @package codesaur\Template\Tests
 */
class PerformanceTest extends TestCase
{
    /**
     * Test rendering performance with a large MemoryTemplate.
     * 
     * This test creates a template with many variables and checks
     * that rendering completes within a reasonable time.
     */
    public function testLargeMemoryTemplatePerformance(): void
    {
        // Create a large template with 1000 variables
        $templateContent = '';
        $vars = [];
        
        for ($i = 0; $i < 1000; $i++) {
            $varName = 'var' . $i;
            $templateContent .= '{{ ' . $varName . ' }}';
            $vars[$varName] = 'Value' . $i;
            
            // Add some spacing every 10 variables
            if ($i % 10 === 9) {
                $templateContent .= "\n";
            }
        }
        
        $startTime = microtime(true);
        $template = new MemoryTemplate($templateContent, $vars);
        $output = $template->output();
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        
        // Assert that rendering completes (should be very fast, < 1 second)
        $this->assertLessThan(1.0, $executionTime, 'Large template rendering should complete in less than 1 second');
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Value0', $output);
        $this->assertStringContainsString('Value999', $output);
    }

    /**
     * Test rendering performance with a large FileTemplate.
     * 
     * This test creates a large template file and checks
     * that rendering completes within a reasonable time.
     */
    public function testLargeFileTemplatePerformance(): void
    {
        // Create a large template file with 500 variables
        $templateContent = '';
        $vars = [];
        
        for ($i = 0; $i < 500; $i++) {
            $varName = 'item' . $i;
            $templateContent .= '<div>{{ ' . $varName . ' }}</div>';
            $vars[$varName] = 'Item ' . $i;
            
            if ($i % 20 === 19) {
                $templateContent .= "\n";
            }
        }
        
        $filePath = sys_get_temp_dir() . '/perf_test_' . uniqid() . '.html';
        file_put_contents($filePath, $templateContent);
        
        try {
            $startTime = microtime(true);
            $template = new FileTemplate($filePath, $vars);
            $output = $template->output();
            $endTime = microtime(true);
            
            $executionTime = $endTime - $startTime;
            
            // Assert that rendering completes (should be very fast, < 1 second)
            $this->assertLessThan(1.0, $executionTime, 'Large file template rendering should complete in less than 1 second');
            $this->assertNotEmpty($output);
            $this->assertStringContainsString('Item 0', $output);
            $this->assertStringContainsString('Item 499', $output);
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    /**
     * Test performance with deeply nested variables.
     * 
     * This test creates a template with deeply nested variable structures
     * and checks rendering performance.
     */
    public function testDeeplyNestedVariablesPerformance(): void
    {
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
        
        $startTime = microtime(true);
        $template = new MemoryTemplate($templateContent, $vars);
        $output = $template->output();
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        
        // Assert that rendering completes (should be very fast, < 1 second)
        $this->assertLessThan(1.0, $executionTime, 'Deeply nested template rendering should complete in less than 1 second');
        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Value_0_0_0', $output);
        $this->assertStringContainsString('Value_9_9_9', $output);
    }

    /**
     * Test performance with very long template content.
     * 
     * This test creates a template with very long content (100KB+)
     * and checks rendering performance.
     */
    public function testVeryLongTemplateContentPerformance(): void
    {
        // Create a very long template (approximately 100KB)
        $templateContent = '';
        $vars = ['name' => 'TestUser', 'message' => 'Hello World'];
        
        // Repeat a pattern 10000 times to create a large template
        $pattern = '<p>Hello {{ name }}, this is a test message: {{ message }}</p>';
        for ($i = 0; $i < 10000; $i++) {
            $templateContent .= $pattern;
            if ($i % 100 === 99) {
                $templateContent .= "\n";
            }
        }
        
        $startTime = microtime(true);
        $template = new MemoryTemplate($templateContent, $vars);
        $output = $template->output();
        $endTime = microtime(true);
        
        $executionTime = $endTime - $startTime;
        
        // Assert that rendering completes (should be fast, < 2 seconds for very large content)
        $this->assertLessThan(2.0, $executionTime, 'Very long template rendering should complete in less than 2 seconds');
        $this->assertNotEmpty($output);
        // Output should be similar in size to template (variables replaced)
        $this->assertGreaterThan(strlen($templateContent) * 0.8, strlen($output), 'Output should be similar in size to template');
        $this->assertStringContainsString('TestUser', $output);
    }

    /**
     * Test performance with multiple sequential renders.
     * 
     * This test checks performance when rendering the same template
     * multiple times sequentially.
     */
    public function testMultipleSequentialRendersPerformance(): void
    {
        $templateContent = '';
        $vars = [];
        
        // Create template with 100 variables
        for ($i = 0; $i < 100; $i++) {
            $varName = 'var' . $i;
            $templateContent .= '{{ ' . $varName . ' }}';
            $vars[$varName] = 'Value' . $i;
        }
        
        $template = new MemoryTemplate($templateContent, $vars);
        
        $startTime = microtime(true);
        
        // Render 100 times
        for ($i = 0; $i < 100; $i++) {
            $output = $template->output();
            $this->assertNotEmpty($output);
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Assert that 100 renders complete quickly (should be < 1 second)
        $this->assertLessThan(1.0, $executionTime, '100 sequential renders should complete in less than 1 second');
    }

    /**
     * Test performance with large FileTemplate file.
     * 
     * This test creates a very large template file (500KB+)
     * and checks file reading and rendering performance.
     */
    public function testLargeFileTemplateFilePerformance(): void
    {
        // Create a very large template file (approximately 500KB)
        $templateContent = '';
        $vars = ['title' => 'Large Template Test', 'content' => 'Performance Test Content'];
        
        // Repeat a pattern 50000 times
        $pattern = '<div class="item"><h3>{{ title }}</h3><p>{{ content }}</p></div>';
        for ($i = 0; $i < 50000; $i++) {
            $templateContent .= $pattern;
            if ($i % 500 === 499) {
                $templateContent .= "\n";
            }
        }
        
        $filePath = sys_get_temp_dir() . '/large_perf_test_' . uniqid() . '.html';
        file_put_contents($filePath, $templateContent);
        
        try {
            $startTime = microtime(true);
            $template = new FileTemplate($filePath, $vars);
            $output = $template->output();
            $endTime = microtime(true);
            
            $executionTime = $endTime - $startTime;
            
            // Assert that large file rendering completes (should be < 3 seconds for very large files)
            $this->assertLessThan(3.0, $executionTime, 'Large file template rendering should complete in less than 3 seconds');
            $this->assertNotEmpty($output);
            // Output should be similar in size to template (variables replaced)
            $this->assertGreaterThan(strlen($templateContent) * 0.8, strlen($output), 'Output should be similar in size to template');
            $this->assertStringContainsString('Large Template Test', $output);
        } finally {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}

