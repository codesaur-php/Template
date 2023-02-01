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
        try {
            $fileName = $this->getFileName();
            if (empty($fileName)) {
                throw new \Exception('Error settings of ' . __CLASS__);
            }

            if (!\file_exists($fileName)) {
                throw new \Exception("Error loading template file [$fileName]");
            }

            return \file_get_contents($fileName) ?: '';
        } catch (\Throwable $th) {
            if (defined('CODESAUR_DEVELOPMENT')
                    && CODESAUR_DEVELOPMENT
            ) {
                \error_log($th->getMessage());
            }
            
            return $th->getMessage();
        }
    }

    public function output(): string
    {
        $this->source($this->getFileSource());

        return $this->compile($this->getSource());
    }
}
