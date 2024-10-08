<?php

namespace codesaur\Template;

class MemoryTemplate
{
    protected string $_html;
    
    protected array $_vars;
    
    public function __construct(string $template = '', array $vars = [])
    {
        $this->_html = $template;
        $this->_vars = $vars;
    }
    
    public final function __toString()
    {
        return $this->output();
    }

    public function source(string $html)
    {
        $this->_html = $html;
    }

    public final function has(string $key): bool
    {
        return isset($this->getVars()[$key]);
    }

    public function set(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }
    
    public function setVars(array $values)
    {
        foreach ($values as $var => $value) {
            $this->set($var, $value);
        }
    }

    public final function &get(string $key)
    {
        if ($this->has($key)) {
            return $this->_vars[$key];
        }
        
        $nulldata = null;
        return $nulldata;
    }

    public function getVars(): array
    {
        return $this->_vars;
    }

    public function getSource(): string
    {
        return $this->_html;
    }

    protected function compile(string $html): string
    {
        foreach ($this->getVars() as $key => $value) {
            $tagToReplace = "{{ $key }}";
            $html = \str_replace($tagToReplace, $this->stringify($value), $html);
        }
        
        return $html;
    }

    public function render()
    {
        echo $this->output();
    }

    public function output(): string
    {
        return $this->compile($this->getSource());
    }
    
    protected function stringify($content): string
    {
        if (\is_array($content)) {
            $text = '';
            foreach ($content as $str) {
                $text .= $this->stringify($str);
            }
            return $text;
        } else {
            return (string) $content;
        }
    }
}
