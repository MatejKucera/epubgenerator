# PHP epub generator
by Matej Kucera https://matejkucera.cz

## Installation
``composer require matejkucera/epubgenerator``

## Usage
```php
// Create generator instance
$book = new EpubBook();

// Set book info
$book->setId(671534);
$book->setTitle('Grey Wolf');
$book->setAuthor('Matej Kucera');
$book->setDescription('Lorem ipsum');
$book->setLanguage('cs');
$book->setPublisher('My Books Inc.');
$book->setSubject('Story about the Grey Wolf');
$book->setBottomNote('Written using Wordistry');
$book->setNavTitle('Navigation');

// Create chapter
$chapter = new EpubChapter();
$chapter->setTitle('First Chapter');
$chapter->addParagraph('Lorem ipsum dolor...');
$chapter->addParagraph('Lorem ipsum dolor...');

// Add the chapter to the book
$epub->addChapter($chapter);

// Save book to the file
$book->saveToFile('/var/output/book.epub');

// Save book to stream
$book->stream($stream);
```

## Disclaimer
This library is work in progress. Do not use it in any production environment.
