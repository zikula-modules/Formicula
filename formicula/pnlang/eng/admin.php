<?php
// $Id$
// ----------------------------------------------------------------------
// Original Author of file: Sebastian Fränk
// Purpose of file: english Translation Files
// ----------------------------------------------------------------------
//

//new
define('_FOR_EXCLUDEFROMSPAMCHECK', 'Do not use spam check in these forms<br />(comma separated list of form ids, e.g. embedded forms in pagesetter. The redirect may not work here correctly');
define('_FOR_ACTIVATESPAMCHECK', 'Activate spamcheck<br />(make sure you the necessary form fields<br />are available, see the docs for more information');
define('_FOR_VISITHOMEPAGE', 'Visit Formicula on NOC');
define('_FOR_ILLEGALEMAIL', 'invalid email address detected');
define('_FOR_SENDERINFO', 'Use this information in the users confirmation mail');
define('_FOR_SENDERNAME', 'Sender name');
define('_FOR_SENDEREMAIL', 'Sender email');
define('_FOR_SENDERSUBJECT', 'Subject');
define('_FOR_SENDERSUBJECTHINT', '
with <ul>
    <li>%s = sitename</li>
    <li>%l = slogan</li>
    <li>%u = site url</li>
    <li>%c = contacts sender name</li>
    <li>%n&lt;num&gt; = user defined field name &lt;num&gt;</li>
    <li>%d&lt;num&gt; = user defined field data &lt;num&gt;</li>
</ul>
');

//original
define( '_FOR_ADDCONTACT','Add contact' );
define( '_FOR_CANCELDELETE','Cancel deletion routine' );
define( '_FOR_CONFIRMDELETE','Click here to delete this contact' );
define( '_FOR_CONTACTID','ID' );
define( '_FOR_DELETE','Delete contact' );
define( '_FOR_DELETECONTACT','Delete contact' );
define( '_FOR_DELETEUPLOADEDFILE','Delete file after sending' );
define( '_FOR_EDIT','Edit contact' );
define( '_FOR_EDITCONFIG','Change configuration' );
define( '_FOR_EDITCONTACT','Edit contact' );
define( '_FOR_EMAIL','Email' );
define( '_FOR_FORMICULA','Formicula!' );
define( '_FOR_NAME','Contact name' );
define( '_FOR_OPTIONS','Options' );
define( '_FOR_PUBLIC', 'Public' );
define( '_FOR_SENDUSER','Send confirmation email to user?' );
define( '_FOR_SHOWCOMMENT', 'Show comments textarea' );
define( '_FOR_SHOWCOMPANY','Show company?' );
define( '_FOR_SHOWLOCATION','Show location?' );
define( '_FOR_SHOWPHONE','Show phone number?' );
define( '_FOR_SHOWURL','Show URL?' );
define( '_FOR_UPLOADDIRNOTWRITABLE','The webserver cannot write into this folder!' );
define( '_FOR_UPLOADFILEDIR', 'Folder for uploaded files' );
define( '_FOR_VIEWCONTACT','View contacts' );

if( !defined(_FOR_BADAUTHKEY) ) { define('_FOR_BADAUTHKEY', 'Bad AuthKey'); }
if( !defined(_FOR_CONTACTCREATED) ) { define('_FOR_CONTACTCREATED', 'Contact created'); }
if( !defined(_FOR_CONTACTDELETED) ) { define('_FOR_CONTACTDELETED', 'Contact has been Delete'); }
if( !defined(_FOR_CONTACTUPDATED) ) { define('_FOR_CONTACTUPDATED', 'Contact info has been updated'); }
if( !defined(_FOR_ERRORCREATINGCONTACT) ) { define('_FOR_ERRORCREATINGCONTACT', 'Unable to create contact!'); }
if( !defined(_FOR_NOAUTH) ) { define('_FOR_NOAUTH', 'You are not allowed to administer the Formicula module!'); }
if( !defined(_FOR_NOSUCHCONTACT) ) { define('_FOR_NOSUCHCONTACT', 'We do not have a contact with that name.'); }

?>
