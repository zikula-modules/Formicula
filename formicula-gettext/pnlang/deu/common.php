<?php
/**
 * Formicula - the contact mailer for Zikula
 * -----------------------------------------
 *
 * @copyright  (c) Formicula Development Team
 * @link       http://code.zikula.org/formicula 
 * @version    $Id$
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Frank Schummertz <frank@zikula.org>
 * @package    Third_Party_Components
 * @subpackage formicula
 */

// new
define('_FOR_SETNUMBERXWITHYFILES', 'Templatesatz #%formid% mit %files% Dateien');
define('_FOR_SETDEFAULTFORM', 'Standardformular (wird verwendet, wenn keine Formular-ID form=xxx im Aufruf übergeben wird)');
define('_FOR_CONFIGURATIONCHANGED', 'Die Konfiguration wurde aktualisiert.');
define('_FOR_CANNOTCREATEFOLDEROUTSIDEWEBROOT', 'pnTemp-Verzeichnis außerhalb des Webroots gefunden, bitte das formicula_cache Verzeichnis manuell anlegen, wie in der Dokumentation beschrieben');

//
// A
//
define('_FOR_ACTIVATESPAMCHECK', 'Spamcheck aktivieren');
define('_FOR_ACTIVATESPAMCHECK_HINT', 'Dies erfordert u.U. Anpassungen an den Templates, weitere Informationen dazu finden sich in der Dokumentation. Diese Option wird automatisch deaktiviert, wenn Formicula keine PHP-Funktion zur Erstellung von Captcha-Grafiken findet!');
define('_FOR_ADDCONTACT', 'Kontakt hinzufügen' );
define('_FOR_ADMINMAIL1', 'ein Besucher der Seite hat das Kontaktformular genutzt und folgendes mitgeteilt:' );
define('_FOR_ADMINMAIL2', 'Der Nutzer hat folgende IP-Adresse/Hostname: ' );
define('_FOR_ADVICE_DATE', 'Bitte ein gültiges Datum eingeben.');
define('_FOR_ADVICE_EMAIL', 'Bitte eine gültige E-Mailadresse in der Form name@domain.de eingeben.');
define('_FOR_ADVICE_MANDATORY', 'Dies ist ein Pflichtfeld, bitte ausfüllen.');
define('_FOR_ADVICE_NUMBER', 'Bitte nur Zahlenwerte eingeben.');
define('_FOR_ADVICE_URL', 'Bitte eine gültige Internetadresse in der Form http://www.domain.de eingeben.');
define('_FOR_ALTERTABLEFAILED', 'konnte Tabelle nicht ändern');

//
// B
//
define('_FOR_BACK', 'Zurück zum Kontaktformular' );
define('_FOR_BADAUTHKEY', 'Ungültiger AuthKey');

//
// C
//
define('_FOR_CACHEDIRPROBLEM', 'formicula_cache-Verzeichnis nicht vorhanden in pnTemp oder Verzeichnis ist nicht beschreibbar, Captcha deaktiviert');
define('_FOR_CANCELDELETE', 'Löschung abbrechen' );
define('_FOR_CLEARIMAGECACHE', 'Captchabilder löschen' );
define('_FOR_CLEAREDIMAGECACHE', 'Die zwischengespeicherten Captchabilder wurde gelöscht' );
define('_FOR_COMMENT', 'Kommentar' );
define('_FOR_COMPANY', 'Firma' );
define('_FOR_CONFIRMDELETE', 'Löschung bestätigen' );
define('_FOR_CONTACTCREATED', 'Kontakt angelegt');
define('_FOR_CONTACTDELETED', 'Kontakt gelöscht'); 
define('_FOR_CONTACTFORM', 'Kontakt');
define('_FOR_CONTACTID','ID');
define('_FOR_CONTACTNAME','Name');
define('_FOR_CONTACTTITLE', 'Kontaktformular' );
define('_FOR_CONTACTUPDATED', 'Kontakt gepeichert');
define('_FOR_CREATECONTACTFAILED', 'Fehler: Konnte Kontakt nicht anlegen');
define('_FOR_CREATEFILESFAILED', 'Während der Installation konnten formicula_cache/index.html und/oder formicula_cache/.htaccess nicht angelegt werden, vor Benutzung des Moduls bitte den Anweisungen im Handbuch folgen!');
define('_FOR_CREATEFOLDERFAILED', 'Während der Installation konnte das formicula_cache-Verzeichnis nicht angelegt werden, vor Benutzung des Moduls bitte den Anweisungen im Handbuch folgen!');
define('_FOR_CREATETABLEFAILED', 'Während der Installation konnte die formcontacts-Tabelle nicht angelegt werden');

