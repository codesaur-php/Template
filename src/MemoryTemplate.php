<?php

namespace codesaur\Template;

class MemoryTemplate
{
    protected $_html = '';
    protected $_vars = array();
    
    function __construct(?string $template = null, ?array $vars = null)
    {
        if (isset($template)) {
            $this->source($template);
        }

        if (isset($vars)) {
            $this->setVars($vars);
        }
    }
    
    final public function __toString()
    {
        return $this->output();
    }

    public function source($html)
    {
        $this->_html = (string) $html;
    }

    final public function has(string $key)
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

    final public function &get(string $key)
    {
        if ($this->has($key)) {
            return $this->_vars[$key];
        }
        
        if (defined('CODESAUR_DEVELOPMENT')
                && CODESAUR_DEVELOPMENT
        ) {
            error_log("TEMPLATE KEY NOT DEFINED: $key");
        }        
        
        $nulldata = null;
        return $nulldata;
    }

    public function getVars(): array
    {
        return $this->_vars;
    }

    public function getSource()
    {
        return $this->_html;
    }

    protected function compile(string $html): string
    {
        foreach ($this->getVars() as $key => $value) {
            $tagToReplace = "{@$key}";
            $html = str_replace($tagToReplace, isset($value) ? $this->stringify($value) : '', $html);
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
    
    function stringify($content): string
    {
        if (is_array($content)) {
            $text = '';
            foreach ($content as $str) {
                $text .= $this->stringify($str);
            }
            return $text;
        } else {
            return (string)$content;
        }
    }
}
