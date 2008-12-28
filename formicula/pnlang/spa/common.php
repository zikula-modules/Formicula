<?php
// $Id$
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Frank Schummertz
// Purpose of file:  language file
// ----------------------------------------------------------------------

/**
 * Translated by
 * @author  Mateo Tibaquira [mateo]
 */

// new
define('_FOR_SETNUMBERXWITHYFILES', 'Set #%formid% with %files% templates');
define('_FOR_SETDEFAULTFORM', 'Set the default form (used when no form is specified');
define('_FOR_CONFIGURATIONCHANGED', 'The configuration has been changed.');
define('_FOR_CANNOTCREATEFOLDEROUTSIDEWEBROOT', 'pnTemp folder found outside of the webroot, please consult the manual of how to create the formicula_cache folder in this case.');

//
// A
//
define('_FOR_ACTIVATESPAMCHECK', 'Activar verificación antispam');
define('_FOR_ACTIVATESPAMCHECK_HINT', 'Asegúrate de que los campos del formulario necesarios<br />están disponibles, mira los documentos para más información. Esta opción será desactivada por Formicula automáticamente si no existen las funciones de PHP para crear imágenes');
define('_FOR_ADDCONTACT','Añadir contacto' );
define('_FOR_ADMINMAIL1', 'un visitante de tu sitio web usó el formulario de contacto y envió lo siguiente:' );
define('_FOR_ADMINMAIL2', 'El usuario tenía la siguiente dirección IP (o nombre de host): ' );
define('_FOR_ADVICE_EMAIL', 'Por favor digita un correo electrónico válido como usuario@ejemplo.com.');
define('_FOR_ADVICE_MANDATORY', 'Este es un campo obligatorio.');
define('_FOR_ADVICE_URL', 'Por favor digita una dirección de internet válida como http://www.ejemplo.com.');
define('_FOR_ALTERTABLEFAILED', 'no se pudo alterar la tabla');

//
// B
//
define('_FOR_BACK', 'Volver al formulario de contacto' );
define('_FOR_BADAUTHKEY', 'Clave de autorización no válida');

//
// C
//
define('_FOR_CACHEDIRPROBLEM', 'La carpeta formicula_cache no existe en la carpeta temporal de Zikula\'s o no es escribible - las captchas fueron deshabilitadas');
define('_FOR_CANCELDELETE','Cancelar eliminación de rutina' );
define('_FOR_CLEARIMAGECACHE', 'Clear captcha image cache' );
define('_FOR_COMMENT', 'Comentario' );
define('_FOR_COMPANY', 'Empresa' );
define('_FOR_CONFIRMDELETE','Click aquí para borrar este contacto' );
define('_FOR_CONTACTCREATED', 'Contacto creado');
define('_FOR_CONTACTDELETED', 'El contacto ha sido borrado');
define('_FOR_CONTACTFORM', 'Formulario de Contacto');
define('_FOR_CONTACTID','ID' );
define('_FOR_CONTACTNAME','Nombre');
define('_FOR_CONTACTTITLE', 'Contáctenos' );
define('_FOR_CONTACTUPDATED', 'La información del contacto ha sido actualizada');
define('_FOR_CREATECONTACTFAILED', 'Error creando el contacto!');
define('_FOR_CREATEFILESFAILED', 'El instalador no pudo crear el archivo formicula_cache/index.html y/o formicula_cache/.htaccess, por favor lee el manual antes de usar el módulo!');
define('_FOR_CREATEFOLDERFAILED', 'El instalador no pudo crear la carpeta formicula_cache, por favor lee el manual antes de usar el módulo!');
define('_FOR_CREATETABLEFAILED', 'El instalador no pudo crear la tabla formcontacts');

//
// D
//
define('_FOR_DELETE','Borrar contacto' );
define('_FOR_DELETECONTACT','Borrar contacto' );
define('_FOR_DELETETABLEFAILED', 'No se pudo borrar la tabla');
define('_FOR_DELETEUPLOADEDFILE','Borrar archivo después del envio' );
define('_FOR_DESC', 'Herramientas para crear todo tipo de formularios de contacto');

