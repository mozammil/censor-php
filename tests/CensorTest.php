<?php

use PHPUnit\Framework\TestCase;
use Mozammil\Censor\Censor;
use Mozammil\Censor\Replacers\StarReplacer;
use Mozammil\Censor\Replacers\HashReplacer;
use Mozammil\Censor\Replacers\RandomCharactersReplacer;

class CensorTest extends TestCase
{
    public function test_it_can_set_a_wildcard_character()
    {
        $censor = new Censor();
        $censor->setWildcardCharacter('*');

        $property = new \ReflectionProperty($censor, 'wildcardCharacter');
        $property->setAccessible(true);

        $this->assertEquals('*', $property->getValue($censor));
    }

    public function test_it_can_get_a_wildcard_character()
    {
        $censor = new Censor();

        $this->assertEquals('%', $censor->getWildcardCharacter());

        $censor->setWildcardCharacter('*');
        $this->assertEquals('*', $censor->getWildcardCharacter());
    }

    public function test_it_can_perform_replacements_by_other_words_in_a_string()
    {
        $censor = new Censor();

        $data = [
            'Wordpress' => 'WordPress',
            'blog%' => 'blogs',
            'May 27, 2003' => 'June 25, 2006'
        ];

        $string = file_get_contents(__DIR__.'/data/wordpress.txt');
        $expected = file_get_contents(__DIR__ . '/data/wordpress_censored.txt');

        return $this->assertSame($expected, $censor->replace($data, $string));
    }

    public function test_it_can_perform_replacements_by_replacers_in_a_string()
    {
        $censor = new Censor();

        $data = [
            'Wordpress' => 'WordPress',
            'blogging' => StarReplacer::class,
            '2003' => RandomCharactersReplacer::class
        ];

        $string = file_get_contents(__DIR__ . '/data/wordpress.txt');
        $expected = file_get_contents(__DIR__ . '/data/wordpress_censored_replacers.txt');

        return $this->assertSame($expected, $censor->replace($data, $string));
    }

    public function test_it_can_perform_replacements_in_html_string()
    {
        $censor = new Censor();

        $data = [
            'random' => StarReplacer::class,
            'subtitle' => HashReplacer::class,
            'text' => 'stuff'
        ];

        $string = file_get_contents(__DIR__ . '/data/index.html');
        $expected = file_get_contents(__DIR__ . '/data/index_censored.html');

        return $this->assertSame($expected, $censor->replace($data, $string));
    }

    public function test_it_can_perform_replacements_in_json()
    {
        $censor = new Censor();

        $data = [
            'Java%' => 'PHP'
        ];

        $string = file_get_contents(__DIR__ . '/data/test.json');
        $expected = file_get_contents(__DIR__ . '/data/test_censored.json');

        return $this->assertSame($expected, $censor->replace($data, $string));
    }

    public function test_it_can_perform_replacements_using_wildcards()
    {
        $censor = new Censor();

        $data = [
            'Word%' => 'WordPress',
            'mail%' => 'email',
            '%net' => 'web'
        ];

        $string = file_get_contents(__DIR__ . '/data/wordpress.txt');
        $expected = file_get_contents(__DIR__ . '/data/wordpress_censored_wildcard.txt');

        return $this->assertSame($expected, $censor->replace($data, $string));
    }
}