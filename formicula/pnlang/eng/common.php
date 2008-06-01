<?php
// $Id: common.php 107 2008-05-23 09:22:59Z landseer $
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

//
// A
//
define('_FOR_ACTIVATESPAMCHECK', 'Activate spamcheck<br />(make sure you the necessary form fields<br />are available, see the docs for more information. This option will be turned off by Formicula automatically if no PHP-functions for creating images are available)');
define('_FOR_ADDCONTACT','Add contact' );
define('_FOR_ADMINMAIL1', 'A visitor of to your web site used the form for contact and sent the following:' );
define('_FOR_ADMINMAIL2', 'The user has the following IP address/hostname: ' );
define('_FOR_ADVICE_EMAIL', 'Please enter a valid emailaddress like user@example.com.');
define('_FOR_ADVICE_MANDATORY', 'This is a mandatory field.');
define('_FOR_ADVICE_URL', 'Please enter a valid internet address like http://www.example.com.');
define('_FOR_ALTERTABLEFAILED', 'could not alter table');

//
// B
//
define('_FOR_BACK', 'Back to Contact Form' );
define('_FOR_BADAUTHKEY', 'Bad AuthKey');

//
// C
//
define('_FOR_CACHEDIRPROBLEM', 'formicula_cache folder does not exist in PostNuke\'s temporary folder or is not writable - captchas have been disabled');
define('_FOR_CANCELDELETE','Cancel deletion routine' );
define('_FOR_CLEARIMAGECACHE', 'Clear captcha image cache' );
define('_FOR_COMMENT', 'Comment' );
define('_FOR_COMPANY', 'Company' );
define('_FOR_CONFIRMDELETE','Click here to delete this contact' );
define('_FOR_CONTACTCREATED', 'Contact created');
define('_FOR_CONTACTDELETED', 'Contact has been Delete');
define('_FOR_CONTACTFORM', 'Contact Form');
define('_FOR_CONTACTID','ID' );
define('_FOR_CONTACTNAME','Name');
define('_FOR_CONTACTTITLE', 'Contact our team' );
define('_FOR_CONTACTUPDATED', 'Contact info has been updated');
define('_FOR_CREATECONTACTFAILED', 'Error creating contact!');
define('_FOR_CREATEFILESFAILED', 'The installer could not create formicula_cache/index.html and/or formicula_cache/.htaccess, please refer to the manual before using the module!');
define('_FOR_CREATEFOLDERFAILED', 'The installer could not create the formicula_cache folder, please refer to the manual before using the module!');
define('_FOR_CREATETABLEFAILED', 'The installer could not create the formcontacts table');

//
// D
//
define('_FOR_DBUPGRADEFAILED', 'Database upgrade failed');
define('_FOR_DELETE','Delete contact' );
define('_FOR_DELETECONTACT','Delete contact' );
define('_FOR_DELETETABLEFAILED', 'could not delete table');
define('_FOR_DELETEUPLOADEDFILE','Delete file after sending' );
define('_FOR_DESC', 'Tools for creation of all kinds contact forms');

//
// E
//
define('_FOR_EDIT','Edit contact' );
define('_FOR_EDITCONFIG','Modify configuration' );
define('_FOR_EDITCONTACT','Edit contact' );
define('_FOR_EMAIL','Email' );
define('_FOR_EMAILFROM', 'Email from');
define('_FOR_ERROR', 'There is an error in your form' );
define('_FOR_ERRORCOMMENT', 'Error: no or invalid comment supplied (no HTML!)');
define('_FOR_ERRORCONTACT', 'Error: no contact name');
define('_FOR_ERRORCREATINGCONTACT', 'Unable to create contact!');
define('_FOR_ERROREMAIL', 'Error: no or incorrect email address supplied');
define('_FOR_ERRORINVALIDEMAIL', 'Error: incorrect email address supplied');
define('_FOR_ERRORNOMANDATORYFIELD', 'Error: missing mandatory field');
define('_FOR_ERRORSENDINGMAIL', 'There was an error sending the email.');
define('_FOR_ERRORSENDINGUSERMAIL', 'There was an internal error when sending confirmation mail' );
define('_FOR_ERRORUPLOADERROR', 'Error: Upload error');
define('_FOR_ERRORUSERNAME', 'Error: no username');
define('_FOR_EXCLUDEFROMSPAMCHECK', 'Do not use spam check in these forms<br />(comma separated list of form ids, e.g. embedded forms in pagesetter. The redirect may not work here correctly');

//
// F
//
define('_FOR_FORMICULA','Formicula!' );
define('_FOR_FORMNUMBER', 'Form #' );

