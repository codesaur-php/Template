<?php

namespace codesaur\Template;

class IndexTemplate extends TwigTemplate
{
    function __construct(string $template = null, array $vars = null)
    {
        parent::__construct($template, $vars);
                
        $this->set('meta', array());
    }
    
    public function title($value)
    {
        if (!empty($value)) {
            $this->get('meta')['title'] = $value;
        }
        
        return $this;
    }

    public function addContent($content)
    {
        if ($this->has('content')) {
            $contents = $this->get('content');
            if (!is_array($contents)) {
                $contents = array($contents);
            }
        } else {
            $contents = array();
        }
        
        $contents[] = $content;
        $this->set('content', $contents);
    }
    
    public function render($content = null)
    {
        if (!empty($content)) {
            $this->addContent($content);
        }
        
        if ($this->has('content')) {
            $this->set('content', $this->stringify($this->get('content')));
        }
        
        parent::render();
    }
}
