<?php
declare(strict_types=1);


require_once __DIR__ . '/../../layout/framework-helpers.php';
/*
 * The Reg's Allegiance Manager
 * Public wrapper for the legacy ACGM CGI backend.
 *
 * Public page: /ac/tree.php
 * Backend engine: /cgi-bin/acgm/server.cgi
 *
 * This file keeps the old CGI as the data engine while making tree.php
 * responsible for the public tree and character pages.
 */

global $request;

$acgm_endpoint = 'https://www.theregs.org/cgi-bin/acgm/server.cgi';
$public_tree_url = '/ac/tree.php';

function acgm_request_value(string $name, string $default = ''): string
{
    global $request;

    if (isset($request) && is_object($request) && method_exists($request, 'variable')) {
        return trim((string) $request->variable($name, $default));
    }

    $value = filter_input(INPUT_GET, $name, FILTER_UNSAFE_RAW);
    return is_string($value) ? trim($value) : $default;
}

function acgm_request_method(): string
{
    global $request;

    if (isset($request) && is_object($request) && method_exists($request, 'server')) {
        return strtoupper((string) $request->server('REQUEST_METHOD', 'GET'));
    }

    $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_UNSAFE_RAW);
    return is_string($method) ? strtoupper($method) : 'GET';
}

function acgm_post_data(): array
{
    $data = filter_input_array(INPUT_POST, FILTER_UNSAFE_RAW);

    if (!is_array($data)) {
        return [];
    }

    $clean = [];
    foreach ($data as $key => $value) {
        if (!is_string($key)) {
            continue;
        }

        if (is_array($value)) {
            $clean[$key] = array_map(static fn($v): string => is_scalar($v) ? (string) $v : '', $value);
        } else {
            $clean[$key] = is_scalar($value) ? (string) $value : '';
        }
    }

    return $clean;
}

function acgm_fetch(string $url, array $post_data = []): string
{
    $options = [
        'http' => [
            'method' => empty($post_data) ? 'GET' : 'POST',
            'timeout' => 12,
            'ignore_errors' => true,
            'header' => [
                'User-Agent: TheRegs-ACGM-Tree-Wrapper/1.0',
            ],
        ],
    ];

    if (!empty($post_data)) {
        $body = http_build_query($post_data, '', '&', PHP_QUERY_RFC3986);
        $options['http']['header'][] = 'Content-Type: application/x-www-form-urlencoded';
        $options['http']['header'][] = 'Content-Length: ' . strlen($body);
        $options['http']['content'] = $body;
    }

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    return is_string($result) ? $result : '';
}

function acgm_extract_body(string $html): string
{
    if (preg_match('~<body[^>]*>(.*?)</body>~is', $html, $match)) {
        return trim($match[1]);
    }

    return trim($html);
}


function acgm_extract_tree_table(string $html): string
{
    if (preg_match('~<table\b[^>]*class=("|\')?[^>"\']*\btree\b[^>"\']*\1?[^>]*>.*?</table>~is', $html, $match)) {
        return trim($match[0]);
    }

    return trim($html);
}

function acgm_extract_member_count(string $html): string
{
    if (preg_match('~([0-9,]+)\s+Members\s+Listed~i', $html, $match)) {
        return $match[1];
    }

    return '';
}

function acgm_is_modern_character_html(string $html): bool
{
    return stripos($html, 'class="acgm-page"') !== false || stripos($html, "class='acgm-page'") !== false;
}

