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
 *
 * @package codesaur\Template
 * @author Narankhuu
 * @since 1.0.0
 */
class MemoryTemplate
{
    /**
     * Темплейтийн үндсэн HTML эсвэл текст эх сурвалч.
     *
     * @var string
     */
    protected string $_html;
    
    /**
     * Темплейтэд оруулах хувьсагчдын массив.
     *
     * @var array<string, mixed>
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
     * Темплейтэд ашиглах хувьсагчийг name=value хэлбэрээр нэмэх эсвэл шинэчлэх.
     *
     * @param string $key   Хувьсагчийн түлхүүр (key)
     * @param mixed  $value Хувьсагчийн утга (ямар ч төрөл)
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->_vars[$key] = $value;
    }
    
    /**
     * Нэгэн зэрэг олон хувьсагч нэмэх эсвэл шинэчлэх.
     *
     * @param array<string, mixed> $values Хувьсагчдын массив
     * @return void
     */
    public function setVars(array $values): void
    {
        foreach ($values as $var => $value) {
            $this->set($var, $value);
        }
    }

    /**
     * Хувьсагчийн утгыг reference байдлаар буцаана.
     *
     * Хувьсагч олдохгүй бол null буцаана.
     *
     * @param string $key Хувьсагчийн түлхүүр
     * @return mixed Хувьсагчийн утга эсвэл null
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
     * @return array<string, mixed> Хувьсагчдын массив
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
     * - {{ key }}
     * - {{key}}
     * - {{   user.profile.email   }}
     *
     * Хувьсагч олдохгүй бол tag өөрөө үлдэнэ.
     *
     * @param string $html Темплейтийн эх HTML эсвэл текст
     * @return string Боловсруулсан финал HTML эсвэл текст
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
     * Жишээ: "user.profile.email" → $vars['user']['profile']['email']
     *
     * @param string $path "a.b.c" хэлбэртэй key path (цэгээр тусгаарлагдсан)
     * @return mixed|null Олдсон утга эсвэл null (олдохгүй бол)
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
     * Массив бол бүх элементүүдийг дараалан нэгтгэнэ.
     * Бусад төрөл бол string cast хийнэ.
     *
     * @param mixed $content Хөрвүүлэх өгөгдөл
     * @return string Текст хэлбэрт хөрвүүлсэн утга
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
