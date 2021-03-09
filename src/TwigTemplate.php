<?php

namespace codesaur\Template;

use Twig\Markup;
use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;

class TwigTemplate extends FileTemplate
{
    protected $_environment;
    
    function __construct(string $template = null, array $vars = null)
    {
        parent::__construct($template, $vars);
        
        $this->_environment = new Environment(new ArrayLoader(), array('autoescape' => false));
        
        $this->addFilter(new TwigFilter('int', function ($variable)
        {
            return intval($variable);
        }));
        
        $this->addFilter(new TwigFilter('json_decode', function ($data, $param = true)
        {
            return json_decode($data, $param);
        }));
 
        $this->addFunction(new TwigFunction('script', function ($src, $attr = 'defer')
        {
            $script = '<script';
            if (!empty($attr)) {
                $script .= " $attr";
            }
            $script .= ' src="' . $src . '"></script>';
            
            return new Markup($script, 'UTF-8');
        }));
        
        $this->addFunction(new TwigFunction('stylesheet', function ($href, $attr = null)
        {
            $link = '<link href="' . $href . '" rel="stylesheet" type="text/css"';
            if (!empty($attr)) {
                $link .= " $attr";
            }
            $link .= '>';
            
            return new Markup($link, 'UTF-8');
        }));
    }
    
    public function getEnvironment(): Environment
    {
        return $this->_environment;
    }

    public function addGlobal(string $name, $value)
    {
        $this->_environment->addGlobal($name, $value);
    }
    
    public function addFilter(TwigFilter $filter)
    {
        $this->_environment->addFilter($filter);
    }

    public function addFunction(TwigFunction $function)
    {
        $this->_environment->addFunction($function);
    }    

    protected function compile(string $html): string
    {
       $this->_environment->getLoader()->setTemplate('result', $html);
       return $this->_environment->render('result', $this->getVars());
    }
}
