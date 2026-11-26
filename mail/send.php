<?php
/**
 * SKAA 2027 – mail relay  (version 1.0, 26 November 2026)
 * ------------------------------------------------------------------
 * Sends the automatic e-mails of the registration and abstract sheets
 * (Google Apps Script → this script → the mail server of skaa.si) from the
 * real mailboxes registration@skaa.si and abstracts@skaa.si, and saves a
 * copy of every e-mail in the "Sent" folder of that mailbox (IMAP).
 *
 * Why: Google removes "Send mail as" for non-Google addresses from Gmail in
 * January 2027, and Apps Script can only send through Gmail. This relay is
 * called by the scripts instead (UrlFetchApp, JSON POST, secret token).
 *
 * Files
 *   mail/send.php               this script          (public_html/mail/)
 *   mail/PHPMailer/             PHPMailer 6.12 (LGPL) (public_html/mail/)
 *   ~/skaa-mail/config.php      token, mailbox passwords, server settings
 *                               – OUTSIDE public_html, created by hand from
 *                               mail/config.example.php (never in GitHub)
 *   ~/skaa-mail/relay.log       one line per e-mail (date, account, to, subject, result)
 *
 * Request (POST, Content-Type: application/json)
 *   { "token": "...", "account": "registration" | "abstracts",
 *     "action": "send" (default) | "check",
 *     "to": "name@example.com", "toName": "...", "subject": "...",
 *     "html": "<html body>", "text": "plain-text alternative",
 *     "replyTo": "info@skaa.si", "replyToName": "...",
 *     "bcc": ["..."],
 *     "attachments":  [ { "name": "x.docx", "type": "mime/type", "base64": "..." } ],
 *     "inlineImages": [ { "cid": "skaaHeader", "name": "h.png", "type": "image/png", "base64": "..." } ] }
 * Answer
 *   { "ok": true, "sent": true, "saved": true|false, "folder": "INBOX.Sent", "messageId": "<...>" }
 *   { "ok": false, "error": "..." }
 * A GET request only answers with the relay status (no secrets).
 */

declare(strict_types=1);

use PHPMailer\PHPMailer\PHPMailer;

const RELAY_VERSION = '1.0';
const MAX_BODY_BYTES = 25 * 1024 * 1024;   // whole request (attachments included)

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');

function answer(array $a, int $code = 200): void {
  http_response_code($code);
  echo json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

/* ------------------------------------------------------------ config */

/** Where config.php may live: ~/skaa-mail/config.php (recommended – outside the web root), or next to this file. */
function configCandidates(): array {
  $c = [];
  $home = getenv('HOME');
  if (!$home && !empty($_SERVER['HOME'])) $home = $_SERVER['HOME'];
  if ($home) $c[] = rtrim($home, '/') . '/skaa-mail/config.php';
  $c[] = dirname(__DIR__, 2) . '/skaa-mail/config.php';     // public_html/mail/send.php → $HOME/skaa-mail/config.php
  $c[] = __DIR__ . '/config.php';                            // fallback: protected by mail/.htaccess
  return array_values(array_unique($c));
}
function loadConfig(): ?array {
  foreach (configCandidates() as $f) {
    if (is_file($f)) { $cfg = include $f; return is_array($cfg) ? $cfg + ['_file' => $f] : null; }
  }
  return null;
}

/* ------------------------------------------------------------ status (GET) */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  $cfg = loadConfig();
  answer([
    'ok' => true, 'relay' => 'SKAA 2027 mail relay', 'version' => RELAY_VERSION, 'php' => PHP_VERSION,
    'config' => $cfg ? 'found' : 'missing – create ~/skaa-mail/config.php from mail/config.example.php',
    'accounts' => $cfg ? array_keys($cfg['accounts'] ?? []) : [],
  ]);
}

/* ------------------------------------------------------------ request */

$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) > MAX_BODY_BYTES) answer(['ok' => false, 'error' => 'Request too large or unreadable.'], 413);
$req = json_decode($raw, true);
if (!is_array($req)) answer(['ok' => false, 'error' => 'Expected a JSON body.'], 400);

$cfg = loadConfig();
if (!$cfg) answer(['ok' => false, 'error' => 'config.php not found on the server (expected ~/skaa-mail/config.php).'], 500);
if (empty($cfg['token']) || strlen((string)$cfg['token']) < 16) answer(['ok' => false, 'error' => 'The token in config.php is missing or too short.'], 500);
if (!hash_equals((string)$cfg['token'], (string)($req['token'] ?? ''))) {
  usleep(300000);
  answer(['ok' => false, 'error' => 'Invalid token.'], 403);
}

