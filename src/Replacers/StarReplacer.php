<?php
namespace Mozammil\Censor\Replacers;

class StarReplacer implements Replacer
{
    /**
     * @inheritDoc
     *
     * In this particular case, the word is
     * being replaced by stars matching the
     * length of the word
     *
     * @param string $word
     *
     * @return string
     */
    public function replace(string $word): string
    {
        return str_repeat('*', strlen($word));
    }
}