<?php

namespace App\Services\Localization;

use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    /**
     * Translate text from one language to another using configured provider.
     * Falls back to source text if provider not configured or API fails.
     */
    public function translateOrFallback(string $text, string $source = 'ar', string $target = 'en'): string
    {
        $provider = strtolower(env('TRANSLATION_PROVIDER', ''));

        try {
            return match ($provider) {
                // Free provider via LibreTranslate (no API key required)
                'free' => $this->libreTranslate($text, $source, $target),
                // Unofficial free provider using Stichoza library (no API key)
                'stichoza' => $this->stichozaTranslate($text, $source, $target),
                'google' => $this->googleTranslate($text, $source, $target),
                'azure' => $this->azureTranslate($text, $source, $target),
                'deepl' => $this->deeplTranslate($text, $source, $target),
                default => $this->simpleFallback($text),
            };
        } catch (\Throwable $e) {
            return $this->simpleFallback($text);
        }
    }

    /**
     * Free translation using LibreTranslate public endpoint.
     * Configure base URL with FREE_TRANSLATE_BASE_URL or use default.
     */
    public function freeTranslateOrFallback(string $text, string $source = 'ar', string $target = 'en'): string
    {
        try {
            return $this->libreTranslate($text, $source, $target);
        } catch (\Throwable $e) {
            return $this->simpleFallback($text);
        }
    }

    protected function googleTranslate(string $text, string $source, string $target): string
    {
        $key = env('GOOGLE_TRANSLATE_API_KEY');
        if (! $key) {
            return $this->simpleFallback($text);
        }
        $response = Http::asForm()->post('https://translation.googleapis.com/language/translate/v2', [
            'q' => $text,
            'source' => $source,
            'target' => $target,
            'format' => 'text',
            'key' => $key,
        ]);
        if ($response->successful()) {
            $data = $response->json();

            return $data['data']['translations'][0]['translatedText'] ?? $this->simpleFallback($text);
        }

        return $this->simpleFallback($text);
    }

    protected function azureTranslate(string $text, string $source, string $target): string
    {
        $key = env('AZURE_TRANSLATOR_KEY');
        $region = env('AZURE_TRANSLATOR_REGION');
        if (! $key || ! $region) {
            return $this->simpleFallback($text);
        }
        $url = 'https://api.cognitive.microsofttranslator.com/translate?api-version=3.0&from='.$source.'&to='.$target;
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $key,
            'Ocp-Apim-Subscription-Region' => $region,
            'Content-Type' => 'application/json',
        ])->post($url, [
            ['Text' => $text],
        ]);
        if ($response->successful()) {
            $data = $response->json();

            return $data[0]['translations'][0]['text'] ?? $this->simpleFallback($text);
        }

        return $this->simpleFallback($text);
    }

    protected function deeplTranslate(string $text, string $source, string $target): string
    {
        $key = env('DEEPL_API_KEY');
        if (! $key) {
            return $this->simpleFallback($text);
        }
        $response = Http::asForm()->post('https://api.deepl.com/v2/translate', [
            'auth_key' => $key,
            'text' => $text,
            'source_lang' => strtoupper($source),
            'target_lang' => strtoupper($target),
        ]);
        if ($response->successful()) {
            $data = $response->json();

            return $data['translations'][0]['text'] ?? $this->simpleFallback($text);
        }

        return $this->simpleFallback($text);
    }

    /**
     * LibreTranslate free API (no key required).
     * Defaults to https://libretranslate.com, can be overridden.
     */
    protected function libreTranslate(string $text, string $source, string $target): string
    {
        $base = rtrim(env('FREE_TRANSLATE_BASE_URL', 'https://libretranslate.com'), '/');
        $url = $base.'/translate';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            'q' => $text,
            'source' => $source,
            'target' => $target,
            'format' => 'text',
        ]);
        if ($response->successful()) {
            $data = $response->json();

            return $data['translatedText'] ?? $this->simpleFallback($text);
        }

        return $this->simpleFallback($text);
    }

    /**
     * Stichoza Google Translate (unofficial) - may be rate-limited by Google.
     */
    protected function stichozaTranslate(string $text, string $source, string $target): string
    {
        $gt = new GoogleTranslate($target);
        $gt->setSource($source);
        $gt->setTarget($target);

        // Optionally: $gt->setOptions(['timeout' => 5]);
        return $gt->translate($text);
    }

    protected function simpleFallback(string $text): string
    {
        // Fallback: return source text as-is when translation not available
        return $text;
    }
}
