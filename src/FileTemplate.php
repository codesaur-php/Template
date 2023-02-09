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
            return 'Error settings of ' . __CLASS__ . ': Must provide filename!';
        }
        if (!\file_exists($fileName)) {
            return "Template file [$fileName] not found!";
        }

        return \file_get_contents($fileName) ?: "Error getting contents of template file [$fileName]";
    }

    public function output(): string
    {
        $this->source($this->getFileSource());

        return $this->compile($this->getSource());
    }
}
