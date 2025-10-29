<?php

namespace App\Services;

class ProfanityFilter
{
    public function filter($text)
    {
        if (empty($text)) {
            return $text;
        }
        
        $filePath = storage_path('app/profanity/en.txt');
        
        if (!file_exists($filePath)) {
            return $text;
        }
        
        $badWords = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $filteredText = $text;
        
        foreach ($badWords as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
                $replacement = str_repeat('*', strlen($word));
                $filteredText = preg_replace($pattern, $replacement, $filteredText);
            }
        }
        
        return $filteredText;
    }
}