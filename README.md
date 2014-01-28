Formicula Zikula Module
=======================

A template-driven form mailer for Zikula. 
Formicula is a highly configurable contact mailer solution for Zikula Application Framework. 
You can create any kind of contact form with any number contact fields by changing Render templates.
Forms can be controlled by permissions. It also supports multiple, permission controlled recipients, configurable notification emails, a simple captcha for spam protection, hooks to the Captcha module, javascript form validation and file uploads.

3.x versions
------------
The 3.x version in the master branch needs Zikula 1.3.x to run with. 

3.0.2 is the version that contains the same functionality as 3.1.0, but is Not converted to HTML5 validation and jQuery. See https://github.com/zikula-ev/Formicula/issues/54 about HTML5 required and CkEditor < 4.2 - Scribite.

3.1.0 is a version that brings HTML5 form validation (with webshims lib polyfill fallback for
browsers not supporting this) and all jQuery javascript. 3.0.2 is the latest version with prototype
and prototype validation.

* More information on HTML5 form validation also in the Zikula Core ticket: https://github.com/zikula/core/issues/1181
* HTML5 constraint validation: http://www.html5rocks.com/en/tutorials/forms/constraintvalidation/
* Webshims polyfill lib for html5 functionality in browsers not supporting this: http://afarkas.github.io/webshim/demos/

2.x versions
------------
The 2.2 branch contains the latest version for Zikula 1.2.x

