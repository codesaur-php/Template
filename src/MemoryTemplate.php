<?php

namespace codesaur\Template;

/**
 * MemoryTemplate класс нь HTML эсвэл текст суурьтай template engine юм.
 *
 * Дэмжигдэх синтакс:
 * - Output: {{ expr }}, {{ expr|filter }}, {{ expr|filter(args) }}
 * - Tags: if/elseif/else/endif, for/endfor, set, macro/endmacro
 * - Operators: ==, !=, <, >, <=, >=, and, or, not, ~, ?:, ??
 * - Tests: is defined, is empty, is null, is iterable (+ is not вариант)
 * - Loop: loop.first, loop.last, loop.index, loop.index0, loop.length
 * - Macros: macro definition, _self recursive call, from/import
 * - Literals: string, number, boolean, null, hash {}, array []
 * - Access: dot notation, bracket notation, filter chain
 * - Operators: starts with
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

    /** @var array<string, callable> Бүртгэгдсэн filter-үүд */
    protected array $filters = [];

    /** @var array<string, callable> Бүртгэгдсэн function-үүд */
    protected array $functions = [];

    /** @var array<string, array> Тодорхойлогдсон macro-ууд */
    private array $macros = [];

    /** @var string|null _self alias ({% import _self as name %}) */
    private ?string $selfAlias = null;

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

        $this->registerBuiltins();
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
     * @param string $html Темплейтийн HTML эсвэл текст агуулга
     * @return void
     */
    public function source(string $html): void
    {
        $this->_html = $html;
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
        if (isset($this->_vars[$key])) {
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
     * @return string Темплейтийн эх HTML эсвэл текст агуулга
     */
    public function getSource(): string
    {
        return $this->_html;
    }

    /**
     * Custom filter нэмэх.
     *
     * @param string $name Filter нэр (template-д |name хэлбэрээр ашиглана)
     * @param callable $callback Filter функц. Эхний аргумент нь filter-лэх утга
     */
    public function addFilter(string $name, callable $callback): void
    {
        $this->filters[$name] = $callback;
    }

    /**
     * Custom function нэмэх.
     *
     * @param string $name Function нэр (template-д name() хэлбэрээр ашиглана)
     * @param callable $callback Function
     */
    public function addFunction(string $name, callable $callback): void
    {
        $this->functions[$name] = $callback;
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
     * @return string Боловсруулсан финал HTML эсвэл текст
     */
    public function output(): string
    {
        return $this->compile($this->getSource());
    }

    // ==================== COMPILE ====================

    /**
     * Template-г compile хийх.
     *
     * @param string $html Template-ийн эх HTML
     * @return string Боловсруулсан HTML
     */
    protected function compile(string $html): string
    {
        $this->macros = [];
        $this->selfAlias = null;
        $tokens = $this->tokenize($html);
        $i = 0;
        $tree = $this->buildTree($tokens, $i);
        $ctx = $this->getVars();
        return $this->renderNodes($tree, $ctx);
    }

    // ==================== BUILT-IN FILTERS & FUNCTIONS ====================

    private function registerBuiltins(): void
    {
        $this->filters = [
            'int'           => fn($v) => (int) $v,
            'round'         => fn($v, int $p = 0) => \round((float) $v, $p),
            'number_format' => fn($v, int $d = 0, string $dp = '.', string $ts = ',') => \number_format((float) $v, $d, $dp, $ts),
            'json_encode'   => fn($v) => \json_encode($v, \JSON_UNESCAPED_UNICODE),
            'json_decode'   => fn($v, bool $assoc = true) => \json_decode((string) $v, $assoc),
            'upper'         => fn($v) => \mb_strtoupper((string) $v),
            'lower'         => fn($v) => \mb_strtolower((string) $v),
            'capitalize'    => fn($v) => \mb_strtoupper(\mb_substr((string) $v, 0, 1)) . \mb_substr((string) $v, 1),
            'nl2br'         => fn($v) => \nl2br((string) $v),
            'url_encode'    => fn($v) => \rawurlencode((string) $v),
            'raw'           => fn($v) => $v,
            'e'             => function ($v, string $s = 'html') {
                $v = (string) $v;
                return match ($s) {
                    'js' => \str_replace(
                        ['\\', "'", '"', "\n", "\r", "\t", '<', '>', '&', '/'],
                        ['\\\\', "\\'", '\\"', '\\n', '\\r', '\\t', '\\x3C', '\\x3E', '\\x26', '\\/'],
                        $v
                    ),
                    'url' => \rawurlencode($v),
                    default => \htmlspecialchars($v, \ENT_QUOTES | \ENT_SUBSTITUTE, 'UTF-8'),
                };
            },
            'date'          => fn($v, string $f = 'Y-m-d') => $v ? (new \DateTime((string) $v))->format($f) : '',
            'length'        => fn($v) => \is_array($v) ? \count($v) : \mb_strlen((string) $v),
            'keys'          => fn($v) => \is_array($v) ? \array_keys($v) : [],
            'first'         => fn($v) => \is_array($v) ? \reset($v) : \mb_substr((string) $v, 0, 1),
            'last'          => function ($v) { if (\is_array($v)) { $r = \end($v); return $r; } return \mb_substr((string) $v, -1); },
            'slice'         => fn($v, int $s, ?int $l = null) => \is_array($v) ? \array_slice($v, $s, $l) : \mb_substr((string) $v, $s, $l),
            'merge'         => fn($v, array $a) => \is_array($v) ? \array_merge($v, $a) : $v,
            'split'         => fn($v, string $d = '') => $d ? \explode($d, (string) $v) : \str_split((string) $v),
            'default'       => fn($v, $d = '') => ($v === null || $v === '' || $v === false || $v === []) ? $d : $v,
            'format'        => fn($v, ...$a) => \sprintf((string) $v, ...$a),
            'abs'           => fn($v) => \abs((float) $v),
            'trim'          => fn($v, string $chars = " \t\n\r\0\x0B") => \trim((string) $v, $chars),
            'striptags'     => fn($v, string $allowed = '') => \strip_tags((string) $v, $allowed ?: null),
            'title'         => fn($v) => \mb_convert_case((string) $v, \MB_CASE_TITLE),
            'join'          => fn($v, string $glue = '') => \is_array($v) ? \implode($glue, $v) : (string) $v,
            'reverse'       => fn($v) => \is_array($v) ? \array_reverse($v) : \strrev((string) $v),
            'sort'          => function ($v) { if (!\is_array($v)) { return $v; } \sort($v); return $v; },
            'unique'        => fn($v) => \is_array($v) ? \array_values(\array_unique($v)) : $v,
            'column'        => fn($v, string $key) => \is_array($v) ? \array_column($v, $key) : [],
            'batch'         => fn($v, int $size) => \is_array($v) ? \array_chunk($v, $size) : [],
            'values'        => fn($v) => \is_array($v) ? \array_values($v) : [],
            'replace'       => fn($v, array $map) => \strtr((string) $v, $map),
            'wordwrap'      => fn($v, int $w = 75, string $br = "\n", bool $cut = false) => \wordwrap((string) $v, $w, $br, $cut),
        ];
        $this->filters['escape'] = $this->filters['e'];

        $this->functions = [
            'attribute' => fn($o, $k) => \is_array($o) ? ($o[$k] ?? null) : null,
            'range'     => fn(int $low, int $high, int $step = 1) => \range($low, $high, $step),
            'max'       => fn(...$a) => \count($a) === 1 && \is_array($a[0]) ? \max($a[0]) : \max(...$a),
            'min'       => fn(...$a) => \count($a) === 1 && \is_array($a[0]) ? \min($a[0]) : \min(...$a),
        ];
    }

    // ==================== TOKENIZER ====================

    private function tokenize(string $html): array
    {
        $tokens = [];
        $pos = 0;
        $len = \strlen($html);

        while ($pos < $len) {
            $next = $len;
            $type = null;
            foreach (['{{' => 'print', '{%' => 'block'] as $d => $t) {
                $p = \strpos($html, $d, $pos);
                if ($p !== false && $p < $next) {
                    $next = $p;
                    $type = $t;
                }
            }

            if ($type === null) {
                if ($pos < $len) {
                    $tokens[] = ['type' => 'text', 'value' => \substr($html, $pos)];
                }
                break;
            }

            if ($next > $pos) {
                $tokens[] = ['type' => 'text', 'value' => \substr($html, $pos, $next - $pos)];
            }

            $close = $type === 'print' ? '}}' : '%}';
            $end = \strpos($html, $close, $next + 2);
            if ($end === false) {
                $tokens[] = ['type' => 'text', 'value' => \substr($html, $next)];
                break;
            }

            $tokens[] = ['type' => $type, 'value' => \trim(\substr($html, $next + 2, $end - $next - 2))];
            $pos = $end + 2;
        }

        return $tokens;
    }

    // ==================== TREE BUILDER ====================

    private function buildTree(array &$tokens, int &$i): array
    {
        $nodes = [];
        $count = \count($tokens);
        while ($i < $count) {
            $t = $tokens[$i];
            if ($t['type'] !== 'block') {
                $nodes[] = $t;
                $i++;
                continue;
            }

            $tag = $t['value'];
            if ($tag === 'endif' || $tag === 'endfor' || $tag === 'endmacro'
                || $tag === 'else' || \str_starts_with($tag, 'elseif ')) {
                return $nodes;
            }

            $i++;
            if (\str_starts_with($tag, 'if ')) {
                $nodes[] = $this->buildIf(\substr($tag, 3), $tokens, $i);
            } elseif (\str_starts_with($tag, 'for ')) {
                $nodes[] = $this->buildFor(\substr($tag, 4), $tokens, $i);
            } elseif (\str_starts_with($tag, 'set ')) {
                $nodes[] = ['type' => 'set', 'expr' => \substr($tag, 4)];
            } elseif (\str_starts_with($tag, 'macro ')) {
                $nodes[] = $this->buildMacro(\substr($tag, 6), $tokens, $i);
            } else {
                $nodes[] = ['type' => 'import', 'raw' => $tag];
            }
        }
        return $nodes;
    }

    private function buildIf(string $cond, array &$tokens, int &$i): array
    {
        $branches = [['cond' => $cond, 'body' => $this->buildTree($tokens, $i)]];
        $count = \count($tokens);

        while ($i < $count && $tokens[$i]['type'] === 'block') {
            $tag = $tokens[$i]['value'];
            if ($tag === 'endif') {
                $i++;
                break;
            }
            if (\str_starts_with($tag, 'elseif ')) {
                $i++;
                $branches[] = ['cond' => \substr($tag, 7), 'body' => $this->buildTree($tokens, $i)];
            } elseif ($tag === 'else') {
                $i++;
                $branches[] = ['cond' => null, 'body' => $this->buildTree($tokens, $i)];
                if ($i < $count && $tokens[$i]['type'] === 'block' && $tokens[$i]['value'] === 'endif') {
                    $i++;
                }
                break;
            } else {
                break;
            }
        }

        return ['type' => 'if', 'branches' => $branches];
    }

    private function buildFor(string $header, array &$tokens, int &$i): array
    {
        $inPos = \strpos($header, ' in ');
        $vars = \trim(\substr($header, 0, $inPos));
        $iter = \trim(\substr($header, $inPos + 4));
        $keyVar = null;
        $valVar = $vars;
        if (\str_contains($vars, ',')) {
            [$keyVar, $valVar] = \array_map('trim', \explode(',', $vars, 2));
        }

        $body = $this->buildTree($tokens, $i);
        if ($i < \count($tokens) && $tokens[$i]['type'] === 'block' && $tokens[$i]['value'] === 'endfor') {
            $i++;
        }

        return ['type' => 'for', 'key' => $keyVar, 'val' => $valVar, 'iter' => $iter, 'body' => $body];
    }

    private function buildMacro(string $header, array &$tokens, int &$i): array
    {
        $paren = \strpos($header, '(');
        $name = \trim(\substr($header, 0, $paren));
        $paramStr = \trim(\substr($header, $paren + 1, -1));
        $params = $paramStr !== '' ? \array_map('trim', \explode(',', $paramStr)) : [];

        $body = $this->buildTree($tokens, $i);
        if ($i < \count($tokens) && $tokens[$i]['type'] === 'block' && $tokens[$i]['value'] === 'endmacro') {
            $i++;
        }

        return ['type' => 'macro', 'name' => $name, 'params' => $params, 'body' => $body];
    }

    // ==================== RENDERER ====================

    private function renderNodes(array $nodes, array &$ctx): string
    {
        $out = '';
        foreach ($nodes as $node) {
            $out .= match ($node['type']) {
                'text'   => $node['value'],
                'print'  => (string) $this->expr($node['value'], $ctx),
                'if'     => $this->renderIf($node, $ctx),
                'for'    => $this->renderFor($node, $ctx),
                'set'    => $this->execSet($node['expr'], $ctx),
                'macro'  => $this->execMacro($node),
                'import' => $this->execImport($node['raw']),
                default  => '',
            };
        }
        return $out;
    }

    private function renderIf(array $node, array &$ctx): string
    {
        foreach ($node['branches'] as $b) {
            if ($b['cond'] === null || (bool) $this->expr($b['cond'], $ctx)) {
                return $this->renderNodes($b['body'], $ctx);
            }
        }
        return '';
    }

    private function renderFor(array $node, array &$ctx): string
    {
        $items = $this->expr($node['iter'], $ctx);
        if (!\is_iterable($items)) {
            return '';
        }
        $arr = \is_array($items) ? $items : \iterator_to_array($items);
        $total = \count($arr);
        if (!$total) {
            return '';
        }

        $save = [];
        foreach ([$node['val'], $node['key'], 'loop'] as $v) {
            if ($v !== null && \array_key_exists($v, $ctx)) {
                $save[$v] = $ctx[$v];
            }
        }

        $out = '';
        $idx = 0;
        foreach ($arr as $k => $v) {
            $ctx[$node['val']] = $v;
            if ($node['key'] !== null) {
                $ctx[$node['key']] = $k;
            }
            $ctx['loop'] = [
                'index' => $idx + 1, 'index0' => $idx,
                'first' => $idx === 0, 'last' => $idx === $total - 1,
                'length' => $total,
            ];
            $out .= $this->renderNodes($node['body'], $ctx);
            $idx++;
        }

        foreach ([$node['val'], $node['key'], 'loop'] as $v) {
            if ($v === null) {
                continue;
            }
            if (\array_key_exists($v, $save)) {
                $ctx[$v] = $save[$v];
            } else {
                unset($ctx[$v]);
            }
        }

        return $out;
    }

    private function execSet(string $raw, array &$ctx): string
    {
        $eq = \strpos($raw, '=');
        $ctx[\trim(\substr($raw, 0, $eq))] = $this->expr(\trim(\substr($raw, $eq + 1)), $ctx);
        return '';
    }

    private function execMacro(array $node): string
    {
        $this->macros[$node['name']] = $node;
        return '';
    }

    private function execImport(string $raw): string
    {
        if (\preg_match('/^import\s+_self\s+as\s+(\w+)$/', $raw, $m)) {
            $this->selfAlias = $m[1];
        }
        return '';
    }

    private function callMacro(string $name, array $args): string
    {
        if (!isset($this->macros[$name])) {
            return '';
        }
        $macro = $this->macros[$name];
        $ctx = [];
        foreach ($macro['params'] as $i => $param) {
            $ctx[$param] = $args[$i] ?? null;
        }
        return $this->renderNodes($macro['body'], $ctx);
    }

    // ==================== EXPRESSION PARSER ====================

    private function expr(string $s, array &$ctx): mixed
    {
        $p = 0;
        return $this->pTernary($s, $p, $ctx);
    }

    private function pTernary(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pNullCoalesce($s, $p, $ctx);
        $this->ws($s, $p);
        if ($p < \strlen($s) && $s[$p] === '?') {
            if ($p + 1 < \strlen($s) && $s[$p + 1] === '?') {
                return $left;
            }
            if ($p + 1 < \strlen($s) && $s[$p + 1] === ':') {
                $p += 2;
                return $left ?: $this->pTernary($s, $p, $ctx);
            }
            $p++;
            $then = $this->pTernary($s, $p, $ctx);
            $this->ws($s, $p);
            if ($p < \strlen($s) && $s[$p] === ':') {
                $p++;
                return $left ? $then : $this->pTernary($s, $p, $ctx);
            }
            return $left ? $then : '';
        }
        return $left;
    }

    private function pNullCoalesce(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pOr($s, $p, $ctx);
        while ($this->mStr($s, $p, '??')) {
            $left = $left ?? $this->pOr($s, $p, $ctx);
        }
        return $left;
    }

    private function pOr(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pAnd($s, $p, $ctx);
        while ($this->mWord($s, $p, 'or')) {
            $left = ((bool) $left) || ((bool) $this->pAnd($s, $p, $ctx));
        }
        return $left;
    }

    private function pAnd(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pNot($s, $p, $ctx);
        while ($this->mWord($s, $p, 'and')) {
            $left = ((bool) $left) && ((bool) $this->pNot($s, $p, $ctx));
        }
        return $left;
    }

    private function pNot(string $s, int &$p, array &$ctx): mixed
    {
        if ($this->mWord($s, $p, 'not')) {
            return !((bool) $this->pNot($s, $p, $ctx));
        }
        return $this->pCompare($s, $p, $ctx);
    }

    private function pCompare(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pConcat($s, $p, $ctx);
        $this->ws($s, $p);

        if ($this->mWord($s, $p, 'is')) {
            $neg = $this->mWord($s, $p, 'not');
            $this->ws($s, $p);
            $test = $this->rIdent($s, $p);
            $r = match ($test) {
                'empty'        => empty($left),
                'null', 'none' => $left === null,
                'defined'      => $left !== null,
                'iterable'     => \is_iterable($left),
                default        => false,
            };
            return $neg ? !$r : $r;
        }

        if ($this->mStr($s, $p, '==')) { return $left == $this->pConcat($s, $p, $ctx); }
        if ($this->mStr($s, $p, '!=')) { return $left != $this->pConcat($s, $p, $ctx); }
        if ($this->mStr($s, $p, '>=')) { return $left >= $this->pConcat($s, $p, $ctx); }
        if ($this->mStr($s, $p, '<=')) { return $left <= $this->pConcat($s, $p, $ctx); }

        $this->ws($s, $p);
        $len = \strlen($s);
        if ($p < $len) {
            if ($s[$p] === '>' && ($p + 1 >= $len || $s[$p + 1] !== '=')) {
                $p++;
                return $left > $this->pConcat($s, $p, $ctx);
            }
            if ($s[$p] === '<' && ($p + 1 >= $len || $s[$p + 1] !== '=')) {
                $p++;
                return $left < $this->pConcat($s, $p, $ctx);
            }
        }

        $sp = $p;
        if ($this->mWord($s, $p, 'starts') && $this->mWord($s, $p, 'with')) {
            $right = $this->pConcat($s, $p, $ctx);
            return \is_string($left) && \is_string($right) && \str_starts_with($left, $right);
        }
        $p = $sp;

        return $left;
    }

    private function pConcat(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pAdd($s, $p, $ctx);
        $this->ws($s, $p);
        while ($p < \strlen($s) && $s[$p] === '~') {
            $p++;
            $left = (string) $left . (string) $this->pAdd($s, $p, $ctx);
            $this->ws($s, $p);
        }
        return $left;
    }

    private function pAdd(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pMul($s, $p, $ctx);
        $this->ws($s, $p);
        while ($p < \strlen($s) && ($s[$p] === '+' || $s[$p] === '-') && ($p + 1 >= \strlen($s) || $s[$p + 1] !== '}')) {
            $op = $s[$p];
            $p++;
            $right = $this->pMul($s, $p, $ctx);
            $left = $op === '+' ? $left + $right : $left - $right;
            $this->ws($s, $p);
        }
        return $left;
    }

    private function pMul(string $s, int &$p, array &$ctx): mixed
    {
        $left = $this->pPostfix($s, $p, $ctx);
        $this->ws($s, $p);
        while ($p < \strlen($s) && ($s[$p] === '*' || $s[$p] === '/' || $s[$p] === '%')) {
            $op = $s[$p];
            $p++;
            $right = $this->pPostfix($s, $p, $ctx);
            $left = match ($op) {
                '*' => $left * $right,
                '/' => $right != 0 ? $left / $right : 0,
                '%' => $right != 0 ? $left % $right : 0,
            };
            $this->ws($s, $p);
        }
        return $left;
    }

    private function pPostfix(string $s, int &$p, array &$ctx): mixed
    {
        $val = $this->pPrimary($s, $p, $ctx);
        $len = \strlen($s);

        while ($p < $len) {
            $this->ws($s, $p);
            if ($p >= $len) {
                break;
            }

            if ($s[$p] === '|') {
                $p++;
                $this->ws($s, $p);
                $name = $this->rIdent($s, $p);
                $args = [];
                $this->ws($s, $p);
                if ($p < $len && $s[$p] === '(') {
                    $args = $this->rArgs($s, $p, $ctx);
                }
                if (isset($this->filters[$name])) {
                    $val = ($this->filters[$name])($val, ...$args);
                }
            } elseif ($s[$p] === '.') {
                $p++;
                $prop = $this->rIdent($s, $p);
                $this->ws($s, $p);
                if ($p < $len && $s[$p] === '(') {
                    $args = $this->rArgs($s, $p, $ctx);
                    $val = ($val === '__self__') ? $this->callMacro($prop, $args) : null;
                } else {
                    if ($val === '__self__') {
                        $val = null;
                    } elseif (\is_array($val)) {
                        $val = $val[$prop] ?? null;
                    } elseif (\is_object($val)) {
                        $val = $val->$prop ?? null;
                    } else {
                        $val = null;
                    }
                }
            } elseif ($s[$p] === '[') {
                $p++;
                $key = $this->pTernary($s, $p, $ctx);
                $this->ws($s, $p);
                if ($p < $len && $s[$p] === ']') {
                    $p++;
                }
                $val = \is_array($val) ? ($val[$key] ?? null) : null;
            } else {
                break;
            }
        }

        return $val;
    }

    private function pPrimary(string $s, int &$p, array &$ctx): mixed
    {
        $this->ws($s, $p);
        $len = \strlen($s);
        if ($p >= $len) {
            return null;
        }

        $ch = $s[$p];

        if ($ch === "'" || $ch === '"') { return $this->rString($s, $p); }
        if (\ctype_digit($ch)) { return $this->rNumber($s, $p); }

        if ($ch === '(') {
            $p++;
            $val = $this->pTernary($s, $p, $ctx);
            $this->ws($s, $p);
            if ($p < $len && $s[$p] === ')') { $p++; }
            return $val;
        }

        if ($ch === '{') { return $this->rHash($s, $p, $ctx); }
        if ($ch === '[') { return $this->rArray($s, $p, $ctx); }

        $ident = $this->rIdent($s, $p);
        if ($ident === '') { return null; }

        if ($ident === 'true') { return true; }
        if ($ident === 'false') { return false; }
        if ($ident === 'null' || $ident === 'none') { return null; }

        if ($ident === '_self' || ($this->selfAlias !== null && $ident === $this->selfAlias)) {
            return '__self__';
        }

        $this->ws($s, $p);
        if ($p < $len && $s[$p] === '(') {
            $args = $this->rArgs($s, $p, $ctx);
            if (isset($this->macros[$ident])) { return $this->callMacro($ident, $args); }
            if (isset($this->functions[$ident])) { return ($this->functions[$ident])(...$args); }
            return null;
        }

        return $ctx[$ident] ?? null;
    }

    // ==================== PARSE HELPERS ====================

    private function ws(string $s, int &$p): void
    {
        $len = \strlen($s);
        while ($p < $len && ($s[$p] === ' ' || $s[$p] === "\t" || $s[$p] === "\n" || $s[$p] === "\r")) {
            $p++;
        }
    }

    private function mWord(string $s, int &$p, string $word): bool
    {
        $this->ws($s, $p);
        $wl = \strlen($word);
        $sl = \strlen($s);
        if ($p + $wl <= $sl && \substr($s, $p, $wl) === $word) {
            $after = $p + $wl;
            if ($after >= $sl || (!\ctype_alnum($s[$after]) && $s[$after] !== '_')) {
                $p = $after;
                return true;
            }
        }
        return false;
    }

    private function mStr(string $s, int &$p, string $str): bool
    {
        $this->ws($s, $p);
        $sl = \strlen($str);
        if (\substr($s, $p, $sl) === $str) {
            $p += $sl;
            return true;
        }
        return false;
    }

    private function rIdent(string $s, int &$p): string
    {
        $start = $p;
        $len = \strlen($s);
        while ($p < $len && (\ctype_alnum($s[$p]) || $s[$p] === '_')) {
            $p++;
        }
        return \substr($s, $start, $p - $start);
    }

    private function rString(string $s, int &$p): string
    {
        $quote = $s[$p];
        $p++;
        $result = '';
        $len = \strlen($s);
        while ($p < $len && $s[$p] !== $quote) {
            if ($s[$p] === '\\' && $p + 1 < $len) {
                $p++;
                $result .= match ($s[$p]) {
                    'n' => "\n", 'r' => "\r", 't' => "\t", '\\' => '\\',
                    default => $s[$p],
                };
            } else {
                $result .= $s[$p];
            }
            $p++;
        }
        if ($p < $len) { $p++; }
        return $result;
    }

    private function rNumber(string $s, int &$p): int|float
    {
        $start = $p;
        $len = \strlen($s);
        while ($p < $len && (\ctype_digit($s[$p]) || $s[$p] === '.')) {
            $p++;
        }
        $num = \substr($s, $start, $p - $start);
        return \str_contains($num, '.') ? (float) $num : (int) $num;
    }

    private function rArgs(string $s, int &$p, array &$ctx): array
    {
        $p++;
        $args = [];
        $this->ws($s, $p);
        if ($p < \strlen($s) && $s[$p] === ')') { $p++; return $args; }

        $args[] = $this->pTernary($s, $p, $ctx);
        $this->ws($s, $p);
        while ($p < \strlen($s) && $s[$p] === ',') {
            $p++;
            $args[] = $this->pTernary($s, $p, $ctx);
            $this->ws($s, $p);
        }
        if ($p < \strlen($s) && $s[$p] === ')') { $p++; }
        return $args;
    }

    private function rHash(string $s, int &$p, array &$ctx): array
    {
        $p++;
        $hash = [];
        $this->ws($s, $p);
        $len = \strlen($s);
        if ($p < $len && $s[$p] === '}') { $p++; return $hash; }

        while ($p < $len) {
            $this->ws($s, $p);
            $key = ($s[$p] === "'" || $s[$p] === '"') ? $this->rString($s, $p) : $this->rIdent($s, $p);
            $this->ws($s, $p);
            if ($p < $len && $s[$p] === ':') { $p++; }
            $hash[$key] = $this->pTernary($s, $p, $ctx);
            $this->ws($s, $p);
            if ($p < $len && $s[$p] === ',') { $p++; } else { break; }
        }
        $this->ws($s, $p);
        if ($p < $len && $s[$p] === '}') { $p++; }
        return $hash;
    }

    private function rArray(string $s, int &$p, array &$ctx): array
    {
        $p++;
        $arr = [];
        $this->ws($s, $p);
        $len = \strlen($s);
        if ($p < $len && $s[$p] === ']') { $p++; return $arr; }

        $arr[] = $this->pTernary($s, $p, $ctx);
        $this->ws($s, $p);
        while ($p < $len && $s[$p] === ',') {
            $p++;
            $arr[] = $this->pTernary($s, $p, $ctx);
            $this->ws($s, $p);
        }
        if ($p < $len && $s[$p] === ']') { $p++; }
        return $arr;
    }
}