//
// H
//
define('_FOR_HELLO', 'Hello,' );
define('_FOR_HTACCESSPROBLEM', '.htaccess file needed in formicula_cache folder not exist');
define('_FOR_HTMLMAIL', 'HTML' );

//
// I
//
define('_FOR_ILLEGALEMAIL', 'invalid email address detected');

//
// L
//
define('_FOR_LOCATION', 'Location' );

//
// M
//
define('_FOR_MUSTBE', 'Mandatory field' );

//
// N
//
define('_FOR_NAME', 'Your Name' );
define('_FOR_NAMEOFCONTACT','Contact name' );
define('_FOR_NOAUTH', 'No authorization for this action.');
define('_FOR_NOAUTHFORFORM', 'No authorization for this form.');
define('_FOR_NOCONTACTS', 'No contacts found.');
define('_FOR_NOFORMSELECTED', 'no form selected');
define('_FOR_NOIMAGEFUNCTION', 'no image function available - captcha deactivated');
define('_FOR_NOMAILERMODULE', 'Mailer module is not available - unable to send emails!');
define('_FOR_NOSUCHCONTACT', 'Unknown Contact');

//
// O
//
define('_FOR_ONLINEAPPLYAS', 'Apply as' );
define('_FOR_ONLINEBIRTHDATE', 'Date of birth' );
define('_FOR_ONLINECOUNTRY', 'Country' );
define('_FOR_ONLINEDATE', 'Entry date' );
define('_FOR_ONLINEJOBAPPLY', 'Apply online!' );
define('_FOR_ONLINEPRIVACY', 'Thanks for applying, we will keep your data strictly confidential' );
define('_FOR_ONLINESALARY', 'Salary' );
define('_FOR_ONLINESTREET', 'Street' );
define('_FOR_ONLINEZIPCITY', 'Zip City' );
define('_FOR_OPTIONS','Options' );

//
// P
//
define('_FOR_PHONE', 'Phone Number' );
define('_FOR_PUBLIC', 'Public' );

//
// R
//
define('_FOR_RESUME','Resume');

//
// S
//
define('_FOR_SEND', 'Send' );
define('_FOR_SENDEREMAIL', 'Sender email');
define('_FOR_SENDERINFO', 'Use this information in the users confirmation mail');
define('_FOR_SENDERNAME', 'Sender name');
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
define('_FOR_SENDTOADMIN', 'This data was sent to us:');
define('_FOR_SENDTOUSER', 'Confirmation of your submission will be emailed to you in a few minutes.' );
define('_FOR_SENDUSER','Send confirmation email to user?' );
define('_FOR_SHOWCOMMENT', 'Show comments textarea' );
define('_FOR_SHOWCOMPANY','Show company?' );
define('_FOR_SHOWLOCATION','Show location?' );
define('_FOR_SHOWPHONE','Show phone number?' );
define('_FOR_SHOWURL','Show URL?' );
define('_FOR_SIMPLEMATHEQUATION', 'Please solve this simple math test');
define('_FOR_SUBMIT', 'Update configuration' );

//
// T
//
define('_FOR_TEAM', 'Team' );
define('_FOR_TEXTMAIL', 'Text' );
define('_FOR_THANKS', 'Thank you for your questions/comments to our website!<br>We will reply as soon as possible.' );
define('_FOR_THE', 'The' );
define('_FOR_THEME', 'Contact or Theme' );

//
// U
//
define('_FOR_UPDATECONTACTFAILED', 'Error updating contact!');
define('_FOR_UPLOADDIRNOTWRITABLE','The webserver cannot write into this folder!' );
define('_FOR_UPLOADERROR1', 'upload-error: file too big (php.ini)' );
define('_FOR_UPLOADERROR2', 'upload-error: file too big (form)' );
define('_FOR_UPLOADERROR3', 'upload-error: file received partially' );
define('_FOR_UPLOADERROR4', 'upload-error: no file received' );
define('_FOR_UPLOADFILEDIR', 'Folder for uploaded files' );
define('_FOR_UPLOADLIMIT', '(Upload, max. 2MB)');
define('_FOR_URL', 'Homepage' );
define('_FOR_USERMAIL1', 'Thank you for the comments posted from our Website. The sent data is:' );
define('_FOR_USERMAIL2', 'We will respond to your email as soon as possible.' );
define('_FOR_USERMAILFORMAT', 'Email Format' );

//
// V
//
define('_FOR_VIEWCONTACT','View contacts' );
define('_FOR_VISITHOMEPAGE', 'Visit Formicula on NOC');

//
// W
//
define('_FOR_WRONGCAPTCHA', 'Bad in mathematics? You can do better, try again.');