function acgm_rewrite_links(string $html, string $endpoint, string $public_url): string
{
    $endpoint_pattern = preg_quote($endpoint, '~');
    $endpoint_relative_pattern = preg_quote('/cgi-bin/acgm/server.cgi', '~');

    $rewrite_character_link = static function (array $match) use ($public_url): string {
        $quote = $match[1];
        $query = html_entity_decode($match[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        parse_str($query, $params);

        if (isset($params['id'])) {
            return 'href=' . $quote . $public_url . '?id=' . rawurlencode((string) $params['id']) . $quote;
        }

        return 'href=' . $quote . $public_url . $quote;
    };

    $html = preg_replace_callback(
        '~href=(\'|\")' . $endpoint_pattern . '\?([^\'\"]*)(\1)~i',
        static function (array $m) use ($rewrite_character_link): string {
            return $rewrite_character_link([$m[0], $m[1], $m[2]]);
        },
        $html
    ) ?? $html;

    $html = preg_replace_callback(
        '~href=(\'|\")' . $endpoint_relative_pattern . '\?([^\'\"]*)(\1)~i',
        static function (array $m) use ($rewrite_character_link): string {
            return $rewrite_character_link([$m[0], $m[1], $m[2]]);
        },
        $html
    ) ?? $html;

    $html = preg_replace('~href=(\'|\")' . $endpoint_pattern . '\1~i', 'href=$1' . $public_url . '$1', $html) ?? $html;
    $html = preg_replace('~href=(\'|\")' . $endpoint_relative_pattern . '\1~i', 'href=$1' . $public_url . '$1', $html) ?? $html;
    $html = preg_replace('~action=(\'|\")' . $endpoint_pattern . '\1~i', 'action=$1' . $public_url . '$1', $html) ?? $html;
    $html = preg_replace('~action=(\'|\")' . $endpoint_relative_pattern . '\1~i', 'action=$1' . $public_url . '$1', $html) ?? $html;

    return $html;
}

function acgm_build_backend_url(string $endpoint, string $id): string
{
    if ($id === '') {
        return $endpoint;
    }

    return $endpoint . '?action=view&id=' . rawurlencode($id);
}

$id = acgm_request_value('id', '');
$view = acgm_request_value('view', '');
$is_help = ($id === 'help' || $view === 'help');
$is_post = (acgm_request_method() === 'POST');
$post_data = $is_post ? acgm_post_data() : [];

if ($is_post) {
    $remote_html = acgm_fetch($acgm_endpoint, $post_data);
} elseif (!$is_help) {
    $remote_html = acgm_fetch(acgm_build_backend_url($acgm_endpoint, $id));
} else {
    $remote_html = '';
}

$remote_body = '';
if ($remote_html !== '') {
    $remote_body = acgm_extract_body($remote_html);
    $remote_body = acgm_rewrite_links($remote_body, $acgm_endpoint, $public_tree_url);
}

$tree_table = '';
$member_count = '';
$is_modern_character = false;

if ($remote_body !== '' && $id === '' && !$is_post && !$is_help) {
    $tree_table = acgm_extract_tree_table($remote_body);
    $member_count = acgm_extract_member_count($remote_body);
}

if ($remote_body !== '' && ($id !== '' || $is_post) && !$is_help) {
    $is_modern_character = acgm_is_modern_character_html($remote_body);
}
?>

<style>
:root{
  --ac-bg:#11100d;
  --ac-bg-deep:#080706;
  --ac-panel:#16130f;
  --ac-panel-2:#211911;
  --ac-wood:#212529;
  --ac-wood-2:#171a1d;
  --ac-bronze:#7b5529;
  --ac-bronze-soft:rgba(201,155,58,.28);
  --ac-gold:#c99b3a;
  --ac-gold-bright:#f3d58b;
  --ac-cream:#f7e9c9;
  --ac-muted:#b9aa86;
  --ac-parchment:#d6b889;
  --ac-parchment-light:#efd5a1;
  --ac-parchment-dark:#b88f59;
  --ac-mule:#78512e;
  --ac-mule-light:#a86c38;
  --ac-field:#15110d;
  --ac-line:rgba(201,155,58,.20);
  --ac-shadow:0 18px 48px rgba(0,0,0,.52);
  --ac-card-shadow:0 12px 30px rgba(0,0,0,.34);
}

.acgm-site-page,
.acgm-site-page *{box-sizing:border-box;}

.acgm-site-page{
  color:var(--ac-cream);
  font-family:Arial,Helvetica,sans-serif;
}

.acgm-frame a:link,
.acgm-frame a:visited,
.acgm-frame a:active,
.acgm-character-wrap a:link,
.acgm-character-wrap a:visited,
.acgm-character-wrap a:active{
  color:var(--ac-gold-bright);
  font-weight:800;
  text-decoration:none;
}

.acgm-frame a:hover,
.acgm-character-wrap a:hover{
  color:#fff6d4!important;
  background:transparent!important;
  text-decoration:underline;
}

/* Keep the surrounding TheRegs theme from applying dark hover blocks inside the ACGM content. */
.acgm-site-page a:hover,
.acgm-site-page a:focus{
  background:transparent!important;
  box-shadow:none!important;
}

.acgm-frame{
  border:1px solid var(--ac-bronze-soft);
  border-radius:20px;
  background:
    radial-gradient(circle at 15% -5%,rgba(201,155,58,.13),transparent 28rem),
    linear-gradient(180deg,rgba(22,19,15,.96),rgba(10,9,7,.96));
  box-shadow:var(--ac-shadow);
  overflow:hidden;
  margin:1rem 0;
}

.acgm-hero{
  position:relative;
  padding:18px 20px;
  background:
    linear-gradient(180deg,rgba(33,37,41,.98),rgba(18,20,22,.98)),
    repeating-linear-gradient(90deg,rgba(255,255,255,.025) 0 1px,transparent 1px 9px);
  border-bottom:1px solid var(--ac-line);
}

.acgm-hero:after{
  content:"";
  position:absolute;
  left:0;
  right:0;
  bottom:0;
  height:1px;
  background:linear-gradient(90deg,transparent,var(--ac-gold-bright),transparent);
  opacity:.42;
}

.acgm-hero-row{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;}
.acgm-eyebrow{margin:0 0 5px;color:var(--ac-muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.16em;font-weight:900;}
.acgm-hero h1{margin:0;color:var(--ac-gold-bright);font-size:1.65rem;line-height:1.08;text-shadow:0 2px 0 rgba(0,0,0,.40);}
.acgm-hero p{margin:.4rem 0 0;color:var(--ac-cream);}
.acgm-body{padding:16px;}

.acgm-toolbar{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding:10px 16px;
  background:rgba(0,0,0,.18);
  border-bottom:1px solid rgba(201,155,58,.14);
}

.acgm-legend,
.acgm-tools{display:flex;align-items:center;gap:8px;flex-wrap:wrap;}
.acgm-tools{justify-content:flex-end;}
.acgm-pill{display:inline-flex;align-items:center;gap:7px;border:1px solid rgba(201,155,58,.30);border-radius:999px;background:rgba(0,0,0,.22);padding:7px 11px;color:var(--ac-cream);font-size:.84rem;font-weight:700;}
.acgm-swatch{width:13px;height:13px;border-radius:4px;border:1px solid rgba(255,255,255,.25);background:linear-gradient(180deg,var(--ac-parchment-light),var(--ac-parchment));}
.acgm-swatch.mule{background:linear-gradient(180deg,var(--ac-mule-light),var(--ac-mule));}
.acgm-search input{width:220px;max-width:50vw;border:1px solid rgba(201,155,58,.34);border-radius:999px;background:rgba(0,0,0,.28);color:var(--ac-cream);padding:8px 12px;font-weight:700;outline:none;}
.acgm-search input:focus{border-color:var(--ac-gold-bright);box-shadow:0 0 0 2px rgba(201,155,58,.18);}
.acgm-btn{border:1px solid rgba(201,155,58,.34);border-radius:999px;background:rgba(0,0,0,.24);color:var(--ac-cream);padding:7px 10px;font-size:.8rem;font-weight:900;cursor:pointer;line-height:1;}
.acgm-btn:hover,.acgm-btn:focus{color:#211508;background:linear-gradient(180deg,#f1c967,#a36d1f);border-color:#f3d58b;outline:none;}
.acgm-search-count,.acgm-zoom-label{color:var(--ac-muted);font-size:.78rem;font-weight:800;}
.acgm-zoom-label{min-width:42px;text-align:center;}

.acgm-tree-panel{
  width:100%;
  max-width:100%;
  min-height:520px;
  overflow:auto;
  border:1px solid rgba(201,155,58,.25);
  border-radius:18px;
  background:
    radial-gradient(circle at 50% 30%,rgba(201,155,58,.06),transparent 32rem),
    linear-gradient(rgba(12,10,8,.86),rgba(12,10,8,.86)),
    repeating-linear-gradient(45deg,rgba(255,255,255,.018) 0 1px,transparent 1px 9px);
  box-shadow:var(--ac-card-shadow);
  padding:10px;
  scrollbar-color:var(--ac-bronze) #090807;
}

.acgm-canvas{display:inline-block;min-width:max-content;transform-origin:top left;transition:transform .12s ease;}

/* ACGM PRESERVATION RULES: the CGI owns the tree geometry. */
.acgm-site-page table.tree{
  width:auto!important;
  min-width:max-content;
  border-collapse:separate!important;
  border-spacing:3px 2px!important;
  text-align:center!important;
  color:var(--ac-cream);
}
.acgm-site-page table.tree td{vertical-align:middle!important;text-align:center!important;}
.acgm-site-page td.muleNo,
.acgm-site-page td.muleYes,
.acgm-site-page .muleNo,
.acgm-site-page .muleYes{
  width:92px!important;
  min-width:92px!important;
  max-width:112px!important;
  height:32px!important;
  min-height:32px!important;
  padding:2px 5px!important;
  border:1px solid rgba(92,58,27,.92)!important;
  border-radius:7px!important;
  color:#1d1308!important;
  background:linear-gradient(180deg,var(--ac-parchment-light),var(--ac-parchment) 60%,var(--ac-parchment-dark))!important;
  box-shadow:0 3px 8px rgba(0,0,0,.30),inset 0 1px 0 rgba(255,255,255,.30);
  overflow:hidden!important;
  transition:filter .12s ease,box-shadow .12s ease,border-color .12s ease;
}
.acgm-site-page td.muleYes,
.acgm-site-page .muleYes{
  color:#fff0cf!important;
  background:linear-gradient(180deg,var(--ac-mule-light),var(--ac-mule))!important;
  border-color:rgba(243,213,139,.55)!important;
}
.acgm-site-page td.muleNo:hover{filter:brightness(1.03);border-color:var(--ac-gold)!important;background:linear-gradient(180deg,#f5dfb6,#d9bb88 62%,#bf935b)!important;}
.acgm-site-page td.muleYes:hover{filter:brightness(1.05);border-color:var(--ac-gold-bright)!important;background:linear-gradient(180deg,#a86c38,#74502e)!important;}
.acgm-site-page td.muleNo a,
.acgm-site-page td.muleNo a:visited,
.acgm-site-page td.muleNo a:hover,
.acgm-site-page td.muleNo a:focus,
.acgm-site-page td.muleNo a:active{
  color:#1e1308!important;
  background:transparent!important;
  font-weight:900!important;
  text-decoration:none!important;
  text-shadow:none!important;
  box-shadow:none!important;
}
.acgm-site-page td.muleYes a,
.acgm-site-page td.muleYes a:visited,
.acgm-site-page td.muleYes a:hover,
.acgm-site-page td.muleYes a:focus,
.acgm-site-page td.muleYes a:active{
  color:#fff4d0!important;
  background:transparent!important;
  font-weight:900!important;
  text-decoration:none!important;
  text-shadow:0 1px 0 rgba(0,0,0,.45)!important;
  box-shadow:none!important;
}
.acgm-site-page td.muleNo,
.acgm-site-page td.muleYes{font-size:.72rem!important;line-height:1.05!important;font-weight:900;}
.acgm-site-page td.muleNo br,
.acgm-site-page td.muleYes br{line-height:1!important;}
.acgm-site-page td.muleNo img,
.acgm-site-page td.muleYes img{display:none!important;}

.acgm-search-hit{border-color:var(--ac-gold-bright)!important;box-shadow:0 0 0 2px rgba(243,213,139,.30),0 0 18px rgba(201,155,58,.45),inset 0 1px 0 rgba(255,255,255,.34)!important;filter:brightness(1.13);}
.acgm-search-current{border-color:#fff6d4!important;box-shadow:0 0 0 2px rgba(255,246,212,.55),0 0 24px rgba(243,213,139,.55),inset 0 1px 0 rgba(255,255,255,.40)!important;}

.acgm-character-wrap > table.main,
.acgm-character-wrap table.main{
  width:100%!important;
  border:0!important;
  background:transparent!important;
  color:var(--ac-cream)!important;
}
.acgm-character-wrap td.mainitemhead{
  display:block!important;
  padding:18px 20px!important;
  color:var(--ac-gold-bright)!important;
  background:linear-gradient(180deg,rgba(63,43,25,.96),rgba(29,20,13,.98))!important;
  border-bottom:1px solid var(--ac-line)!important;
  font-size:1.35rem!important;
}
.acgm-character-wrap td.mainitembody{
  display:block!important;
  padding:18px!important;
  color:var(--ac-cream)!important;
  background:linear-gradient(180deg,rgba(22,19,15,.96),rgba(10,9,7,.96))!important;
}
.acgm-character-wrap p.parahead{
  margin:18px 0 10px!important;
  color:var(--ac-gold-bright)!important;
  text-transform:uppercase;
  letter-spacing:.08em;
  text-decoration:none!important;
  font-weight:900!important;
  border-bottom:1px solid var(--ac-line);
  padding-bottom:8px;
}
.acgm-character-wrap p{line-height:1.65;}
.acgm-character-wrap form{
  margin:10px 0 18px;
  border:1px solid rgba(201,155,58,.22);
  border-radius:14px;
  background:rgba(0,0,0,.16);
  padding:14px;
}
.acgm-character-wrap input,
.acgm-character-wrap select,
.acgm-character-wrap textarea{
  max-width:100%;
  border:1px solid rgba(201,155,58,.34);
  border-radius:9px;
  background:var(--ac-field);
  color:var(--ac-cream);
  padding:7px 9px;
  margin:4px 0 9px;
}
.acgm-character-wrap input[type=submit]{
  cursor:pointer;
  background:linear-gradient(180deg,#f1c967,#a36d1f);
  color:#1e1308;
  font-weight:900;
  border-color:#f3d58b;
  padding:8px 12px;
}


/* Character page support: preserves the modern CGI template-char.htm look inside the TheRegs wrapper. */
.acgm-character-wrap.modern-char .acgm-page{max-width:1240px;margin:0 auto;padding:0;color:var(--ac-cream);font-family:Arial,Helvetica,sans-serif;}
.acgm-character-wrap.modern-char .acgm-shell{border:1px solid var(--ac-bronze-soft);border-radius:20px;background:linear-gradient(180deg,rgba(22,19,15,.96),rgba(10,9,7,.96));box-shadow:var(--ac-shadow);overflow:hidden;}
.acgm-character-wrap.modern-char .acgm-hero{position:relative;padding:22px 24px 20px;background:linear-gradient(180deg,rgba(33,37,41,.98),rgba(18,20,22,.98)),repeating-linear-gradient(90deg,rgba(255,255,255,.025) 0 1px,transparent 1px 9px);border-bottom:1px solid var(--ac-line);}
.acgm-character-wrap.modern-char .acgm-hero:after{content:"";position:absolute;left:0;right:0;bottom:0;height:1px;background:linear-gradient(90deg,transparent,var(--ac-gold-bright),transparent);opacity:.42;}
.acgm-character-wrap.modern-char .acgm-hero-row{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;}
.acgm-character-wrap.modern-char .acgm-eyebrow{margin:0 0 5px;color:var(--ac-muted);font-size:.78rem;text-transform:uppercase;letter-spacing:.16em;font-weight:900;}
.acgm-character-wrap.modern-char h1{margin:0;color:var(--ac-gold-bright);font-size:2.05rem;line-height:1.08;text-shadow:0 2px 0 rgba(0,0,0,.40);}
.acgm-character-wrap.modern-char .acgm-subtitle{margin:.4rem 0 0;color:var(--ac-cream);font-size:1rem;}
.acgm-character-wrap.modern-char .acgm-subtitle strong{color:var(--ac-gold-bright);}
.acgm-character-wrap.modern-char .acgm-hero-actions{display:flex;align-items:center;gap:8px;flex-wrap:wrap;justify-content:flex-end;}
.acgm-character-wrap.modern-char .acgm-back,.acgm-character-wrap.modern-char .acgm-btn{display:inline-flex;align-items:center;gap:7px;border:1px solid rgba(201,155,58,.35);border-radius:999px;background:rgba(0,0,0,.25);color:var(--ac-cream)!important;padding:9px 13px;white-space:nowrap;font-weight:900;font-size:.86rem;cursor:pointer;text-decoration:none!important;}
.acgm-character-wrap.modern-char .acgm-back:hover,.acgm-character-wrap.modern-char .acgm-btn:hover,.acgm-character-wrap.modern-char .acgm-btn:focus{color:#211508!important;background:linear-gradient(180deg,#f1c967,#a36d1f);border-color:#f3d58b;outline:none;text-decoration:none!important;}
.acgm-character-wrap.modern-char .acgm-copy-status{color:var(--ac-muted);font-size:.8rem;font-weight:800;min-width:54px;}
.acgm-character-wrap.modern-char .acgm-stat-row{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:1px;background:var(--ac-line);border-top:1px solid var(--ac-line);}
.acgm-character-wrap.modern-char .acgm-stat{background:rgba(0,0,0,.18);padding:13px 16px;min-width:0;}
.acgm-character-wrap.modern-char .acgm-stat span{display:block;color:var(--ac-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.1em;font-weight:900;}
.acgm-character-wrap.modern-char .acgm-stat strong{display:block;margin-top:4px;color:var(--ac-cream);font-size:1.03rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.acgm-character-wrap.modern-char .acgm-content{padding:18px;}
.acgm-character-wrap.modern-char .acgm-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:16px;}
.acgm-character-wrap.modern-char .acgm-card{grid-column:span 6;border:1px solid rgba(201,155,58,.23);border-radius:16px;background:linear-gradient(180deg,rgba(34,29,22,.96),rgba(19,17,14,.96));box-shadow:var(--ac-card-shadow);overflow:hidden;}
.acgm-character-wrap.modern-char .acgm-card.third{grid-column:span 4;}.acgm-character-wrap.modern-char .acgm-card.full{grid-column:1/-1;}.acgm-character-wrap.modern-char .acgm-card.profile{grid-column:span 8;}.acgm-character-wrap.modern-char .acgm-card.side{grid-column:span 4;}
.acgm-character-wrap.modern-char .acgm-card h2{margin:0;padding:12px 15px;color:var(--ac-gold-bright);font-size:.94rem;text-transform:uppercase;letter-spacing:.10em;background:rgba(0,0,0,.24);border-bottom:1px solid var(--ac-line);}
.acgm-character-wrap.modern-char .acgm-body{padding:15px;}
.acgm-character-wrap.modern-char .acgm-card:hover{border-color:rgba(201,155,58,.36);}
.acgm-character-wrap.modern-char .acgm-info{display:grid;grid-template-columns:150px minmax(0,1fr);gap:9px 13px;margin:0;}
.acgm-character-wrap.modern-char .acgm-info dt{color:var(--ac-muted);font-weight:900;}
.acgm-character-wrap.modern-char .acgm-info dd{margin:0;min-width:0;}
/* Prevent long contact values, email links, and generated ACGM fields from overflowing inside the site wrapper. */
.acgm-character-wrap.modern-char,
.acgm-character-wrap.modern-char *{
  min-width:0;
}
.acgm-character-wrap.modern-char .acgm-card,
.acgm-character-wrap.modern-char .acgm-body,
.acgm-character-wrap.modern-char .acgm-box,
.acgm-character-wrap.modern-char .acgm-mini,
.acgm-character-wrap.modern-char .acgm-info dd,
.acgm-character-wrap.modern-char .acgm-meta,
.acgm-character-wrap.modern-char p,
.acgm-character-wrap.modern-char li{
  overflow-wrap:anywhere;
  word-break:break-word;
}
.acgm-character-wrap.modern-char a{
  overflow-wrap:anywhere;
  word-break:break-word;
}
.acgm-character-wrap.modern-char .acgm-info dd a,
.acgm-character-wrap.modern-char .acgm-mini strong a{
  display:inline;
  max-width:100%;
}

.acgm-character-wrap.modern-char .acgm-info dd ul{margin:0 0 0 18px;padding:0;}.acgm-character-wrap.modern-char .acgm-info dd li{margin:.15rem 0;}
.acgm-character-wrap.modern-char .acgm-profile-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
.acgm-character-wrap.modern-char .acgm-mini{border:1px solid rgba(201,155,58,.18);border-radius:13px;background:rgba(0,0,0,.15);padding:12px;}
.acgm-character-wrap.modern-char .acgm-mini span{display:block;color:var(--ac-muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.09em;font-weight:900;}
.acgm-character-wrap.modern-char .acgm-mini strong{display:block;margin-top:4px;color:var(--ac-cream);font-size:1rem;}
.acgm-character-wrap.modern-char .acgm-skill-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px;}
.acgm-character-wrap.modern-char .acgm-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;}
.acgm-character-wrap.modern-char .acgm-box{border:1px solid rgba(201,155,58,.18);border-radius:13px;background:rgba(0,0,0,.16);padding:13px;}
.acgm-character-wrap.modern-char .acgm-box h3{margin:0 0 10px;color:var(--ac-gold-bright);font-size:.88rem;text-transform:uppercase;letter-spacing:.08em;}
.acgm-character-wrap.modern-char .acgm-box ul{margin:0 0 0 18px;padding:0;}.acgm-character-wrap.modern-char .acgm-box li{margin:.2rem 0;}
.acgm-character-wrap.modern-char .acgm-section-note{margin:0 0 12px;color:var(--ac-muted);font-size:.9rem;line-height:1.45;}
.acgm-character-wrap.modern-char .acgm-action-card{background:linear-gradient(180deg,rgba(42,33,22,.95),rgba(17,14,11,.95));}.acgm-character-wrap.modern-char .acgm-warning{border-color:rgba(158,77,53,.42);}
.acgm-character-wrap.modern-char form{margin:0;}
.acgm-character-wrap.modern-char input,.acgm-character-wrap.modern-char select,.acgm-character-wrap.modern-char textarea{max-width:100%;border:1px solid rgba(201,155,58,.34);border-radius:9px;background:var(--ac-field);color:var(--ac-cream);padding:7px 9px;margin:4px 0 9px;box-shadow:inset 0 1px 2px rgba(0,0,0,.35);}
.acgm-character-wrap.modern-char input[type=text],.acgm-character-wrap.modern-char input[type=password]{width:100%;}.acgm-character-wrap.modern-char input[size="2"],.acgm-character-wrap.modern-char input[size="3"],.acgm-character-wrap.modern-char input[size="5"],.acgm-character-wrap.modern-char input[size="10"]{width:auto;min-width:5.5rem;}
.acgm-character-wrap.modern-char input[type=submit]{cursor:pointer;width:auto;background:linear-gradient(180deg,#f1c967,#a36d1f);color:#1e1308;font-weight:900;border-color:#f3d58b;padding:8px 12px;box-shadow:0 5px 14px rgba(0,0,0,.25);}
.acgm-character-wrap.modern-char input[type=submit]:hover{filter:brightness(1.08);transform:translateY(-1px);}
.acgm-character-wrap.modern-char input:focus,.acgm-character-wrap.modern-char select:focus,.acgm-character-wrap.modern-char textarea:focus{outline:1px solid var(--ac-gold-bright);outline-offset:1px;}
.acgm-character-wrap.modern-char .acgm-action-label{display:block;color:var(--ac-muted);font-weight:900;margin:4px 0 2px;font-size:.85rem;}
.acgm-character-wrap.modern-char .acgm-meta{color:var(--ac-muted);line-height:1.75;font-size:.9rem;}
.acgm-character-wrap.modern-char .acgm-divider{height:1px;background:linear-gradient(90deg,transparent,rgba(201,155,58,.32),transparent);margin:10px 0 12px;}
.acgm-character-wrap.modern-char .acgm-details summary{cursor:pointer;color:var(--ac-gold-bright);font-weight:900;text-transform:uppercase;letter-spacing:.08em;}.acgm-character-wrap.modern-char .acgm-details summary:hover{color:#fff6d4;}.acgm-character-wrap.modern-char .acgm-details[open] summary{margin-bottom:10px;}
@media(max-width:1000px){.acgm-character-wrap.modern-char .acgm-stat-row{grid-template-columns:repeat(3,minmax(0,1fr));}.acgm-character-wrap.modern-char .acgm-card.profile,.acgm-character-wrap.modern-char .acgm-card.side,.acgm-character-wrap.modern-char .acgm-card.third{grid-column:1/-1;}.acgm-character-wrap.modern-char .acgm-skill-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
@media(max-width:700px){.acgm-character-wrap.modern-char .acgm-hero-row{flex-direction:column;}.acgm-character-wrap.modern-char .acgm-stat-row{grid-template-columns:repeat(2,minmax(0,1fr));}.acgm-character-wrap.modern-char .acgm-content{padding:12px;}.acgm-character-wrap.modern-char .acgm-card{grid-column:1/-1;}.acgm-character-wrap.modern-char .acgm-profile-grid,.acgm-character-wrap.modern-char .acgm-skill-grid,.acgm-character-wrap.modern-char .acgm-form-grid{grid-template-columns:1fr;}.acgm-character-wrap.modern-char .acgm-info{grid-template-columns:1fr;gap:4px;}.acgm-character-wrap.modern-char .acgm-info dt{margin-top:8px;}.acgm-character-wrap.modern-char h1{font-size:1.55rem;}}

.acgm-footer{margin:16px;text-align:center;color:var(--ac-muted);border:1px solid rgba(201,155,58,.18);border-radius:14px;background:rgba(0,0,0,.18);padding:11px 12px;font-size:.86rem;line-height:1.55;}
.acgm-footer strong{color:var(--ac-gold-bright);}
.acgm-footer .legacy{display:block;margin-top:2px;color:#9f906c;font-size:.78rem;}

@media(max-width:760px){.acgm-hero-row,.acgm-toolbar{align-items:flex-start;flex-direction:column}.acgm-hero h1{font-size:1.25rem}.acgm-tree-panel{padding:8px}.acgm-search input{max-width:78vw}}
@media print{.acgm-toolbar,.acgm-footer,.sidebar-nav,.offcanvas{display:none!important}.acgm-frame,.acgm-tree-panel{box-shadow:none}.acgm-tree-panel{overflow:visible;border:0}.acgm-site-page{color:#000}}
</style>

<button
    class="btn btn-outline-light d-md-none mb-3"
    type="button"
    data-bs-toggle="offcanvas"
    data-bs-target="#leftSidebar"
>
    ☰ Menu
</button>

<div
    class="offcanvas offcanvas-start text-bg-dark d-md-none"
    tabindex="-1"
    id="leftSidebar"
>
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Asheron's Call</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body">
        <?php render_sidebar('ac'); ?>
    </div>
</div>

<aside class="col-md-2 d-none d-md-block sidebar-nav">
    <?php render_sidebar('ac'); ?>
</aside>

<main
    class="col-md-8 text-light acgm-site-page"
    style="background:url('//cdn.theregs.org/images/tsw/pixel.webp') repeat;"
>

<?php if ($is_help): ?>

    <div class="acgm-frame">
        <header class="acgm-hero">
            <div class="acgm-hero-row">
                <div>
                    <p class="acgm-eyebrow">The Reg's Allegiance Manager</p>
                    <h1>Allegiance Tree Help</h1>
                    <p>Help and connection information for the Asheron's Call tree.</p>
                </div>
            </div>
        </header>

        <div class="acgm-body lh-lg">
            <p>
                The Official Regs Allegiance Tree can be found here:
                <a href="<?= htmlspecialchars($public_tree_url, ENT_QUOTES, 'UTF-8') ?>">The Regs Allegiance Tree</a>
            </p>

            <p>
                Please try to keep this up to date. You can change your password to whatever
                will be best for you but please try to remember it.
            </p>

            <h5>Tree Administrators</h5>
            <p>William Ohmsford</p>

            <p class="mb-0">
                If you need assistance please contact an administrator or post on our forums.
            </p>
        </div>

        <footer class="acgm-footer"><strong>The Reg's Allegiance Manager</strong><span class="legacy">Originally powered by ACGM &bull; Modernized for TheRegs.org</span></footer>
    </div>

<?php else: ?>

    <div class="acgm-frame">
        <?php if ($id === '' && !$is_post): ?>
            <header class="acgm-hero">
                <div class="acgm-hero-row">
                    <div>
                        <p class="acgm-eyebrow">The Reg's Allegiance Manager</p>
                        <h1>Darktide Regulators Family Tree</h1>
                        <p>Public tree powered by the legacy ACGM backend.<?php if ($member_count !== ''): ?> <strong><?= htmlspecialchars($member_count, ENT_QUOTES, 'UTF-8') ?> Members Listed</strong><?php endif; ?></p>
                    </div>
                </div>
            </header>

            <div class="acgm-toolbar">
                <div class="acgm-legend">
                    <span class="acgm-pill"><span class="acgm-swatch"></span>Not a Mule</span>
                    <span class="acgm-pill"><span class="acgm-swatch mule"></span>Is a Mule</span>
                </div>
                <div class="acgm-tools" aria-label="Tree tools">
                    <div class="acgm-search">
                        <input id="acgmSearch" type="search" placeholder="Search character..." autocomplete="off" aria-label="Search character name">
                    </div>
                    <button class="acgm-btn" type="button" id="acgmSearchNext">Next</button>
                    <button class="acgm-btn" type="button" id="acgmSearchClear">Clear</button>
                    <span class="acgm-search-count" id="acgmSearchCount">0 found</span>
                    <button class="acgm-btn" type="button" id="acgmZoomOut">-</button>
                    <span class="acgm-zoom-label" id="acgmZoomLabel">100%</span>
                    <button class="acgm-btn" type="button" id="acgmZoomIn">+</button>
                    <button class="acgm-btn" type="button" id="acgmPrint">Print</button>
                </div>
            </div>

            <div class="acgm-body">
                <div class="alert alert-secondary lh-lg">
                    <p>It is your responsibility to maintain your own record and ensure that your vassals do also.</p>
                    <p>You can update your own basic information after viewing your record, e.g. level, etc.</p>
                    <p>
                        For further functionality, such as entering your skills,
                        <a href="https://www.theregs.org/ac/tree/index.php">download the AC Guild Manager Client</a>.
                        <small>(NB We are running version 0.32.)</small>
                    </p>
                    <hr>
                    <p><strong>Server:</strong> www.theregs.org/cgi-bin/acgm/server.cgi</p>
                    <p><strong>Username:</strong> Your in-game name</p>
                    <p><strong>Password:</strong> If it is the first time you are logging in use the family default, otherwise this is whatever you set it to previously.</p>
                </div>

                <div class="acgm-tree-panel" aria-label="Allegiance tree scroll area">
                    <div class="acgm-canvas">
                        <?php if ($tree_table !== ''): ?>
                            <?= $tree_table ?>
                        <?php else: ?>
                            <div class="alert alert-danger my-4">Unable to load tree data.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="acgm-character-wrap<?= $is_modern_character ? ' modern-char' : ' legacy-char' ?>">
                <?php if ($remote_body !== ''): ?>
                    <?= $remote_body ?>
                <?php else: ?>
                    <div class="alert alert-danger my-4">Unable to load character data.</div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($id === '' && !$is_post): ?>
            <footer class="acgm-footer"><strong>The Reg's Allegiance Manager</strong><span class="legacy">Originally powered by ACGM &bull; Modernized for TheRegs.org</span></footer>
        <?php endif; ?>
    </div>

<?php endif; ?>

</main>

<?php render_right_sidebar('ac'); ?>

<script>
(function(){
  var root=document.querySelector('.acgm-site-page');
  if(!root)return;
  var panel=root.querySelector('.acgm-tree-panel');
  var canvas=root.querySelector('.acgm-canvas');
  var search=root.querySelector('#acgmSearch');
  var count=root.querySelector('#acgmSearchCount');
  var next=root.querySelector('#acgmSearchNext');
  var clear=root.querySelector('#acgmSearchClear');
  var zoomIn=root.querySelector('#acgmZoomIn');
  var zoomOut=root.querySelector('#acgmZoomOut');
  var zoomLabel=root.querySelector('#acgmZoomLabel');
  var printBtn=root.querySelector('#acgmPrint');
  var zoom=1;
  var hits=[];
  var current=-1;

  function cells(){return Array.prototype.slice.call(root.querySelectorAll('td.muleNo,td.muleYes'));}
  function textOf(cell){return (cell.textContent||'').replace(/\s+/g,' ').trim().toLowerCase();}
  function clearHits(){cells().forEach(function(c){c.classList.remove('acgm-search-hit','acgm-search-current');});hits=[];current=-1;}
  function updateCount(){if(count){count.textContent=hits.length+(hits.length===1?' found':' found');}}
  function runSearch(){
    clearHits();
    var q=(search&&search.value||'').trim().toLowerCase();
    if(!q){updateCount();return;}
    hits=cells().filter(function(c){return textOf(c).indexOf(q)!==-1;});
    hits.forEach(function(c){c.classList.add('acgm-search-hit');});
    updateCount();
    if(hits.length){current=0;focusHit();}
  }
  function focusHit(){
    hits.forEach(function(c){c.classList.remove('acgm-search-current');});
    if(current<0||!hits[current])return;
    var c=hits[current];
    c.classList.add('acgm-search-current');
    c.scrollIntoView({behavior:'smooth',block:'center',inline:'center'});
  }
  function nextHit(){if(!hits.length){runSearch();return;}current=(current+1)%hits.length;focusHit();}
  function setZoom(value){
    zoom=Math.max(.65,Math.min(1.35,value));
    if(canvas){canvas.style.transform='scale('+zoom+')';canvas.style.marginRight=((zoom-1)*100)+'%';canvas.style.marginBottom=((zoom-1)*100)+'%';}
    if(zoomLabel){zoomLabel.textContent=Math.round(zoom*100)+'%';}
  }
  if(search){search.addEventListener('input',runSearch);search.addEventListener('keydown',function(e){if(e.key==='Enter'){e.preventDefault();nextHit();}});}
  if(next){next.addEventListener('click',nextHit);}
  if(clear){clear.addEventListener('click',function(){if(search){search.value='';search.focus();}clearHits();updateCount();});}
  if(zoomIn){zoomIn.addEventListener('click',function(){setZoom(zoom+.1);});}
  if(zoomOut){zoomOut.addEventListener('click',function(){setZoom(zoom-.1);});}
  if(printBtn){printBtn.addEventListener('click',function(){window.print();});}
  updateCount();
})();
</script>
