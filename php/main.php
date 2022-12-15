<?php

function main()
{
  // HEADER
  $header_template = file_get_contents('../templates/header.html');
  $navigation = file_get_contents('../templates/navigation.html');
  $header = preg_replace('/\[\%navigation\%\]/', $navigation, $header_template);

  // FOOTER
  $footer = file_get_contents('../templates/footer.html');

  // CONTENT
  $request = trim($_SERVER['REQUEST_URI'], '/') ?: 'home';
  $path = '../templates/content/' . $request . '.html';
  $main = file_exists($path)
    ? file_get_contents($path)
    : '<h1>Fehler!</h1><h4>Inhalt "' .
      $request .
      '" konnte nicht gefunden werden!</h4>';

  // HTML
  $html_template = file_get_contents('../templates/html.html');
  $temp_html = preg_replace('/\[\%header\%\]/', $header, $html_template);
  $temp_html = preg_replace('/\[\%footer\%\]/', $footer, $temp_html);
  $html = preg_replace('/\[\%main\%\]/', $main, $temp_html);

  return $html;
}

?>