//
// E
//
define('_FOR_EDIT','Editar contacto' );
define('_FOR_EDITCONFIG','Modificar configuración' );
define('_FOR_EDITCONTACT','Editar contacto' );
define('_FOR_EMAIL','Correo electrónico' );
define('_FOR_EMAILFROM', 'Correo electrónico de');
define('_FOR_ERROR', 'Hay un error en tu formulario' );
define('_FOR_ERRORCOMMENT', 'Error: El comentario no fue suministrado o no es válido (HTML prohibido!)');
define('_FOR_ERRORCONTACT', 'Error: No escogiste el nombre del contacto');
define('_FOR_ERRORCREATINGCONTACT', 'No se pudo crear el contacto!');
define('_FOR_ERROREMAIL', 'Error: El correo electrónico no fue suministrado o no es válido');
define('_FOR_ERRORINVALIDEMAIL', 'Error: Correo electrónico suministrado incorrecto');
define('_FOR_ERRORNOMANDATORYFIELD', 'Error: falta campo obligatorio');
define('_FOR_ERRORSENDINGMAIL', 'Hubo un error enviando el correo.');
define('_FOR_ERRORSENDINGUSERMAIL', 'Hubo un error interno enviando el correo de confirmación' );
define('_FOR_ERRORUPLOADERROR', 'Error: Error subiendo archivo');
define('_FOR_ERRORUSERNAME', 'Error: Nombre de usuario no suministradono username');
define('_FOR_EXCLUDEFROMSPAMCHECK', 'No usar verificación antispam en estos formularios');
define('_FOR_EXCLUDEFROMSPAMCHECK_HINT', 'Llista de los ods de formularios separados por comas, ej. formularios embebidos en pagesetter. El redireccionamiento quizas no funcione correctamente aqui');

//
// F
//
define('_FOR_FORMICULA','Formicula!' );
define('_FOR_FORMNUMBER', 'Formulario #' );

//
// H
//
define('_FOR_HELLO', 'Hola,' );
define('_FOR_HTACCESSPROBLEM', 'el archivo indispensable .htaccess en la carpeta formicula_cache no existe');
define('_FOR_HTMLMAIL', 'HTML' );

//
// I
//
define('_FOR_ILLEGALEMAIL', 'Correo electrónico inválido detectado');

//
// L
//
define('_FOR_LOCATION', 'Ubicación' );

//
// M
//
define('_FOR_MUSTBE', 'Campo obligatorio' );

//
// N
//
define('_FOR_NAME', 'Tu nombre' );
define('_FOR_NAMEOFCONTACT','Nombre del contacto' );
define('_FOR_NOAUTH', 'No tienes autorización para ejecutar esta acción.');
define('_FOR_NOAUTHFORFORM', 'No tienes autorización para ver este formulario.');
define('_FOR_NOCONTACTS', 'No se encontraron contactos.');
define('_FOR_NOFORMSELECTED', 'no form selected');
define('_FOR_NOIMAGEFUNCTION', 'funciones de PHP para manipular imágenes no disponibles - captcha desactivado');
define('_FOR_NOMAILERMODULE', 'El módulo de correo no está disponible - incapaz de enviar correos!');
define('_FOR_NOSUCHCONTACT', 'Contacto desconocido');

//
// O
//
define('_FOR_ONLINEAPPLYAS', 'Aplicar como' );
define('_FOR_ONLINEBIRTHDATE', 'fecha de nacimiento' );
define('_FOR_ONLINECOUNTRY', 'País' );
define('_FOR_ONLINEDATE', 'Fecha de entrada' );
define('_FOR_ONLINEJOBAPPLY', 'Aplicar en línea!' );
define('_FOR_ONLINEPRIVACY', 'Gracias por aplicar, mantendremos tus datos en estricta confidencialidad' );
define('_FOR_ONLINESALARY', 'Salario' );
define('_FOR_ONLINESTREET', 'Calle' );
define('_FOR_ONLINEZIPCITY', 'ZIP Ciudad' );
define('_FOR_OPTIONS','Opciones' );

