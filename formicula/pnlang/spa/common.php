<?php
/**
 * Avatar Module
 * 
 * The Avatar module allows uploading of individual Avatars.
 * It is based on EnvoAvatar from A.T.Web, http://www.atw.it
 *
 * @package      Avatar
 * @version      $Id: common.php 72 2008-03-24 14:45:09Z landseer $
 * @author       Joerg Napp, Frank Schummertz
 * @link         http://lottasophie.sf.net, http://www.pn-cms.de
 * @copyright    Copyright (C) 2004-2007
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Translated by
 * @author       Mateo Tibaquirá
 */

//
// A
//
define('_AVATAR_ADM_ALLOWMULTIPLEAVATARS',    'Permitir multiples avatars');
define('_AVATAR_ADM_ALLOWRESIZE',             'Redimensionar el avatar automáticamente');
define('_AVATAR_ADM_AVATARDIR',               'Carpeta de Avatar (PostNuke)');
define('_AVATAR_ADM_AVATARDIR_HINT',          'Por defecto: images/avatar <strong>sin barra final</strong>');
define('_AVATAR_ADM_EXTENSIONS',              'Extensiones permitidas');
define('_AVATAR_ADM_EXTENSIONS_HINT',         '(una lista separada por punto y coma de las extensiones permitidas. Tipos soportados: gif, jpg, jpeg, png, wbm. Al usar PHP 5 o mayor tienes que permitir \'jpeg\' en vez de \'jpg\')');
define('_AVATAR_ADM_FORUMDIR',                'Carpeta de Avatars (phpBB');
define('_AVATAR_ADM_MAXHEIGHT',               'Alto max. en pixeles');
define('_AVATAR_ADM_MAXSIZE',                 'Tamaño max. del archivo en bytes');
define('_AVATAR_ADM_MAXWIDTH',                'Ancho max. en pixeles');
define('_AVATAR_ADM_MULTIPLE_HINT',           'Esto le permite al usuario almacenar un avatar por extensión');
define('_AVATAR_ADM_TITLE',                   'Administración de Avatar');
define('_AVATAR_ADM_UPLOAD',                  'Configuración de archivos');
define('_AVATAR_ALLOWEDEXTENSIONS',           'Extensiones permitidas');
define('_AVATAR_AVATARINUSE',                 'Advertencia: Este avatar está en uso y no puede ser borrado. Si quieres borrarlo, por favor cambia los avatars de los usuarios listados a continuación.');

//
// C
//
define('_AVATAR_CHANGEDTO',                   'El avatar del usuario %username% fue cambiado a %avatar%');
define('_AVATAR_CLEAR_BUTTON',                'Limpiar');
define('_AVATAR_CONFIRMDELETE',               'Confirmar eliminación');
define('_AVATAR_CURRENTAVATAR',               'Tu avatar actual es ');

//
// D
//
define('_AVATAR_DELETEAVATAR',                'borrar avatar');
define('_AVATAR_DELETECURRENTAVATAR',         'Borrar avatar actual?');
define('_AVATAR_DELETED',                     'El avatar %avatar% ha sido borrado');

//
// E
//
define('_AVATAR_ENTERUSERNAME',               'Nombre de usuario');
define('_AVATAR_ERRORDELETINGAVATAR',         'Error: No se pudo borrar el avatar %avatar%');
define('_AVATAR_ERR_AUTHORIZED',              'No estas autorizado para subir tu avatar.');
define('_AVATAR_ERR_COPYAVATAR',              'Falla al copiar el archivo a la carpeta de avatars.');
define('_AVATAR_ERR_COPYFORUM',               'Falla al copiar el archivo a la carpeta de phpbb.');
define('_AVATAR_ERR_FILEDIMENSIONS',          'Error en el alto (max. %h%px) o ancho (max. %w%px) de la imágen.');
define('_AVATAR_ERR_FILESIZE',                'Error en tamaño de archivo, sólo se permite un máximo de %max% bytes.');
define('_AVATAR_ERR_FILETYPE',                'Extensión de archivo no está dentro de las permitidas: %ft%.');
define('_AVATAR_ERR_FILEUPLOAD',              'No se seleccionó ningún archivo.');
define('_AVATAR_ERR_NOIMAGE',                 'El archivo subido no es una imágen');
define('_AVATAR_ERR_NOTLOGGEDIN',             'No eres un usuario registrado.');
define('_AVATAR_ERR_SELECT',                  'Error mientras se seleccionaba el avatar.');
define('_AVATAR_ERR_USERNOTAUTHORIZED',       'No estás autorizado para usar este avatar. Para cambiar esto, actualiza el permiso para %avatar%.');

//
// L
//
define('_AVATAR_LISTUSERS',                   'lista de usuarios que usan este avatar');

//
// M
//
define('_AVATAR_MAINTAINAVATARS',             'Mantenimiento de Avatars');
define('_AVATAR_MAXDIMENSIONS',               'Dimensiones máximas');
define('_AVATAR_MAXHEIGHT',                   'Alto máximo');
define('_AVATAR_MAXSIZE',                     'Tamaño max. del Avatar');
define('_AVATAR_MAXWIDTH',                    'Ancho máximo');
define('_AVATAR_MISSINGPATH',                 'ruta no encontrada');
define('_AVATAR_MODIFYCONFIG',                'Modificar configuración');

//
// N
//
define('_AVATAR_NOUSERFORTHISAVATAR',         'Ningún usuario está usando este avatar');
define('_AVATAR_NOAVATARSELECTED',            'ningún avatar seleccionado');

//
// P
//
define('_AVATAR_PATHDOESNOTEXIST',            'La ruta %path% no existe o no es accesible para el servidor.');
define('_AVATAR_PATHISNOTWRITABLE',           'El servidor no puede escribir en %path%.');
define('_AVATAR_PIXEL',                       'pixeles');

//
// R
//
define('_AVATAR_RESIZE',                      'Imágenes grandes serán redimensionadas automáticamente.');

//
// S
//
define('_AVATAR_SEARCHUSERS',                 'Buscar usuario');
define('_AVATAR_SELECTAVATAR',                'Seleccionar avatar');
define('_AVATAR_SELECTAVATARFORUSERS',        'Escoge el avatar para actualizar a todos los usuarios seleccionados');
define('_AVATAR_SELECTAVATAR_LINK',           'Cambiar Avatar');
define('_AVATAR_SELECTEDAVATAR',              'Avatar seleccionado');
define('_AVATAR_SELECTNEWAVATAR',             'Seleccionar un nuevo avatar');
define('_AVATAR_SELECTYOURAVATAR',            'Escoge tu avatar preferido');

//
// T
//
define('_AVATAR_TITLE',                       'Avatar');

//
// U
//
define('_AVATAR_UPLOADFILE',                  'Subir archivo');
define('_AVATAR_UPLOAD_BUTTON',               'Subir');
define('_AVATAR_USERLISTPERAVATAR',           'usuarios que usan este avatar');
define('_AVATAR_USER_CHOOSE',                 'Si ninguno de los avatars disponibles te representa, puedes subir tu propio avatar.');

//
// V
//
define('_AVATAR_VISITHOMEPAGE',               'Visita el proyecto Avatar en el NOC');

//
// w
//
define('_AVATAR_WARN_AVARTARDIR',             'Advertencia: el servidor no puede escribir en la carpeta de avatars');
define('_AVATAR_WARN_FORUMDIR',               'Advertencia: el servidor no puede escribir en la carpeta de avatars de los foros (no es problema cuando PNphpBB2 no está instalado)');
