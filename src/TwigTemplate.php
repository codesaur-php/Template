<?php

namespace codesaur\Template;

use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;

class TwigTemplate extends FileTemplate
{
    protected Environment $_environment;
    
    function __construct(string $template = '', array $vars = [])
    {
        parent::__construct($template, $vars);
        
        $this->_environment = new Environment(new ArrayLoader(), ['autoescape' => false]);
        
        $this->addFilter(new TwigFilter('int', function ($variable)
        {
            return intval($variable);
        }));
        
        $this->addFilter(new TwigFilter('json_decode', function (string $data, $associative = true)
        {
            return json_decode($data, $associative);
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
