<?php


namespace MatejKucera\EpubGenerator;


class OrderVendor
{

    private static $instance;
    private $counter;

    private function __construct(){
        $this->counter = 1;
    }

    public static function getInstance() {
        if(!(self::$instance instanceof self)) {
            self::$instance = new OrderVendor();
        }

        return self::$instance;
    }

    public function getAndRaise() {
        return $this->counter++;
    }

}
