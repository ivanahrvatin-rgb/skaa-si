<?php
/**
 * SKAA 2027 – mail relay settings  (template)
 * ------------------------------------------------------------------
 * Copy this file to   ~/skaa-mail/config.php   on the skaa.si hosting
 * (cPanel → File Manager → in your HOME folder, next to public_html, create
 * the folder "skaa-mail" and inside it the file "config.php") and fill in
 * the token and the two mailbox passwords. The file must NOT be inside
 * public_html and must never be put into GitHub.
 *
 * Token: the same long random string as in the Google Sheets
 * (SKAA menu → Mail relay – show token).
 */
return [

  // shared secret between the Google Sheets scripts and this relay (at least 32 characters)
  'token' => 'PASTE-THE-TOKEN-FROM-THE-SKAA-MENU-HERE',

  // mail server of the skaa.si mailboxes (cPanel → Email Accounts → Connect Devices)
  'smtp' => [
    'host'   => 'mail.skaa.si',
    'port'   => 465,          // 465 = SSL/TLS, 587 = STARTTLS
    'secure' => 'ssl',        // 'ssl' for 465, 'tls' for 587
    'verify' => true,         // set to false only if the certificate of the mail server does not match its name
  ],
  'imap' => [
    'host'   => 'mail.skaa.si',
    'port'   => 993,
    'secure' => 'ssl',
    'verify' => true,
    'sent'   => '',           // name of the Sent folder, e.g. 'INBOX.Sent' – leave empty: it is detected automatically
  ],

  // the mailboxes the relay sends from (created in cPanel → Email Accounts)
  'accounts' => [
    'registration' => [
      'user'        => 'registration@skaa.si',
      'password'    => 'PASSWORD-OF-registration@skaa.si',
      'name'        => 'SKAA 2027 Registration',   // sender name shown to the participant
      'replyTo'     => 'info@skaa.si',             // default Reply-To (the script may override it)
      'replyToName' => 'SKAA 2027 secretariat',
      'bcc'         => [],                         // e.g. ['info@skaa.si'] for a copy of every registration e-mail
    ],
    'abstracts' => [
      'user'        => 'abstracts@skaa.si',
      'password'    => 'PASSWORD-OF-abstracts@skaa.si',
      'name'        => 'SKAA 2027 Abstracts',
      'replyTo'     => 'abstracts@skaa.si',
      'replyToName' => 'SKAA 2027 Scientific Committee',
      'bcc'         => [],
    ],
  ],
];
