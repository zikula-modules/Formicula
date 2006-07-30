<?php

// made with DefineMachine 0.4 (c) Frank Schummertz, frank.schummertz@landseer-stuttgart.de, 22.03.2003
// sourcefile : /var/www/modules/formicula/pnadmin.php
// defines read from /var/www/modules/formicula/pnlang/deu
// file created on Thursday 12th February 2004 20:14:21

//neu
define('_FOR_ILLEGALEMAIL', 'ungültige Emailadresse');
define('_FOR_SENDERINFO', 'Verwende folgende Daten für die Bestätigungsemail an den Benutzer');
define('_FOR_SENDERNAME', 'Absendername');
define('_FOR_SENDEREMAIL', 'Absenderemail');
define('_FOR_SENDERSUBJECT', 'Betreff');

//original
define( '_FOR_ADDCONTACT', 'Kontakt hinzufügen' );
define( '_FOR_CANCELDELETE', 'Löschung abbrechen' );
define( '_FOR_CONFIRMDELETE', 'Löschung bestätigen' );
define( '_FOR_CONTACTID','ID');
define( '_FOR_DELETE', 'löschen' );
define( '_FOR_DELETECONTACT', 'Kontakt löschen' );
define( '_FOR_DELETEUPLOADEDFILE','Datei nach dem Senden löschen');
define( '_FOR_EDIT', 'editieren' );
define( '_FOR_EDITCONFIG', 'Konfiguration' );
define( '_FOR_EDITCONTACT', 'Kontakt ändern' );
define( '_FOR_EMAIL', 'Emailadresse' );
define( '_FOR_FORMICULA', 'Formicula - Kontaktformulare' );
define( '_FOR_NAME', 'Bezeichnung' );
define( '_FOR_OPTIONS', 'Optionen' );
define( '_FOR_PUBLIC', 'Öffentlich' );
define( '_FOR_SENDUSER', 'Bestätigungsmail an User verschicken?' );
define( '_FOR_SHOWCOMMENT', 'Kommentarfeld anzeigen?' );
define( '_FOR_SHOWCOMPANY', 'Firma anzeigen?' );
define( '_FOR_SHOWLOCATION', 'Standort anzeigen?' );
define( '_FOR_SHOWPHONE', 'Telefonnummer anzeigen?' );
define( '_FOR_SHOWURL', 'Homepage anzeigen?' );
define( '_FOR_UPLOADDIRNOTWRITABLE','Dieses Verzeichnis ist vom Webserver nicht beschreibbar' );
define( '_FOR_UPLOADFILEDIR', 'Verzeichnis für Dateiupload');
define( '_FOR_VIEWCONTACT', 'Kontakte anzeigen' );

if( !defined(_FOR_BADAUTHKEY) ) { define('_FOR_BADAUTHKEY', 'Ungültiger AuthKey'); }
if( !defined(_FOR_CONTACTCREATED) ) { define('_FOR_CONTACTCREATED', 'Kontakt angelegt'); }
if( !defined(_FOR_CONTACTDELETED) ) { define('_FOR_CONTACTDELETED', 'Kontakt gelöscht'); }
if( !defined(_FOR_CONTACTUPDATED) ) { define('_FOR_CONTACTUPDATED', 'Kontakt gepeichert'); }
if( !defined(_FOR_ERRORCREATINGCONTACT) ) { define('_FOR_ERRORCREATINGCONTACT', 'Fehler beim Erstellen des Kontaktes'); }
if( !defined(_FOR_NOAUTH) ) { define('_FOR_NOAUTH', 'Keine Berechtigung für diese Aktion'); }
if( !defined(_FOR_NOSUCHCONTACT) ) { define('_FOR_NOSUCHCONTACT', 'unbekannter Kontakt'); }

?>
