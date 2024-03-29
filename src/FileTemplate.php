<?php

namespace codesaur\Template;

class FileTemplate extends MemoryTemplate
{
    protected string $_file = '';

    public function __construct(string $template = '', array $vars = [])
    {
        parent::__construct('', $vars);
        
        $this->file($template);
    }

    public function file(string $filepath)
    {
        if (empty($filepath)) {
            throw new \InvalidArgumentException(__CLASS__ . ': Must provide filename');
        }
        
        $this->_file = $filepath;
    }
    
    public function getFileName(): string
    {
        return $this->_file;
    }

    public function getFileSource(): string
    {
        $fileName = $this->getFileName();
        if (empty($fileName)) {
            throw new \RuntimeException('Error settings of ' . __CLASS__ . ': Must provide filename!', 500);
        }
        if (!\file_exists($fileName)) {
            throw new \RuntimeException("Template file [$fileName] not found!", 500);
        }

        return \file_get_contents($fileName) ?:
            throw new \RuntimeException("Error getting contents of template file [$fileName]", 500);
    }

    public function output(): string
    {
        $this->source($this->getFileSource());

        return $this->compile($this->getSource());
    }
}
