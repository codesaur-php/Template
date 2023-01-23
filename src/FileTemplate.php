<?php

namespace codesaur\Template;

use codesaur\Http\Message\OutputBuffer;

class FileTemplate extends MemoryTemplate
{
    protected string $_file = '';

    function __construct(string $template = '', array $vars = [])
    {
        parent::__construct('', $vars);
        
        $this->file($template);
    }

    public function file(string $filepath)
    {
        if (empty($filepath)
            || \ctype_space($filepath)
        ) {
            return;
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
            return 'Error settings of Template';
        }

        if (!file_exists($fileName)) {
            $error = "Error loading template file ($fileName)";
            
            if (defined('CODESAUR_DEVELOPMENT')
                    && CODESAUR_DEVELOPMENT
            ) {
                error_log($error);
            }

            return $error;
        }

        $buffer = new OutputBuffer();
        $buffer->start();

        include($fileName);

        $fileSource = $buffer->getContents();

        $buffer->endClean();
        
        return (string) $fileSource;
    }

    public function output(): string
    {
        $this->source($this->getFileSource());

        return $this->compile($this->getSource());
    }
}
