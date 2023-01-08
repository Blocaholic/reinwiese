<?php
class DB {
  static function connect() {
    try {
      $user = $name = 'd03b9ff6';
      $password = 'DvxkAhJQ3ZUhEtec';

      $pdo = new PDO("mysql:dbname=$name;host=localhost", $user, $password);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $pdo;
    } catch (PDOException $e) {
      throw new DatabaseException(
        'Verbindung zur Datenbank fehlgeschlagen!',
        $e->getCode(),
        $e
      );
    }
  }

  public static function insertOrder($data) {
    try {
      $pdo = DB::connect();
      $query = 'INSERT INTO vorbestellung (
          vorname,
          nachname,
          strasse,
          hausnummer,
          postleitzahl,
          ort,
          email,
          telefon,
          standardkiste,
          familienkiste,
          datenschutz
          ) VALUES (
          :vorname,
          :nachname,
          :strasse,
          :hausnummer,
          :postleitzahl,
          :ort,
          :email,
          :telefon,
          :standardkiste,
          :familienkiste,
          :datenschutz
          )';
      $statement = $pdo->prepare($query);
      $statement->execute($data);
      $insertedId = $pdo->lastInsertId();
      $pdo = null;
      return $insertedId;
    } catch (PDOException $e) {
      throw new DatabaseException(
        'Daten konnten nicht in die Datenbank eingetragen werden!',
        0,
        $e
      );
    }
  }

  public static function getOrder($id) {
    try {
      $pdo = DB::connect();
      $query = 'SELECT * FROM vorbestellung WHERE id=? LIMIT 1';
      $statement = $pdo->prepare($query);
      $statement->execute([$id]);
      $row = $statement->fetch(PDO::FETCH_ASSOC);
      return $row;
    } catch (PDOException $e) {
      throw new DatabaseException(
        'Datenbankeintrag konnte nicht gefunden werden!',
        $e->getCode(),
        $e
      );
    }
  }
}
