<?php

namespace codesaur\Template;

/**
 * FileTemplate класс нь MemoryTemplate-ийг өргөтгөж,
 * темплейтийг файлын системээс уншиж рэндэрлэх боломж олгоно.
 *
 * Онцлог:
 * - Файл замыг зааж өгөөд template-г файлтай нь холбож ашиглана
 * - Файл уншиж байх үед filename, file_exists, permission зэрэг алдааг шалгана
 * - FileTemplate → MemoryTemplate compile() механизмыг ашиглан финал HTML гаргана
 */
class FileTemplate extends MemoryTemplate
{
    /**
     * Рэндэрлэх гэж буй темплейт файлын бүрэн зам.
     *
     * @var string
     */
    protected string $_file = '';

    /**
     * FileTemplate конструктор.
     *
     * @param string $template Темплейт файлын зам
     * @param array  $vars     Темплейтэд дамжуулах хувьсагчдын массив
     */
    public function __construct(string $template = '', array $vars = [])
    {
        parent::__construct('', $vars);
        
        $this->file($template);
    }

    /**
     * Ашиглах темплейт файлын замыг тохируулна.
     *
     * @param string $filepath Темплейт файлын зам
     *
     * @return void
     *
     * @throws \InvalidArgumentException Файлын нэр хоосон байвал
     */
    public function file(string $filepath)
    {
        if (empty($filepath)) {
            throw new \InvalidArgumentException(__CLASS__ . ': Must provide filename');
        }

        $this->_file = $filepath;
    }

    /**
     * Одоогоор тохируулсан темплейт файлын замыг буцаана.
     *
     * @return string
     */
    public function getFileName(): string
    {
        return $this->_file;
    }

    /**
     * Тэмплейт файлын агуулгыг уншиж буцаана.
     *
     * @return string Файлын HTML/текст агуулга
     *
     * @throws \RuntimeException Файл заагаагүй, файл олдохгүй эсвэл уншихад алдаа гарвал
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

        return \file_get_contents($fileName)
            ?: throw new \RuntimeException("Error getting contents of template file [$fileName]", 500);
    }

    /**
     * Тэмплейт файлыг уншиж, MemoryTemplate-ийн compile() ашиглан финал HTML буцаана.
     *
     * @return string Финал боловсруулсан HTML
     */
    public function output(): string
    {
        $this->source($this->getFileSource());

        return $this->compile($this->getSource());
    }
}
