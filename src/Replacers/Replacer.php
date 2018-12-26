<?php
namespace Mozammil\Censor\Replacers;

interface Replacer
{
    /**
     * Replaces a given word with anything
     *
     * @param string $word
     *
     * @return string
     */
    public function replace(string $word): string;
}