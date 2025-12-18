<?php

namespace codesaur\Template;

use Twig\TwigFilter;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\ArrayLoader;

/**
 * TwigTemplate нь FileTemplate-г өргөтгөж, Twig template engine ашиглан
 * илүү хүчин чадалтай, уян хатан темплейт боловсруулах боломжийг олгоно.
 *
 * Онцлог:
 * - Twig синтакс: {{ key }}, filters, functions, loops, blocks зэргийг дэмжинэ
 * - FileTemplate ашиглан файлын агуулгыг авч Twig орчинд рэндэрлэнэ
 * - Custom Twig filter, function, global хувьсагч нэмэх боломжтой
 * - autoescape=false (HTML дээр шууд гарах текстийг өөрөө хянах боломж)
 *
 * @package codesaur\Template
 * @author Narankhuu
 * @since 1.0.0
 */
class TwigTemplate extends FileTemplate
{
    /**
     * TwigEnvironment объект - Twig engine-г бүх тохиргоотой нь агуулна.
     *
     * @var Environment
     */
    protected Environment $_environment;

    /**
     * TwigTemplate конструктор.
     *
     * Twig Environment болон ArrayLoader үүсгэж, энгийн custom filter-үүд
     * (int, json_decode) нэмнэ.
     *
     * @param string $template Темплейтийн файлын зам (хоосон байж болно)
     * @param array<string, mixed> $vars Темплейтэд дамжуулах хувьсагчдын массив
     */
    public function __construct(string $template = '', array $vars = [])
    {
        parent::__construct($template, $vars);

        // Twig loader болон environment үүсгэх
        $this->_environment = new Environment(
            new ArrayLoader(),
            ['autoescape' => false] // Escape-ийг хэрэглэгч өөрөө хянана
        );

        // Энгийн custom filter-үүд
        $this->addFilter(new TwigFilter('int', function ($variable) {
            return \intval($variable);
        }));

        $this->addFilter(new TwigFilter('json_decode', function (string $data, $associative = true) {
            return \json_decode($data, $associative);
        }));
    }

    /**
     * Twig Environment объект буцаах.
     *
     * Environment объектыг ашиглан илүү дэлгэрэнгүй тохиргоо хийх боломжтой.
     *
     * @return Environment Twig Environment объект
     */
    public function getEnvironment(): Environment
    {
        return $this->_environment;
    }

    /**
     * Темплейтэд гадаад глобал хувьсагч нэмэх.
     *
     * Глобал хувьсагч нь бүх template-д хүртээмжтэй байна.
     *
     * @param string $name  Глобал хувьсагчийн нэр
     * @param mixed  $value Глобал хувьсагчийн утга (ямар ч төрөл)
     * @return void
     */
    public function addGlobal(string $name, $value): void
    {
        $this->_environment->addGlobal($name, $value);
    }

    /**
     * Twig-д custom filter нэмэх.
     *
     * Custom filter нэмсний дараа template-д ашиглаж болно.
     * Жишээ: {{ value|custom_filter }}
     *
     * @param TwigFilter $filter Нэмэх Twig filter объект
     * @return void
     */
    public function addFilter(TwigFilter $filter): void
    {
        $this->_environment->addFilter($filter);
    }

    /**
     * Twig-д custom function нэмэх.
     *
     * Custom function нэмсний дараа template-д ашиглаж болно.
     * Жишээ: {{ custom_function(arg1, arg2) }}
     *
     * @param TwigFunction $function Нэмэх Twig function объект
     * @return void
     */
    public function addFunction(TwigFunction $function): void
    {
        $this->_environment->addFunction($function);
    }

    /**
     * TwigTemplate-ийн үндсэн compile функц.
     *
     * FileTemplate → файлын агуулгыг уншина,
     * MemoryTemplate → compile() override хийгдэж Twig рүү дамжина.
     *
     * Энэ метод нь ArrayLoader ашиглан "result" нэртэй virtual template үүсгэж,
     * Twig-ийн render() ашиглан боловсруулна.
     *
     * @param string $html Файлаас уншсан template-ийн эх HTML
     * @return string Twig engine-ээр боловсруулсан финал HTML
     *
     * @throws \Twig\Error\LoaderError Template loader алдаа гарвал
     * @throws \Twig\Error\RuntimeError Runtime алдаа гарвал
     * @throws \Twig\Error\SyntaxError Template синтакс алдаа гарвал
     */
    protected function compile(string $html): string
    {
        // "result" нэртэй virtual template болгон бүртгэж рэндэрлэх
        /** @var ArrayLoader $loader */
        $loader = $this->_environment->getLoader();
        $loader->setTemplate('result', $html);

        return $this->_environment->render('result', $this->getVars());
    }
}
