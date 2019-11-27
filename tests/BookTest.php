<?php

use joshtronic\LoremIpsum;
use MatejKucera\EpubGenerator\EpubChapter;
use PHPUnit\Framework\TestCase;

final class BookTest extends TestCase
{

    public function testDefaults(): void {

        $book = new \MatejKucera\EpubGenerator\EpubBook();
        $book->setAuthor('Matej Kucera');
        $book->setDescription('Lorem ipsum');
        $book->setId(671534);
        $book->setLanguage('cs');
        $book->setPublisher('Wordistry App');
        $book->setTitle('Grey Wolf');
        $book->setSubject('Story about the Grey Wolf');
        $book->setBottomNote('Written using Wordistry');
        $book->setNavTitle('Navigation');

        $lipsum = new LoremIpsum();

        for($i = 1; $i<=10; $i++) {
            $book->addChapter((new EpubChapter())
                ->setTitle(ucwords($lipsum->words(rand(2,6))))
                ->setHtml($lipsum->paragraphs(rand(10, 100), 'p')));
        }

        $book->saveToFile('output/test.epub');

        $content = $book->content();


        // TODO implement tests
        $this->assertTrue(true);

    }

}