//
// D
//
define('_FOR_DBUPGRADEFAILED', 'Datenbankänderung fehlgeschlagen');
define('_FOR_DELETE', 'löschen' );
define('_FOR_DELETECONTACT', 'Kontakt löschen' );
define('_FOR_DELETETABLEFAILED', 'konnte Tabelle nicht löschen');
define('_FOR_DELETEUPLOADEDFILE','Datei nach dem Senden löschen');

//
// E
//
define('_FOR_EDIT', 'editieren' );
define('_FOR_EDITCONFIG', 'Konfiguration' );
define('_FOR_EDITCONTACT', 'Kontakt ändern' );
define('_FOR_EMAIL', 'E-Mail' );
define('_FOR_EMAILFROM', 'E-Mail von');
define('_FOR_ERROR', 'Ein oder mehrere notwendige Felder wurden nicht ausgefüllt oder enthalten fehlerhafte Daten' );
define('_FOR_ERRORCOMMENT', 'Fehler: Keinen oder ungültigen Kommentar abgegeben (kein HTML!)');
define('_FOR_ERRORCONTACT', 'Fehler: Name des Kontakts fehlt');
define('_FOR_ERRORCREATINGCONTACT', 'Fehler beim Erstellen des Kontaktes');
define('_FOR_ERROREMAIL', 'Fehler: keine oder feherhafte E-Mailadresse angegeben');
define('_FOR_ERRORINVALIDEMAIL', 'Fehler: fehlerhafte E-Mailadresse angegeben');
define('_FOR_ERRORNOMANDATORYFIELD', 'Fehler: folgendes Pflichtfeld fehlt');
define('_FOR_ERRORSENDINGMAIL', 'Fehler beim Senden der Mail');
define('_FOR_ERRORSENDINGUSERMAIL', 'interner Fehler: konnte Bestätigungsmail nicht versenden' );
define('_FOR_ERRORUPLOADERROR', 'Fehler: Uploadfehler');
define('_FOR_ERRORUSERNAME', 'Fehler: kein Username angegeben');
define('_FOR_EXCLUDEFROMSPAMCHECK', 'Spamcheck nicht in diesen Formularen verwenden');
define('_FOR_EXCLUDEFROMSPAMCHECK_HINT', 'Kommaseparierte Liste der FormIDs, die z.B. in Pagesetter eingebettet sind. Hier könnte es zu Problemen beim Weiterleiten kommen, wenn der Benutzer die Rechenaufgabe nicht korrekt löst.');

//
// F
//
define('_FOR_FORMICULA', 'Formicula - Kontaktformulare' );
define('_FOR_FORMNUMBER', 'Formular #' );

//
// H
//
define('_FOR_HELLO', 'Hallo,' );
define('_FOR_HTACCESSPROBLEM', 'Notwendige .htaccess Datei in formicula_cache nicht gefunden, Captcha deaktiviert');
define('_FOR_HTMLMAIL', 'HTML-Format' );

//
// I
//
define('_FOR_ILLEGALEMAIL', 'ungültige E-Mailadresse');

//
// L
//
define('_FOR_LOCATION', 'Ort' );

//
// M
//
define('_FOR_MUSTBE', 'Notwendige Felder' );

//
// N
//
define('_FOR_NAMEOFCONTACT', 'Bezeichnung' );
define('_FOR_NAME', 'Ihr Name' );
define('_FOR_NOAUTH', 'Keine Berechtigung für diese Aktion');
define('_FOR_NOAUTHFORFORM', 'Keine Berechtigung für dieses Formular');
define('_FOR_NOCONTACTS', 'keine Kontakt gefunden');
define('_FOR_NOFORMSELECTED', 'kein Formular ausgewählt');
define('_FOR_NOIMAGEFUNCTION', 'Keine Funktion zur Bilderzeugung verfügbar - Captcha deaktiviert');
define('_FOR_NOMAILERMODULE', 'Mailer-Modul nicht verfügbar, E-Mails können nicht verssendet werden!');
define('_FOR_NOSUCHCONTACT', 'unbekannter Kontakt');

