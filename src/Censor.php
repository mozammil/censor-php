<?php
namespace Mozammil\Censor;

use ReflectionClass;
use Mozammil\Censor\Replacers\Replacer;

class Censor
{
    /**
     * The wildcard character
     *
     * @var string
     */
    protected $wildcardCharacter = '%';

    /**
     * Replaces the given words in the string
     *
     * @param string $string
     * @param array $dictionary
     *
     * @return string
     */
    public function replace(array $dictionary = [], string $string = ''): string
    {
        $dictionary = $this->prepareDictionary($dictionary);

        $words = $this->getWordsToBeCensored($dictionary);
        $regex = $this->getRegexPattern($words);

        return preg_replace_callback($regex, function ($match) use ($dictionary) {
            return $this->replaceMatchedWord(strtolower($match[0]), $dictionary);
        }, $string);
    }

    /**
     * Sets the wildcard character
     *
     * @param string $wildcardCharacter
     *
     * @return void
     */
    public function setWildcardCharacter(string $wildcardCharacter)
    {
        $this->wildcardCharacter = $wildcardCharacter;
    }

    /**
     * Gets the wildcard character
     *
     * @return string
     */
    public function getWildcardCharacter(): string
    {
        return $this->wildcardCharacter;
    }

    /**
     * Prepares the dictionary for faster matched words lookup
     *
     * @param array $dictionary
     *
     * @return array
     */
    private function prepareDictionary(array $dictionary): array
    {
        // Resolving replacers. For faster lookups,
        // we also convert the keys to lowercase
        $dictionary = $this->resolveDictionaryReplacers(
            array_change_key_case($dictionary, CASE_LOWER)
        );

        return $this->normalizeRegexKey($dictionary);
    }

    /**
     * Replaces the matched word by using
     * the replacer
     *
     * @param string $word
     * @param mixed $replacer
     *
     * @return string
     */
    private function replaceMatchedWord($word, $dictionary): string
    {
        // Let's match against the Regex Key
        // because it can be a wildcard match
        $word = $this->getRegexKey($word, $dictionary);

        // If for some reason, we cannot look up a replacement
        // we will just return the matched word instead
        // hence not replacing it
        if(!array_key_exists($word, $dictionary)) {
            return $word;
        }

        $replacer = $dictionary[$word];

        return is_string($replacer)
            ? $replacer
            : call_user_func_array([$replacer, 'replace'], [$word]);
    }

    /**
     * Gets the egex key for the matched word
     *
     * @param string $matched
     * @param array $dictionary
     *
     * @return void
     */
    private function getRegexKey($matched, $dictionary)
    {
        foreach ($dictionary as $pattern => $replacer) {
            if (preg_match('/' . $pattern . '/', $matched)) {
                return $pattern;
            }
        }

        return false;
    }

    /**
     * Converts the keys containing wildcards to regex patterns
     * So that they can be matched later
     *
     * @param $dictionary
     *
     * @return array
     */
    private function normalizeRegexKey(array $dictionary): array
    {
        $normalized = [];
        foreach($dictionary as $pattern => $replacer) {
            $pattern = str_replace('%', '(?:[^<\s]*)', $pattern);
            $normalized[$pattern] = $replacer;
        }

        return $normalized;
    }


    /**
     * Gets the words to be censored from the dictionary.
     * We filter out non String values here, just in case
     *
     * @param array $dictionary
     *
     * @return array
     */
    private function getWordsToBeCensored(array $dictionary = []): array
    {
        return array_map(function($word) {
            return (string) $word;
        }, array_keys($dictionary));
    }

    /**
     * Resolves the dictionary replacer objects
     *
     * @param array $dictionary
     *
     * @return array
     */
    private function resolveDictionaryReplacers(array $dictionary = []): array
    {
        return array_map(function($replacer) {
            return $this->resolveReplacer($replacer);
        }, $dictionary);
    }

    /**
     * Resolves the replacers
     *
     * @param string $replacer
     *
     * @return mixed
     */
    private function resolveReplacer(string $replacer)
    {
        return $this->isReplacerInstantiable($replacer)
            ? new $replacer()
            : $replacer;
    }

    /**
     * Checks if a class exists and can be instantiated
     * as a replacer
     *
     * @param string $class
     *
     * @return boolean
     */
    private function isReplacerInstantiable(string $replacer): bool
    {
        if(!class_exists($replacer)) {
            return false;
        }

        $reflection = new ReflectionClass($replacer);

        return $reflection->isInstantiable() &&
            $reflection->implementsInterface(Replacer::class);
    }

    /**
     * Generates the regular expression to match
     * all the words in a given string
     *
     * @param array $words
     *
     * @return string
     */
    private function getRegexPattern(array $words = []): string
    {
        // Replacing the wildcard character
        // with regex pattern so that it can be matched
        $words = array_map(function($word) {
            return str_replace($this->wildcardCharacter, '(?:[^<\s]*)', $word);
        }, $words);

        return '/\b(?:' . implode('|', $words) . ')\b/ui';
    }
}