$accountKey = (string)($req['account'] ?? '');
$acc = $cfg['accounts'][$accountKey] ?? null;
if (!$acc || empty($acc['user']) || !isset($acc['password'])) answer(['ok' => false, 'error' => 'Unknown account "' . $accountKey . '" (config.php → accounts).'], 400);
$smtp = ($cfg['smtp'] ?? []) + ['host' => 'mail.skaa.si', 'port' => 465, 'secure' => 'ssl', 'verify' => true];
$imap = ($cfg['imap'] ?? []) + ['host' => $smtp['host'], 'port' => 993, 'secure' => 'ssl', 'verify' => $smtp['verify'], 'sent' => ''];
$fromAddress = (string)($acc['address'] ?? $acc['user']);
$fromName    = (string)($acc['name'] ?? 'SKAA 2027');

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';


function newMailer(array $smtp, array $acc): PHPMailer {
  $m = new PHPMailer(true);
  $m->isSMTP();
  $m->Host = (string)$smtp['host'];
  $m->Port = (int)$smtp['port'];
  $sec = strtolower((string)$smtp['secure']);
  $m->SMTPSecure = $sec === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : ($sec === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : '');
  $m->SMTPAutoTLS = $sec !== '';
  $m->SMTPAuth = true;
  $m->Username = (string)$acc['user'];
  $m->Password = (string)$acc['password'];
  $m->Timeout = 20;
  $m->CharSet = PHPMailer::CHARSET_UTF8;
  $m->Encoding = PHPMailer::ENCODING_QUOTED_PRINTABLE;
  if (empty($smtp['verify'])) {   // only if the hosting's certificate does not match the host name
    $m->SMTPOptions = ['ssl' => ['verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true]];
  }
  return $m;
}

function logLine(array $cfg, string $line): void {
  $dir = dirname((string)$cfg['_file']);
  $f = $dir . '/relay.log';
  if (!is_dir($dir) || (!is_writable($dir) && !is_writable($f))) return;
  @file_put_contents($f, date('Y-m-d H:i:s') . "  " . $line . "\n", FILE_APPEND | LOCK_EX);
}

/* ------------------------------------------------------------ IMAP: save a copy in "Sent" */

final class ImapAppend {
  private $fp; private $n = 0; private $delim = '.';
  public $log = [];

  public function __construct(string $host, int $port, string $secure, bool $verify) {
    $ctx = stream_context_create(['ssl' => ['verify_peer' => $verify, 'verify_peer_name' => $verify, 'allow_self_signed' => !$verify]]);
    $url = ($secure === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $fp = @stream_socket_client($url, $errno, $errstr, 15, STREAM_CLIENT_CONNECT, $ctx);
    if (!$fp) throw new RuntimeException('IMAP connection failed: ' . $errstr . ' (' . $errno . ')');
    stream_set_timeout($fp, 20);
    $this->fp = $fp;
    $greet = $this->line();
    if (strpos($greet, '* OK') !== 0 && strpos($greet, '* PREAUTH') !== 0) throw new RuntimeException('IMAP: unexpected greeting ' . trim($greet));
    if ($secure === 'tls') {
      $this->cmd('STARTTLS');
      if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) throw new RuntimeException('IMAP: STARTTLS failed');
    }
  }
  private function line(): string {
    $l = fgets($this->fp, 65536);
    if ($l === false) throw new RuntimeException('IMAP: connection closed');
    return $l;
  }
  private static function q(string $s): string { return '"' . addcslashes($s, "\\\"") . '"'; }
  /** Sends a command and returns all untagged lines; throws unless the tagged answer is OK. */
  private function cmd(string $c, ?string $literal = null): array {
    $tag = 'A' . (++$this->n);
    fwrite($this->fp, $tag . ' ' . $c . "\r\n");
    $untagged = [];
    while (true) {
      $l = $this->line();
      if ($literal !== null && $l[0] === '+') { fwrite($this->fp, $literal . "\r\n"); $literal = null; continue; }
      if (strpos($l, $tag . ' ') === 0) {
        $status = strtoupper(substr($l, strlen($tag) + 1, 2));
        if ($status !== 'OK') throw new RuntimeException('IMAP ' . strtoupper(strtok($c, ' ')) . ': ' . trim(substr($l, strlen($tag) + 1)));   // verb only – never the arguments (password)
        return $untagged;
      }
      $untagged[] = rtrim($l, "\r\n");
    }
  }
  public function login(string $user, string $pass): void { $this->cmd('LOGIN ' . self::q($user) . ' ' . self::q($pass)); }
  /** The Sent folder: $preferred from config.php, else the folder flagged \Sent, else the usual names (created when missing). */
  public function sentFolder(string $preferred = ''): string {
    $list = $this->cmd('LIST "" "*"');
    $folders = []; $flagged = '';
    foreach ($list as $l) {
      if (!preg_match('/^\* LIST \(([^)]*)\) ("(?:[^"\\\\]|\\\\.)*"|NIL) (.+)$/i', $l, $m)) continue;
      $d = $m[2] === 'NIL' ? '' : stripcslashes(substr($m[2], 1, -1));
      if ($d !== '') $this->delim = $d;
      $name = trim($m[3]);
      if ($name !== '' && $name[0] === '"') $name = stripcslashes(substr($name, 1, -1));
      elseif (preg_match('/^\{\d+\}$/', $name)) continue;     // literal folder names are not expected on cPanel
      $folders[] = $name;
      if (!$flagged && stripos($m[1], '\\Sent') !== false) $flagged = $name;
    }
    if ($preferred !== '' && in_array($preferred, $folders, true)) return $preferred;
    if ($flagged !== '') return $flagged;
    foreach (['INBOX' . $this->delim . 'Sent', 'Sent', 'INBOX' . $this->delim . 'Sent Items', 'Sent Items', 'INBOX' . $this->delim . 'Sent Messages', 'Sent Messages'] as $cand) {
      if (in_array($cand, $folders, true)) return $cand;
    }
    $create = $preferred !== '' ? $preferred : 'INBOX' . $this->delim . 'Sent';
    $this->cmd('CREATE ' . self::q($create));
    try { $this->cmd('SUBSCRIBE ' . self::q($create)); } catch (RuntimeException $e) { /* optional */ }
    return $create;
  }
  public function append(string $folder, string $mime): void {
    $mime = preg_replace("/\r?\n/", "\r\n", $mime);
    $this->cmd('APPEND ' . self::q($folder) . ' (\\Seen) {' . strlen($mime) . '}', $mime);
  }
  public function logout(): void { try { $this->cmd('LOGOUT'); } catch (Throwable $e) {} @fclose($this->fp); }
}

function saveToSent(array $imap, array $acc, string $mime): array {
  $c = new ImapAppend((string)$imap['host'], (int)$imap['port'], strtolower((string)$imap['secure']), !empty($imap['verify']));
  try {
    $c->login((string)$acc['user'], (string)$acc['password']);
    $folder = $c->sentFolder((string)($acc['sent'] ?? $imap['sent'] ?? ''));
    $c->append($folder, $mime);
    return ['saved' => true, 'folder' => $folder];
  } finally {
    $c->logout();
  }
}

/* ------------------------------------------------------------ action: check */

$action = (string)($req['action'] ?? 'send');
if ($action === 'check') {
  $res = ['ok' => true, 'relay' => RELAY_VERSION, 'php' => PHP_VERSION, 'account' => $accountKey, 'from' => $fromName . ' <' . $fromAddress . '>', 'smtp' => '', 'imap' => '', 'log' => ''];
  try {
    $m = newMailer($smtp, $acc);
    if (!$m->smtpConnect()) throw new RuntimeException($m->ErrorInfo ?: 'login failed');
    $m->smtpClose();
    $res['smtp'] = 'OK – ' . $smtp['host'] . ':' . $smtp['port'] . ' (' . $smtp['secure'] . '), login as ' . $acc['user'];
  } catch (Throwable $e) {
    $res['ok'] = false; $res['smtp'] = 'FAILED – ' . $e->getMessage();
  }
  try {
    $c = new ImapAppend((string)$imap['host'], (int)$imap['port'], strtolower((string)$imap['secure']), !empty($imap['verify']));
    try { $c->login((string)$acc['user'], (string)$acc['password']); $folder = $c->sentFolder((string)($acc['sent'] ?? $imap['sent'] ?? '')); } finally { $c->logout(); }
    $res['imap'] = 'OK – copies are saved in "' . $folder . '"';
  } catch (Throwable $e) {
    $res['ok'] = false; $res['imap'] = 'FAILED – ' . $e->getMessage();
  }
  $dir = dirname((string)$cfg['_file']);
  $res['log'] = (is_writable($dir) || is_writable($dir . '/relay.log')) ? $dir . '/relay.log' : 'not writable (' . $dir . ')';
  logLine($cfg, 'check ' . $accountKey . ' smtp=' . ($res['smtp'][0] === 'O' ? 'ok' : 'failed') . ' imap=' . ($res['imap'][0] === 'O' ? 'ok' : 'failed'));
  answer($res);
}
if ($action !== 'send') answer(['ok' => false, 'error' => 'Unknown action.'], 400);

/* ------------------------------------------------------------ action: send */

$to = trim((string)($req['to'] ?? ''));
if (!filter_var($to, FILTER_VALIDATE_EMAIL)) answer(['ok' => false, 'error' => 'Invalid recipient address.'], 400);
$toName  = mb_substr(trim((string)($req['toName'] ?? '')), 0, 120);
$subject = mb_substr(trim(preg_replace('/[\r\n]+/', ' ', (string)($req['subject'] ?? ''))), 0, 250);
if ($subject === '') answer(['ok' => false, 'error' => 'Missing subject.'], 400);
$html = (string)($req['html'] ?? '');
$text = (string)($req['text'] ?? '');
if ($html === '' && $text === '') answer(['ok' => false, 'error' => 'Missing body.'], 400);
$replyTo = trim((string)($req['replyTo'] ?? ($acc['replyTo'] ?? '')));
if ($replyTo !== '' && !filter_var($replyTo, FILTER_VALIDATE_EMAIL)) answer(['ok' => false, 'error' => 'Invalid Reply-To address.'], 400);
$replyToName = mb_substr(trim((string)($req['replyToName'] ?? ($acc['replyToName'] ?? ''))), 0, 120);
$bcc = [];
foreach ((array)($req['bcc'] ?? []) as $b) { $b = trim((string)$b); if ($b !== '' && filter_var($b, FILTER_VALIDATE_EMAIL)) $bcc[] = $b; }
foreach ((array)($acc['bcc'] ?? []) as $b) { $b = trim((string)$b); if ($b !== '' && filter_var($b, FILTER_VALIDATE_EMAIL)) $bcc[] = $b; }

try {
  $m = newMailer($smtp, $acc);
  $m->setFrom($fromAddress, $fromName);
  $m->Sender = (string)$acc['user'];                  // envelope sender → bounces land in the mailbox
  if ($replyTo !== '') $m->addReplyTo($replyTo, $replyToName);
  $m->addAddress($to, $toName);
  foreach (array_unique($bcc) as $b) $m->addBCC($b);
  $m->Subject = $subject;
  if ($html !== '') { $m->isHTML(true); $m->Body = $html; $m->AltBody = $text !== '' ? $text : trim(html_entity_decode(strip_tags(preg_replace('/<(br|\/p|\/tr|\/li|\/div)[^>]*>/i', "\n", $html)), ENT_QUOTES, 'UTF-8')); }
  else { $m->isHTML(false); $m->Body = $text; }
  $nAtt = 0;
  foreach ((array)($req['inlineImages'] ?? []) as $img) {
    if (!is_array($img) || empty($img['cid']) || empty($img['base64'])) continue;
    $bin = base64_decode((string)$img['base64'], true);
    if ($bin === false) answer(['ok' => false, 'error' => 'Inline image is not valid base64.'], 400);
    $m->addStringEmbeddedImage($bin, (string)$img['cid'], (string)($img['name'] ?? 'image'), PHPMailer::ENCODING_BASE64, (string)($img['type'] ?? 'image/png'));
  }
  foreach ((array)($req['attachments'] ?? []) as $att) {
    if (!is_array($att) || empty($att['name']) || empty($att['base64'])) continue;
    $bin = base64_decode((string)$att['base64'], true);
    if ($bin === false) answer(['ok' => false, 'error' => 'Attachment is not valid base64.'], 400);
    $m->addStringAttachment($bin, (string)$att['name'], PHPMailer::ENCODING_BASE64, (string)($att['type'] ?? 'application/octet-stream'));
    $nAtt++;
  }
  $m->send();
  $messageId = $m->getLastMessageID();
  $mime = $m->getSentMIMEMessage();
} catch (Throwable $e) {
  logLine($cfg, 'FAILED ' . $accountKey . ' to=' . $to . ' subject="' . mb_substr($subject, 0, 80) . '" error=' . $e->getMessage());
  answer(['ok' => false, 'error' => 'Sending failed: ' . $e->getMessage()], 500);
}

$saved = ['saved' => false, 'folder' => '', 'warning' => ''];
try {
  $saved = saveToSent($imap, $acc, $mime) + $saved;
} catch (Throwable $e) {
  $saved['warning'] = 'sent, but the copy could not be saved in Sent: ' . $e->getMessage();
}
logLine($cfg, 'sent ' . $accountKey . ' to=' . $to . ' subject="' . mb_substr($subject, 0, 80) . '"' . ($nAtt ? ' attachments=' . $nAtt : '') . ' sentcopy=' . ($saved['saved'] ? $saved['folder'] : 'NO (' . $saved['warning'] . ')'));
answer(['ok' => true, 'sent' => true, 'saved' => $saved['saved'], 'folder' => $saved['folder'], 'warning' => $saved['warning'], 'messageId' => $messageId]);