//
// P
//
define('_FOR_PHONE', 'Número telefónico' );
define('_FOR_PUBLIC', 'Público' );

//
// R
//
define('_FOR_RESUME','Reanudar');

//
// S
//
define('_FOR_SEND', 'Enviar' );
define('_FOR_SENDEREMAIL', 'Correo del remitente');
define('_FOR_SENDERINFO', 'Usar esta información en el correo de confirmación a los usuarios');
define('_FOR_SENDERNAME', 'Nombre del remitente');
define('_FOR_SENDERSUBJECT', 'Asunto');
define('_FOR_SENDERSUBJECTHINT', '
con <ul>
    <li>%s = nombre del sitio</li>
    <li>%l = slogan</li>
    <li>%u = url del sitio</li>
    <li>%c = nombre del remitente</li>
    <li>%n&lt;num&gt; = nombre de campo definido por el usuario &lt;num&gt;</li>
    <li>%d&lt;num&gt; = dato de campo definido por el usuario &lt;num&gt;</li>
</ul>
');
define('_FOR_SENDTOADMIN', 'Esta información fue enviada a nosotros:');
define('_FOR_SENDTOUSER', 'Una confirmación de tu envio será enviada a tu correo electrónico en unos minutos.' );
define('_FOR_SENDUSER','Send confirmation email to user?' );
define('_FOR_SHOWCOMMENT', 'Mostrar texto para el comentario' );
define('_FOR_SHOWCOMPANY','Mostrar empresa?' );
define('_FOR_SHOWLOCATION','Mostrar ubicación?' );
define('_FOR_SHOWPHONE','Mostrar número telefónico?' );
define('_FOR_SHOWURL','Mostrar URL?' );
define('_FOR_SIMPLEMATHEQUATION', 'Por favor soluciona esta simple ecuación matemática');
define('_FOR_SUBMIT', 'Actualizar configuración' );

//
// T
//
define('_FOR_TEAM', 'Equipo' );
define('_FOR_TEXTMAIL', 'Texto' );
define('_FOR_THANKS', 'Gracias por tus preguntas/comentarios a nuestro sitio web!<br>Te responderemos lo más pronto posible.' );
define('_FOR_THE', 'El' );
define('_FOR_THEME', 'Contacto' );

//
// U
//
define('_FOR_UPDATECONTACTFAILED', 'Error actualizando contacto!');
define('_FOR_UPLOADDIRNOTWRITABLE','El servidor web no puede escribir en esta carpeta!' );
define('_FOR_UPLOADERROR1', 'error de carga: archivo muy grande (php.ini)' );
define('_FOR_UPLOADERROR2', 'error de carga: archivo muy grande (formulario)' );
define('_FOR_UPLOADERROR3', 'error de carga: archivo recibido parcialmente' );
define('_FOR_UPLOADERROR4', 'error de carga: no se recibió ningún archivo' );
define('_FOR_UPLOADFILEDIR', 'Carpeta para subir archivos' );
define('_FOR_UPLOADLIMIT', '(Subir, max. 2MB)');
define('_FOR_URL', 'Página de inicio' );
define('_FOR_USERMAIL1', 'Gracias por los comentarios enviados desde nuestro sitio Web. Los datos enviados son:' );
define('_FOR_USERMAIL2', 'Te responderemos a tu correo lo más pronto posiible.' );
define('_FOR_USERMAILFORMAT', 'Formato del correo' );

//
// V
//
define('_FOR_VIEWCONTACT','Ver contactos' );
define('_FOR_VISITHOMEPAGE', 'Visitar Formicula en el http://code.zikula.org');

//
// W
//
define('_FOR_WRONGCAPTCHA', 'Mal en matemáticas? Puedes hacerlo mejor, intenta de nuevo.');

