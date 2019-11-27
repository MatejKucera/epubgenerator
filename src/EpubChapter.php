<?php

namespace MatejKucera\EpubGenerator;

use Carbon\Carbon;
use PHLAK\StrGen\CharSet;
use PHLAK\StrGen\Generator;

class EpubChapter
{

    private $html;
    private $title;
    private $hash;

    public function __construct(string $title = null, string $html = null, int $order = null)
    {
        $this->title = $title;
        $this->html = $html;

        //$this->hash = substr(sha1(time().microtime().rand(1000000000,999999999)), 0, 8);

        $generator = new Generator();
        $this->hash = $generator->charset(CharSet::ALPHA_NUMERIC)->length(16)->generate();
    }

    public function setTitle(string $title) :self {
        $this->title = $title;
        return $this;
    }

    public function setHtml(string $html) :self {
        $this->html = $html;
        return $this;
    }

    public function getHash() :string {
        return $this->hash;
    }

    public function xhtml() :string {
        $string = file_get_contents(__DIR__.'/../data/chapter.xhtml');
        $data = [
            'id' => $this->hash,
            'title' => $this->title,
            'content' => $this->html,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }
        return $string;
    }

    public function manifestRecord() :string {
        return '<item id="uuid-'.$this->hash.'" href="'.$this->hash.'.xhtml" media-type="application/xhtml+xml" />';
    }

    public function spineRecord() :string {
        return '<itemref idref="uuid-'.$this->hash.'" linear="yes" />';
    }

    public function navRecord() :string {
        $string = file_get_contents(__DIR__.'/../data/navrecord.xhtml');
        $data = [
            'id' => $this->hash,
            'title' => $this->title,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }
        return $string;
    }
/*
    public function ncxRecord() :string {
        $string = file_get_contents('data/navpoints.ncx');
        $data = [
            'id' => $this->hash,
            'label' => $this->title,
            'order' => $this->order,
        ];
        foreach($data as $key => $value) {
            $string = str_replace(strtoupper('%'.$key.'%'), $value, $string);
        }
        return $string;
    }*/



}
