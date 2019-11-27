<?php

namespace MatejKucera\EpubGenerator;

use Carbon\Carbon;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use PhpZip\ZipFileInterface;

class EpubBook
{

    private $id;
    private $title;
    private $author = "";
    private $description = "";
    private $publisher = "";
    private $language = "en";
    private $subject = "";
    private $navTitle = "Table of Contents";
    private $bottomNote = "";
    private $styles = null;

    private $chapters = [];

    public function __construct()
    {
    }

    public function setTitle(string $title) :void {
        $this->title = $title;
    }

    public function setAuthor(string $author) :void {
        $this->author = $author;
    }

    public function setId(string $id) :void {
        $this->id = $id;
    }

    public function setDescription(string $description) :void {
        $this->description = $description;
    }

    public function setLanguage(string $language) :void {
        $this->language = $language;
    }

    public function setPublisher(string $publisher) :void {
        $this->publisher = $publisher;
    }

    public function setSubject(string $subject) :void {
        $this->subject = $subject;
    }

    public function setNavTitle(string $navTitle) :void {
        $this->navTitle = $navTitle;
    }

    public function setBottomNote(string $bottomNote) :void {
        $this->bottomNote = $bottomNote;
    }

    public function setStyles(string $styles) :void {
        $this->styles = $styles;
    }

    public function addChapter(EpubChapter $chapter) {
        $this->chapters[] = $chapter;
    }

    public function saveToFile($path) {
        try {
            $zip = $this->buildZip();
            $zip->close();
            $zip->saveAsFile($path);

        }
        catch (ZipException $exception) {
            die;
        }
    }

    public function stream($stream) {
        try {
            $zip = $this->buildZip();
            $zip->close();
            $zip->saveAsStream($stream);

        }
        catch (ZipException $exception) {
            die;
        }
    }

    public function content() {
        try {
            $zip = $this->buildZip();
            $zip->close();
            $content = $zip->outputAsString();

        }
        catch (ZipException $exception) {
            die;
        }

        return $content;
    }

    /**
     * @return ZipFileInterface
     * @throws ZipException
     */
    private function buildZip() :ZipFileInterface{
        $zip = new ZipFile();

        $zip->addFromString('mimetype', 'application/epub+zip');

        $zip->addEmptyDir('META-INF');
        $zip->addFile('data/container.xml', 'META-INF/container.xml');

        $zip->addEmptyDir('OEBPS');


        $contentOpf = $this->buildContentOpf();
        $zip->addFromString('OEBPS/content.opf', $contentOpf);

        $titleXhtml = $this->buildTitleXhtml();
        $zip->addFromString('OEBPS/title.xhtml', $titleXhtml);

        $navXhtml = $this->buildNavXhtml();
        $zip->addFromString('OEBPS/nav.xhtml', $navXhtml);

        if($this->styles) {
            $zip->addFromString('OEBPS/styles.css', $this->styles);
        } else {
            $zip->addFile('data/defaultStyles.css', 'OEBPS/styles.css');
        }

        foreach($this->chapters as $key => $chapter) {
            $zip->addFromString('OEBPS/'.$chapter->getHash().'.xhtml', $chapter->xhtml());
        }

        $zip->setCompressionLevel(ZipFile::LEVEL_SUPER_FAST);

        return $zip;
    }

    private function buildContentOpf() {
        $spine = "";
        $manifest = "";
        foreach($this->chapters as $key => $chapter) {
            $spine .= $chapter->spineRecord()."\n";
            $manifest .= $chapter->manifestRecord()."\n";
        }

        $string = file_get_contents('data/content.opf');
        $data = [
            'title' => $this->title,
            'author' => $this->author,
            'description' => $this->description,
            'date' => Carbon::now()->format('o-m-d\TH:i:s\Z'),
            'datetime' => Carbon::now()->toDateTimeString(),
            'language' => $this->language,
            'id' => $this->id,
            'publisher' => $this->publisher,
            'year' => Carbon::now()->year,
            'subject' => $this->subject,
            'spine' => $spine,
            'manifest' => $manifest,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }

        return $string;

    }

    private function buildNavXhtml() {
        $navrecords = "";
        foreach($this->chapters as $key => $chapter) {
            $navrecords .= $chapter->navRecord()."\n";
        }

        $string = file_get_contents('data/nav.xhtml');
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'navrecords' => $navrecords,
            'navtitle' => $this->navTitle,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }

        return $string;

    }

    private function buildTitleXhtml() {
        $string = file_get_contents('data/title.xhtml');
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'bottomnote' => $this->bottomNote,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }

        return $string;

    }

    /*private function buildNavNcx() {
        $navpoints = "";
        foreach($this->chapters as $key => $chapter) {
            $navpoints .= $chapter->navRecord()."\n";
        }

        $string = file_get_contents('data/nav.ncx');
        $data = [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'navpoints' => $navpoints,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }

        return $string;

    }*/

}
