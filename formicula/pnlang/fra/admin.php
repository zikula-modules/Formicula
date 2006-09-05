<?php
// $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------

//new 
define('_FOR_EXCLUDEFROMSPAMCHECK', 'Ne pas activer l\'anti-spam pour ces formulaires 
(utiliser la virgule pour séparer les ID des formulaires, 
ex : formulaires incorporés dans pagesetter. La redirection 
peut ne pas fonctionner correctement pour ceux-ci)'); 
define('_FOR_ACTIVATESPAMCHECK', 'Activer l\'anti-spam 
(il faut être certain que les champs 
du formulaire soient disponibles, 
lire la documentation pour plus d\'informations) 
'); 
define('_FOR_VISITHOMEPAGE', 'Se rendre sur le NOC de Formicula'); 
define('_FOR_ILLEGALEMAIL', 'Adresse e-mail invalide détectée'); 
define('_FOR_SENDERINFO', 'Utiliser cette information dans le mail de confirmation envoyé à l\'expéditeur'); 
define('_FOR_SENDERNAME', 'Nom de l\'expéditeur'); 
define('_FOR_SENDEREMAIL', 'E-mail de l\'expéditeur'); 
define('_FOR_SENDERSUBJECT', 'Sujet'); 
define('_FOR_SENDERSUBJECTHINT', ' 
avec <ul> 
    <li>%s = nom du site</li> 
    <li>%l = slogan du site</li> 
    <li>%u = url du site</li> 
    <li>%c = nom de l\'expéditeur</li> 
    <li>%n<num> = nom de champ défini pour l\'utilisateur<num></li> 
    <li>%d<num> = nom de champ défini pour les données<num></li> 
</ul> 
'); 
 
//original 
define( '_FOR_ADDCONTACT','Ajouter un contact' ); 
define( '_FOR_CANCELDELETE','Annuler la supression automatique' ); //?? Cancel deletion routine 
define( '_FOR_CONFIRMDELETE','Cliquer ici pour supprimer ce contact' ); 
define( '_FOR_CONTACTID','ID' ); 
define( '_FOR_DELETE','Supprimer le contact' ); 
define( '_FOR_DELETECONTACT','Supprimer le contact' ); 
define( '_FOR_DELETEUPLOADEDFILE','Supprimer le fichier après l\'envoi' ); 
define( '_FOR_EDIT','Editer le contact' ); 
define( '_FOR_EDITCONFIG','Modifier la configuration' ); 
define( '_FOR_EDITCONTACT','Editer le contact' ); 
define( '_FOR_EMAIL','E-mail' ); 
define( '_FOR_FORMICULA','Formicula!' ); 
define( '_FOR_NAME','Nom du contact' ); 
define( '_FOR_OPTIONS','Options' ); 
define( '_FOR_PUBLIC', 'Public' ); 
define( '_FOR_SENDUSER','Envoyer un e-mail de confirmation à l\'expéditeur ?' ); 
define( '_FOR_SHOWCOMMENT', 'Afficher la zone de commentaire' ); 
define( '_FOR_SHOWCOMPANY','Afficher la société ?' ); 
define( '_FOR_SHOWLOCATION','Afficher l\'adresse?' ); 
define( '_FOR_SHOWPHONE','Afficher le numéro de téléphone ?' ); 
define( '_FOR_SHOWURL','Afficher l\'url ?' ); 
define( '_FOR_UPLOADDIRNOTWRITABLE','Le serveur ne peut pas écrire dans ce dossier !' ); 
define( '_FOR_UPLOADFILEDIR', 'Dossier d\'upload des fichiers' ); 
define( '_FOR_VIEWCONTACT','Afficher les contacts' ); 
 
if( !defined(_FOR_BADAUTHKEY) ) { define('_FOR_BADAUTHKEY', 'Mauvaise clé d\'authentification'); } 
if( !defined(_FOR_CONTACTCREATED) ) { define('_FOR_CONTACTCREATED', 'Contact créé'); } 
if( !defined(_FOR_CONTACTDELETED) ) { define('_FOR_CONTACTDELETED', 'Contact supprimé'); } 
if( !defined(_FOR_CONTACTUPDATED) ) { define('_FOR_CONTACTUPDATED', 'Contact mis à jour'); } 
if( !defined(_FOR_ERRORCREATINGCONTACT) ) { define('_FOR_ERRORCREATINGCONTACT', 'Impossible de créer le contact !'); } 
if( !defined(_FOR_NOAUTH) ) { define('_FOR_NOAUTH', 'Vous n\'êtes pas autorisé d\'administrer le module Formicula !'); } 
if( !defined(_FOR_NOSUCHCONTACT) ) { define('_FOR_NOSUCHCONTACT', 'Nous n\'avons pas de contact ayant ce nom.'); }
?>