//
// O
//
define('_FOR_ONLINEAPPLYAS', 'Bewerbung als' );
define('_FOR_ONLINEBIRTHDATE', 'Geburtsdatum');
define('_FOR_ONLINEDATE', 'Eintrittstermin');
define('_FOR_ONLINEJOBAPPLY', 'Onlinebewerbung' );
define('_FOR_ONLINEPRIVACY', 'Vielen Dank für Ihre Bewerbung, Ihre Daten werden streng vertraulich behandelt' );
define('_FOR_ONLINESALARY', 'Gehaltsvorstellung');
define('_FOR_ONLINESTREET', 'Strasse');
define('_FOR_ONLINEZIPCITY', 'PLZ Ort');
define('_FOR_OPTIONS', 'Optionen' );

//
// P
//
define('_FOR_PHONE', 'Telefon' );
define('_FOR_PUBLIC', 'Öffentlich' );

//
// R
//
define('_FOR_RESUME','Lebenslauf');

//
// S
//
define('_FOR_SEND', 'Senden' );
define('_FOR_SENDEREMAIL', 'E-Mail des Absenders');
define('_FOR_SENDERINFO', 'Verwende folgende Daten für die Bestätigungsmail an den Benutzer');
define('_FOR_SENDERNAME', 'Absendername');
define('_FOR_SENDERSUBJECT', 'Betreff');
define('_FOR_SENDERSUBJECTHINT', '
mit <ul>
    <li>%s = Seitenname</li>
    <li>%l = Slogan</li>
    <li>%u = URL der Seite</li>
    <li>%c = Absendername des Kontakts</li>
    <li>%n&lt;num&gt; = Name des userdefinerten Feldes &lt;num&gt;</li>
    <li>%d&lt;num&gt; = Inhalt des userdefinierten Feldes &lt;num&gt;</li>
</ul>
');
define('_FOR_SENDTOADMIN', 'Folgende Daten wurden gesendet' );
define('_FOR_SENDTOUSER', 'Zur Bestätigung werden die gesendeten Daten auch nochmal an die angegebene E-Mail-Adresse verschickt.' );
define('_FOR_SENDUSER', 'Bestätigungsmail an User verschicken?' );
define('_FOR_SHOWCOMMENT', 'Kommentarfeld anzeigen?' );
define('_FOR_SHOWCOMPANY', 'Firma anzeigen?' );
define('_FOR_SHOWLOCATION', 'Standort anzeigen?' );
define('_FOR_SHOWPHONE', 'Telefonnummer anzeigen?' );
define('_FOR_SHOWURL', 'Homepage anzeigen?' );
define('_FOR_SIMPLEMATHEQUATION', 'Bitte diese einfache Rechenaufgabe lösen');
define('_FOR_SUBMIT', 'Absenden' );

//
// T
//
define('_FOR_TEAM', 'Team' );
define('_FOR_TEXTMAIL', 'normaler Text' );
define('_FOR_THANKS', 'Vielen Dank für die Frage/den Kommentar zu unserer Website!<br>Falls gewünscht, werden wir unverzüglich antworten.' );
define('_FOR_THE', 'Das' );
define('_FOR_THEME', 'Thema' );

//
// U
//
define('_FOR_UPDATECONTACTFAILED', 'Fehler: Konnte Kontakt nicht aktualisieren!');
define('_FOR_UPLOADDIRNOTWRITABLE','Dieses Verzeichnis ist vom Webserver nicht beschreibbar' );
define('_FOR_UPLOADERROR1', 'Upload-Fehler: Datei zu gross (php.ini)' );
define('_FOR_UPLOADERROR2', 'Upload-Fehler: Datei zu gross (form)' );
define('_FOR_UPLOADERROR3', 'Upload-Fehler: Datei nur teilweise erhalten' );
define('_FOR_UPLOADERROR4', 'Upload-Fehler: keine Datei erhalten' );
define('_FOR_UPLOADFILEDIR', 'Verzeichnis für Dateiupload');
define('_FOR_UPLOADLIMIT', '(Upload, max. 2MB)');
define('_FOR_URL', 'Homepage' );
define('_FOR_USERMAIL1', 'vielen Dank für die Frage/den Kommentar zu unserer Website. Die gesendeten Daten sind:' );
define('_FOR_USERMAIL2', 'Wir werden, falls gewünscht, unverzüglich antworten.' );
define('_FOR_USERMAILFORMAT', 'Bestätigungsmail');

//
// V
//
define('_FOR_VIEWCONTACT', 'Kontakte anzeigen' );
define('_FOR_VISITHOMEPAGE', 'Formicula auf http://code.zikula.org besuchen');

//
// W
//
define('_FOR_WRONGCAPTCHA', 'Schlecht im Kopfrechnen? Bitte erneut versuchen!');
