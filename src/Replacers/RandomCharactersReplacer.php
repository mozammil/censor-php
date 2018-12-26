<?php
namespace Mozammil\Censor\Replacers;

class RandomCharactersReplacer implements Replacer
{
    /**
     * @inheritDoc
     *
     * In this particular case, the word is
     * being replaced by 3 hashes
     *
     * @param string $word
     *
     * @return string
     */
    public function replace(string $word) : string
    {
        return '@)%#!#^&*';
    }
}