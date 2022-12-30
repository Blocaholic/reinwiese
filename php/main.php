<?php

function main() {
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

  if ($request == 'check') {
    $main = handleOrder($_POST);
  }

  // HTML
  $html_template = file_get_contents('../templates/html.html');
  $temp_html = preg_replace('/\[\%header\%\]/', $header, $html_template);
  $temp_html = preg_replace('/\[\%footer\%\]/', $footer, $temp_html);
  $html = preg_replace('/\[\%main\%\]/', $main, $temp_html);

  return $html;
}

class FormValidationException extends Exception {
}
class DatabaseException extends Exception {
}

function handleOrder($input) {
  try {
    $strippedInput = removeUnsafeChars($input);
    if ($strippedInput !== $input) {
      throw new FormValidationException('Unzulässige Zeichen');
    }

    $validatedInput = validateInput($strippedInput);

    return insertIntoDB($validatedInput)
      ? confirmOrder($validatedInput)
      : throw new DatabaseException('Eintrag in Datenbank fehlgeschlagen');
  } catch (FormValidationException $e) {
    return showError($e->getMessage());
  } catch (DatabaseException $e) {
    return showError($e->getMessage());
  }
}

function validateInput($input) {
  # EXISTS
  if (!isset($input['vorname'])) {
    throw new FormValidationException('Vorname fehlt');
  }
  if (!isset($input['nachname'])) {
    throw new FormValidationException('Nachname fehlt');
  }
  if (!isset($input['email'])) {
    throw new FormValidationException('E-Mail fehlt');
  }
  if (!isset($input['standardkiste'])) {
    throw new FormValidationException('Anzahl Standardkiste(n) fehlt');
  }
  if (!isset($input['familienkiste'])) {
    throw new FormValidationException('Anzahl Familienkiste(n) fehlt');
  }
  # RANGE
  if (strlen($input['vorname']) < 2) {
    throw new FormValidationException(
      'Der Vorname muss mindestens 2 Zeichen lang sein!'
    );
  }
  if (strlen($input['vorname']) > 50) {
    throw new FormValidationException(
      'Der Vorname darf höchstens 50 Zeichen lang sein!'
    );
  }
  if (strlen($input['nachname']) < 2) {
    throw new FormValidationException(
      'Der Nachname muss mindestens 2 Zeichen lang sein!'
    );
  }
  if (strlen($input['nachname']) > 50) {
    throw new FormValidationException(
      'Der Nachname darf höchstens 50 Zeichen lang sein!'
    );
  }
  if (strlen($input['email']) < 3) {
    throw new FormValidationException(
      'Die E-Mail muss mindestens 3 Zeichen lang sein!'
    );
  }
  if (strlen($input['email']) > 100) {
    throw new FormValidationException(
      'Der E-Mail darf höchstens 100 Zeichen lang sein!'
    );
  }
  if (!($input['standardkiste'] >= 0 && $input['standardkiste'] <= 9)) {
    throw new FormValidationException(
      'Anzahl Standardkisten muss zwischen 0 und 9 liegen'
    );
  }
  if (!($input['familienkiste'] >= 0 && $input['familienkiste'] <= 9)) {
    throw new FormValidationException(
      'Anzahl Familienkisten muss zwischen 0 und 9 liegen'
    );
  }
  return $input;
}

function removeUnsafeChars($input) {
  if (is_array($input)) {
    $output = [];
    foreach ($input as $k => $v) {
      $output[$k] = removeUnsafeChars($v);
    }
    return $output;
  }
  return preg_replace('#[\\\\=<>"/\'$(){}^|´`\[\]]#', '', $input);
}

function insertIntoDB($data) {
  # SQL-Verbindung herstellen
  # Eintragen in Datenbank
  # Eintrag überprüfen
  return true;
}

function showError($message) {
  return '<h1>Fehler</h1><p>' .
    $message .
    '</p><p>Bitte Eingaben überprüfen und erneut versuchen oder
    per Email (kontakt@reinwiese.de) oder Telefon (0176/344 010 95) versuchen.
    </p>';
}

function confirmOrder($data) {
  $html = '<h1>Vorbestellung Registriert</h1>';
  $html .= '<p>Vorname: ' . removeUnsafeChars($data['vorname']) . '</p>';
  $html .= '<p>Nachname: ' . removeUnsafeChars($data['nachname']) . '</p>';
  $html .= '<p>Email: ' . removeUnsafeChars($data['email']) . '</p>';
  $html .=
    '<p>Standardkiste(n): ' .
    removeUnsafeChars($data['standardkiste']) .
    '</p>';
  $html .=
    '<p>Familienkiste(n): ' .
    removeUnsafeChars($data['familienkiste']) .
    '</p>';
  return $html;
}

?>
