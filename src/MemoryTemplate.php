<?php

namespace codesaur\Template;

/**
 * MemoryTemplate класс нь HTML эсвэл текст суурьтай энгийн темплейт
 * боловсруулах зориулалттай lightweight template engine юм.
 *
 * Онцлог:
 * - {{ key }}, {{key}}, {{   key   }} зэрэг whitespace-тай/гүй tag-уудыг танина
 * - Олон түвшний өгөгдөл (multi-level array) дэмжинэ: {{ user.profile.email }}
 * - Үндсэндээ simple replace-д суурилсан хурдан, хөнгөн ажиллагаатай
 * - String, массив зэрэг бүх төрлийн өгөгдлийг stringify хэлбэрээр буцаана
 */
class MemoryTemplate
{
    /**
     * Темплейтийн үндсэн HTML эсвэл текст эх сурвалц.
     *
     * @var string
     */
    protected string $_html;
    
    /**
     * Темплейтэд оруулах хувьсагчдын массив.
     *
     * @var array
     */
    protected array $_vars;

    /**
     * MemoryTemplate объект үүсгэх үндсэн конструктор.
     *
     * @param string $template Темплейтийн HTML эсвэл текст эхлэл утга
     * @param array  $vars     Темплейтэд ашиглах хувьсагчдын массив
     */
    public function __construct(string $template = '', array $vars = [])
    {
        $this->_html = $template;
        $this->_vars = $vars;
    }
    
    /**
     * Объектыг шууд echo хийх үед output() функцийн үр дүнг буцаана.
     *
     * @return string
     */
    public final function __toString()
    {
        return $this->output();
    }

    /**
     * Темплейтийн эх HTML/текст агуулгыг тохируулна.
     *
     * @param string $html
     * @return void
     */
    public function source(string $html)
    {
        $this->_html = $html;
    }

    /**
     * Тодорхой хувьсагч template-д байгаа эсэхийг шалгана.
     *
     * @param string $key
     * @return bool
     */
    public final function has(string $key): bool
    {
        return isset($this->getVars()[$key]);
    }

    /**
     * Темплейтэд ашиглах хувьсагчийг name=value хэлбэрээр нэмэх.
     *
     * @param string $key
     * @param mixed  $value
     * @return void
     */
    public function set(string $key, $value)
    {
        $this->_vars[$key] = $value;
    }
    
    /**
     * Нэгэн зэрэг олон хувьсагч нэмэх.
     *
     * @param array $values
     * @return void
     */
    public function setVars(array $values)
    {
        foreach ($values as $var => $value) {
            $this->set($var, $value);
        }
    }

    /**
     * Хувьсагчийн утгыг аваад reference байдлаар буцаана.
     *
     * @param string $key
     * @return mixed
     */
    public final function &get(string $key)
    {
        if ($this->has($key)) {
            return $this->_vars[$key];
        }
        
        $nulldata = null;
        return $nulldata;
    }

    /**
     * Темплейтэд ашиглаж буй бүх хувьсагчдын массивыг авах.
     *
     * @return array
     */
    public function getVars(): array
    {
        return $this->_vars;
    }

    /**
     * Темплейтийн эх HTML/текстийг буцаах.
     *
     * @return string
     */
    public function getSource(): string
    {
        return $this->_html;
    }

    /**
     * Темплейтийн tag-уудыг боловсруулж финал HTML гаргана.
     *
     * Дэмжигдэх синтакс:
     * {{ key }}
     * {{key}}
     * {{   user.profile.email   }}
     *
     * @param string $html Темплейтийн эх HTML
     * @return string Боловсруулсан финал HTML
     */
    protected function compile(string $html): string
    {
        return \preg_replace_callback(
            '/\{\{\s*([a-zA-Z0-9_\.]+)\s*\}\}/',
            function ($matches) {
                $path = $matches[1];
                $value = $this->resolveValue($path);

                return $value !== null ? $this->stringify($value) : $matches[0];
            },
            $html
        );
    }
    
    /**
     * Олон түвшний key (user.profile.email гэх мэт)-ийн утгыг мөрдөж авах.
     *
     * @param string $path "a.b.c" хэлбэртэй key path
     * @return mixed|null Олдсон утга эсвэл null
     */
    protected function resolveValue(string $path)
    {
        $segments = \explode('.', $path);
        $value = $this->_vars;

        foreach ($segments as $segment) {
            if (\is_array($value)
                && \array_key_exists($segment, $value)
            ) {
                $value = $value[$segment];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Темплейтийг рэндэрлэж echo хийнэ.
     *
     * @return void
     */
    public function render()
    {
        echo $this->output();
    }

    /**
     * Темплейтийн финал боловсруулсан HTML-г буцаана.
     *
     * @return string
     */
    public function output(): string
    {
        return $this->compile($this->getSource());
    }
    
    /**
     * Массив эсвэл дурын төрлийн өгөгдлийг текст болгон хөрвүүлэх.
     *
     * @param mixed $content
     * @return string
     */
    protected function stringify($content): string
    {
        if (\is_array($content)) {
            $text = '';
            foreach ($content as $str) {
                $text .= $this->stringify($str);
            }
            return $text;
        }

        return (string) $content;
    }
}
