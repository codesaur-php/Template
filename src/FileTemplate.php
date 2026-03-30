<?php

namespace codesaur\Template;

/**
 * FileTemplate класс нь MemoryTemplate-ийг өргөтгөж,
 * файлын системээс template уншиж рэндэрлэх боломж олгоно.
 *
 * @package codesaur\Template
 * @author Narankhuu
 */
class FileTemplate extends MemoryTemplate
{
    /** @var string Темплейт файлын зам */
    protected string $_file = '';

    /**
     * @param string $template Темплейт файлын зам (хоосон байж болно)
     * @param array<string, mixed> $vars Темплейтэд дамжуулах хувьсагчид
     */
    public function __construct(string $template = '', array $vars = [])
    {
        parent::__construct('', $vars);

        if (!empty($template)) {
            $this->file($template);
        }
    }

    /**
     * Ашиглах темплейт файлын замыг тохируулна.
     *
     * @param string $filepath Темплейт файлын бүрэн зам
     * @throws \InvalidArgumentException Файлын нэр хоосон байвал
     */
    public function file(string $filepath): void
    {
        if (empty($filepath)) {
            throw new \InvalidArgumentException(__CLASS__ . ': Must provide filename');
        }

        $this->_file = $filepath;
    }

    /**
     * Одоогоор тохируулсан темплейт файлын замыг буцаана.
     *
     * @return string Темплейт файлын бүрэн зам
     */
    public function getFileName(): string
    {
        return $this->_file;
    }

    /**
     * Тэмплейт файлын агуулгыг уншиж буцаана.
     *
     * @return string Файлын HTML/текст агуулга
     * @throws \RuntimeException Файл заагаагүй, олдохгүй эсвэл уншихад алдаа гарвал
     */
    public function getFileSource(): string
    {
        $fileName = $this->getFileName();
        if (empty($fileName)) {
            throw new \RuntimeException(
                'Error settings of ' . __CLASS__ . ': Must provide filename!',
                500
            );
        }

        if (!\file_exists($fileName)) {
            throw new \RuntimeException("Template file [$fileName] not found!", 500);
        }

        $contents = \file_get_contents($fileName);
        if ($contents === false) {
            throw new \RuntimeException("Error getting contents of template file [$fileName]", 500);
        }

        return $contents;
    }

    /**
     * Тэмплейт файлыг уншиж compile хийн финал HTML буцаана.
     *
     * @return string Финал боловсруулсан HTML
     * @throws \RuntimeException Файл уншихад алдаа гарвал
     */
    public function output(): string
    {
        $this->source($this->getFileSource());
        return $this->compile($this->getSource());
    }
}
