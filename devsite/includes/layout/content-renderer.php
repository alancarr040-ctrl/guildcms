<?php
declare(strict_types=1);

/*
 * TheRegs Content Renderer
 * Phase 3C.2 Final
 *
 * Handles:
 * - Plain text paragraph preservation
 * - Legacy BBCode converted output
 * - Mixed HTML/BBCode output
 * - Blockquote paragraph preservation
 * - Responsive media wrappers
 * - Legacy YouTube/video helpers
 */

if (!function_exists('theregs_content_contains_html')) {
    function theregs_content_contains_html(string $content): bool
    {
        return preg_match('/<\s*(p|br|div|span|ul|ol|li|blockquote|table|thead|tbody|tr|td|th|img|a|strong|em|b|i|h[1-6]|pre|code|hr|iframe|video)\b/i', $content) === 1;
    }
}

if (!function_exists('theregs_render_plaintext_content')) {
    function theregs_render_plaintext_content(string $content): string
    {
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));

        if ($content === '') {
            return '';
        }

        $paragraphs = preg_split("/\n\s*\n+/", $content) ?: [];
        $html = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            $escaped = htmlspecialchars($paragraph, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $escaped = nl2br($escaped, false);

            $html[] = '<p>' . $escaped . '</p>';
        }

        return implode("\n", $html);
    }
}

if (!function_exists('theregs_render_html_text_segment')) {
    function theregs_render_html_text_segment(string $content): string
    {
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));

        if ($content === '') {
            return '';
        }

        $paragraphs = preg_split("/\n\s*\n+/", $content) ?: [];
        $html = [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            if ($paragraph === '') {
                continue;
            }

            if (preg_match('/^\s*<(p|div|ul|ol|table|blockquote|pre|h[1-6]|hr|iframe|video)\b/i', $paragraph) === 1) {
                $html[] = $paragraph;
                continue;
            }

            $paragraph = nl2br($paragraph, false);
            $html[] = '<p>' . $paragraph . '</p>';
        }

        return implode("\n", $html);
    }
}

if (!function_exists('theregs_render_blockquote_inner')) {
    function theregs_render_blockquote_inner(string $html): string
    {
        return preg_replace_callback(
            '~(<blockquote\b[^>]*>)(.*?)(</blockquote>)~is',
            static function (array $matches): string {
                $open = $matches[1];
                $inner = trim($matches[2]);
                $close = $matches[3];

                $author = '';

                if (preg_match('~^(<div\s+class="theregs-quote-author"[^>]*>.*?</div>)(.*)$~is', $inner, $author_match)) {
                    $author = $author_match[1] . "\n";
                    $inner = trim($author_match[2]);
                }

                if ($inner === '') {
                    return $open . $author . $close;
                }

                return $open . $author . theregs_render_html_text_segment($inner) . $close;
            },
            $html
        ) ?? $html;
    }
}

if (!function_exists('theregs_extract_youtube_id')) {
    function theregs_extract_youtube_id(string $url): ?string
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        $patterns = [
            '~youtu\.be/([A-Za-z0-9_-]{6,})~i',
            '~youtube\.com/watch\?v=([A-Za-z0-9_-]{6,})~i',
            '~youtube\.com/embed/([A-Za-z0-9_-]{6,})~i',
            '~youtube\.com/shorts/([A-Za-z0-9_-]{6,})~i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $m)) {
                return $m[1];
            }
        }

        return null;
    }
}


if (!function_exists('theregs_is_safe_media_url')) {
    function theregs_is_safe_media_url(string $url): bool
    {
        $url = trim($url);

        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '/')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https'], true);
    }
}

