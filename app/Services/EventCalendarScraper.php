<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EventCalendarScraper
{
    private const SOURCE_URL = 'https://pm1.blogspot.com/p/running-event-2026.html?m=1';
    private const CACHE_KEY = 'event_calendar_2026';

    public function fetch(int $cacheHours = 12): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHours($cacheHours), function () {
            return $this->scrape();
        });
    }

    private function scrape(): array
    {
        $response = Http::timeout(12)->get(self::SOURCE_URL);
        if (!$response->ok()) {
            return [
                'ok' => false,
                'source' => self::SOURCE_URL,
                'updated' => null,
                'months' => [],
                'error' => 'Unable to fetch event calendar.',
            ];
        }

        $html = $response->body();
        $data = $this->extractMainData($html);
        $text = $data['text'];
        $links = $data['links'];
        if ($text === '') {
            return [
                'ok' => false,
                'source' => self::SOURCE_URL,
                'updated' => null,
                'months' => [],
                'error' => 'No event data found.',
            ];
        }

        return $this->parseLines($text, $links);
    }

    private function extractMainData(string $html): array
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $candidates = [
            '//*[@class="post-body entry-content"]',
            '//*[@class="post-body"]',
            '//*[@class="post-body-container"]',
            '//*[@class="post"]',
            '//*[@id="content"]',
            '//*[@id="main"]',
        ];

        foreach ($candidates as $query) {
            $nodes = $xpath->query($query);
            if ($nodes && $nodes->length > 0) {
                $node = $nodes->item(0);
                $htmlBlock = $doc->saveHTML($node);
                $text = $this->normalizeHtmlToText($htmlBlock);
                if ($text !== '') {
                    return [
                        'text' => $text,
                        'links' => $this->extractLinks($node),
                    ];
                }
            }
        }

        $fallback = $doc->textContent ?? '';
        return [
            'text' => trim(preg_replace('/\s+/', ' ', $fallback)),
            'links' => [],
        ];
    }

    private function normalizeHtmlToText(string $html): string
    {
        $html = preg_replace('/<\s*br\s*\/?>/i', "\n", $html);
        $html = preg_replace('/<\/p\s*>/i', "\n", $html);
        $html = preg_replace('/<\/div\s*>/i', "\n", $html);
        $html = preg_replace('/<\/li\s*>/i', "\n", $html);
        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    private function parseLines(string $text, array $links): array
    {
        $normalized = str_replace(["\r\n", "\r"], "\n", $text);
        // Handle missing space between year and date (e.g., "DEC 202620 Dec - ...")
        $normalized = preg_replace('/(\d{4})(\d{1,2}\s+[A-Za-z]{3}\s*-\s*)/i', "$1\n$2", $normalized);
        $normalized = preg_replace('/([A-Z]{3,9}\s+\d{4})(\d{1,2}\s+[A-Za-z]{3}\s*-\s*)/i', "$1\n$2", $normalized);
        // Force line breaks for month headings like "DEC 2026"
        $normalized = preg_replace('/\b([A-Z]{3,9}\s+\d{4})\b/', "\n$1\n", $normalized);
        // Force line breaks for date lines with month abbreviations (e.g., "20 Dec - ...")
        $normalized = preg_replace('/\b(\d{1,2}\s+[A-Za-z]{3}\s*-\s*)/i', "\n$1", $normalized);
        // Fallback for date lines without month abbreviations (e.g., "20 - ...")
        $normalized = preg_replace('/\b(\d{1,2}\s*-\s*)/', "\n$1", $normalized);

        $lines = preg_split('/\R+/', $normalized);
        $lines = array_map(static function ($line) {
            $clean = preg_replace('/\s+/', ' ', trim($line));
            return $clean ?? '';
        }, $lines);
        $lines = array_values(array_filter($lines, static fn($line) => $line !== ''));

        $months = [];
        $currentMonth = null;
        $updated = null;

        foreach ($lines as $line) {
            if (stripos($line, 'Last update') !== false) {
                if (preg_match('/^(.*?)([A-Z]{3,9}\s+\d{4}.*)$/', $line, $matches)) {
                    $updated = trim($matches[1]);
                    $line = trim($matches[2]);
                } else {
                    $updated = $line;
                    continue;
                }
            }

            if ($this->isMonthHeading($line)) {
                $currentMonth = $line;
                if (!isset($months[$currentMonth])) {
                    $months[$currentMonth] = [];
                }
                continue;
            }

            if (!$currentMonth) {
                continue;
            }

            $event = $this->parseEventLine($line);
            if ($event) {
                $event['url'] = $this->findEventUrl($event['title'], $links);
                $months[$currentMonth][] = $event;
            }
        }

        $monthList = [];
        foreach ($months as $label => $events) {
            $monthList[] = [
                'label' => $label,
                'events' => $events,
            ];
        }

        if (empty($monthList)) {
            $fallback = $this->parseByRegex($normalized);
            $monthList = $fallback['months'];
            $updated = $updated ?: $fallback['updated'];
        }

        return [
            'ok' => true,
            'source' => self::SOURCE_URL,
            'updated' => $updated,
            'months' => $monthList,
            'error' => null,
        ];
    }

    private function isMonthHeading(string $line): bool
    {
        return (bool) preg_match('/^[A-Z]{3,9}\s+\d{4}$/', $line);
    }

    private function parseEventLine(string $line): ?array
    {
        if (preg_match('/^\d{1,2}\s+[A-Za-z]{3,9}\s*-\s*(.+)$/', $line, $matches)) {
            $parts = explode('-', $line, 2);
            if (count($parts) === 2) {
                return [
                    'date' => trim($parts[0]),
                    'title' => trim($parts[1]),
                ];
            }
        }

        if (preg_match('/^\d{1,2}\s*-\s*(.+)$/', $line, $matches)) {
            $parts = explode('-', $line, 2);
            if (count($parts) === 2) {
                return [
                    'date' => trim($parts[0]),
                    'title' => trim($parts[1]),
                ];
            }
        }

        return null;
    }

    private function parseByRegex(string $text): array
    {
        $updated = null;
        if (preg_match('/Last update[^\\n]*/i', $text, $matches)) {
            $updated = trim($matches[0]);
        }

        $months = [];
        $parts = preg_split('/([A-Z]{3,9}\s+\d{4})/', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $currentMonth = null;

        foreach ($parts as $part) {
            $part = trim($part);
            if ($part === '') {
                continue;
            }
            if ($this->isMonthHeading($part)) {
                $currentMonth = $part;
                if (!isset($months[$currentMonth])) {
                    $months[$currentMonth] = [];
                }
                continue;
            }
            if (!$currentMonth) {
                continue;
            }

            $eventsText = preg_replace('/\s+/', ' ', $part);
            $eventsText = preg_replace('/(\d{4})(\d{1,2}\s+[A-Za-z]{3}\s*-\s*)/i', "$1\n$2", $eventsText);
            $eventsText = preg_replace('/\b(\d{1,2}\s+[A-Za-z]{3}\s*-\s*)/i', "\n$1", $eventsText);
            $eventsText = preg_replace('/\b(\d{1,2}\s*-\s*)/', "\n$1", $eventsText);

            $lines = preg_split('/\R+/', $eventsText);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    continue;
                }
                $event = $this->parseEventLine($line);
                if ($event) {
                    $event['url'] = $this->findEventUrl($event['title'], $links);
                    $months[$currentMonth][] = $event;
                }
            }
        }

        $monthList = [];
        foreach ($months as $label => $events) {
            if (!empty($events)) {
                $monthList[] = [
                    'label' => $label,
                    'events' => $events,
                ];
            }
        }

        return [
            'months' => $monthList,
            'updated' => $updated,
        ];
    }

    private function extractLinks(\DOMNode $node): array
    {
        $links = [];
        if (!$node->ownerDocument) {
            return $links;
        }
        $xpath = new \DOMXPath($node->ownerDocument);
        $anchors = $xpath->query('.//a', $node);
        if (!$anchors) {
            return $links;
        }
        foreach ($anchors as $anchor) {
            $text = trim($anchor->textContent ?? '');
            $href = trim($anchor->getAttribute('href') ?? '');
            if ($text === '' || $href === '') {
                continue;
            }
            $links[] = [
                'text' => $text,
                'href' => $href,
            ];
        }
        return $links;
    }

    private function findEventUrl(string $title, array $links): ?string
    {
        $normalizedTitle = $this->normalizeMatchText($title);
        if ($normalizedTitle === '') {
            return null;
        }
        foreach ($links as $link) {
            $linkText = $this->normalizeMatchText($link['text'] ?? '');
            if ($linkText === '') {
                continue;
            }
            if (str_contains($linkText, $normalizedTitle) || str_contains($normalizedTitle, $linkText)) {
                return $link['href'] ?? null;
            }
        }

        $titleTokens = $this->tokenize($normalizedTitle);
        if (empty($titleTokens)) {
            return null;
        }

        $bestHref = null;
        $bestScore = 0.0;

        foreach ($links as $link) {
            $linkText = $this->normalizeMatchText($link['text'] ?? '');
            if ($linkText === '') {
                continue;
            }
            $linkTokens = $this->tokenize($linkText);
            if (empty($linkTokens)) {
                continue;
            }

            $overlap = array_intersect($titleTokens, $linkTokens);
            $score = count($overlap) / max(1, count($titleTokens));
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestHref = $link['href'] ?? null;
            }
        }

        return $bestScore >= 0.4 ? $bestHref : null;
        return null;
    }

    private function normalizeMatchText(string $value): string
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^a-z0-9\s]/i', ' ', $value);
        $value = preg_replace('/\s+/', ' ', $value);
        return trim($value);
    }

    private function tokenize(string $value): array
    {
        $parts = preg_split('/\s+/', $value);
        $parts = array_filter($parts, static function ($token) {
            return $token !== '' && strlen($token) >= 4;
        });
        return array_values(array_unique($parts));
    }
}