if (!function_exists('theregs_render_media_embeds')) {
    function theregs_render_media_embeds(string $html): string
    {
        /*
         * Legacy/simple shortcodes:
         *   [youtube]https://youtu.be/...[/youtube]
         *   [youtube]VIDEO_ID[/youtube]
         *   [video]https://example/file.mp4[/video]
         */
        $html = preg_replace_callback(
            '~\[youtube\](.*?)\[/youtube\]~is',
            static function (array $matches): string {
                $source = trim(strip_tags($matches[1]));
                $id = theregs_extract_youtube_id($source) ?: preg_replace('~[^A-Za-z0-9_-]~', '', $source);

                if ($id === '') {
                    return '';
                }

                return '<div class="theregs-media-embed"><iframe src="https://www.youtube-nocookie.com/embed/' . htmlspecialchars($id, ENT_QUOTES, 'UTF-8') . '" title="YouTube video" loading="lazy" allowfullscreen></iframe></div>';
            },
            $html
        ) ?? $html;

        $html = preg_replace_callback(
            '~\[video\](.*?)\[/video\]~is',
            static function (array $matches): string {
                $source = trim(strip_tags($matches[1]));

                if ($source === '' || !theregs_is_safe_media_url($source)) {
                    return '';
                }

                $safe = htmlspecialchars($source, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

                return '<div class="theregs-media-embed theregs-video-file"><video controls preload="metadata"><source src="' . $safe . '"></video></div>';
            },
            $html
        ) ?? $html;

        /*
         * Wrap raw iframes/videos for responsiveness unless already wrapped.
         */
        $html = preg_replace(
            '~(?<!theregs-media-embed">)(<iframe\b[^>]*>.*?</iframe>)~is',
            '<div class="theregs-media-embed">$1</div>',
            $html
        ) ?? $html;

        $html = preg_replace(
            '~(?<!theregs-media-embed">)(<video\b[^>]*>.*?</video>)~is',
            '<div class="theregs-media-embed theregs-video-file">$1</div>',
            $html
        ) ?? $html;

        return $html;
    }
}

if (!function_exists('theregs_enhance_article_html')) {
    function theregs_enhance_article_html(string $html): string
    {
        $html = theregs_render_media_embeds($html);

        /*
         * Add Bootstrap/table responsiveness to plain legacy tables that are not
         * already wrapped.
         */
        if (stripos($html, '<table') !== false && stripos($html, 'table-responsive') === false) {
            $html = preg_replace(
                '~<table\b([^>]*)>~i',
                '<div class="table-responsive"><table$1 class="table table-dark table-bordered table-sm article-table">',
                $html,
                1
            ) ?? $html;

            $html = preg_replace('~</table>~i', '</table></div>', $html, 1) ?? $html;
        }

        return $html;
    }
}

if (!function_exists('theregs_render_mixed_html_content')) {
    function theregs_render_mixed_html_content(string $content): string
    {
        $content = str_replace(["\r\n", "\r"], "\n", trim($content));

        if ($content === '') {
            return '';
        }

        $content = theregs_render_blockquote_inner($content);
        $content = theregs_render_media_embeds($content);

        if (preg_match('/<\s*(p|ul|ol|table|pre|h[1-6]|iframe|video)\b/i', $content) === 1) {
            return theregs_enhance_article_html($content);
        }

        $parts = preg_split(
            '~(<blockquote\b[^>]*>.*?</blockquote>|<div\b[^>]*>.*?</div>|<ul\b[^>]*>.*?</ul>|<ol\b[^>]*>.*?</ol>|<table\b[^>]*>.*?</table>|<pre\b[^>]*>.*?</pre>|<iframe\b[^>]*>.*?</iframe>|<video\b[^>]*>.*?</video>|<hr\s*/?>)~is',
            $content,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        ) ?: [];

        $out = [];

        foreach ($parts as $part) {
            if (trim($part) === '') {
                continue;
            }

            if (preg_match('~^\s*<(blockquote|div|ul|ol|table|pre|iframe|video|hr)\b~i', $part) === 1) {
                $out[] = $part;
            } else {
                $out[] = theregs_render_html_text_segment($part);
            }
        }

        return theregs_enhance_article_html(implode("\n", $out));
    }
}

if (!function_exists('theregs_render_article_content')) {
    function theregs_render_article_content(?string $content): string
    {
        $content = (string) $content;

        if (trim($content) === '') {
            return '<div class="article-content"></div>';
        }

        if (theregs_content_contains_html($content) || stripos($content, '[youtube]') !== false || stripos($content, '[video]') !== false) {
            $body = theregs_render_mixed_html_content($content);
        } else {
            $body = theregs_render_plaintext_content($content);
        }

        return '<div class="article-content">' . "\n" . $body . "\n" . '</div>';
    }
}
