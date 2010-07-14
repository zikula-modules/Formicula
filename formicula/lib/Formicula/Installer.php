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

class Formicula_Installer extends Zikula_Installer
{
    public function install()
    {
        $dom = ZLanguage::getModuleDomain('Formicula');
        $tempdir = System::getVar('temp');
        if(StringUtil::left($tempdir, 1) <> '/') {
            // tempdir does not start with a / which means it does not reside outside
            // the webroot, continue
            if(StringUtil::right($tempdir, 1) <> '/') {
                $tempdir .= '/';
            }
            if(FileUtil::mkdirs($tempdir . 'formicula_cache')) {
                $res1 = FileUtil::writeFile($tempdir . 'formicula_cache/index.html');
                $res2 = FileUtil::writeFile($tempdir . 'formicula_cache/.htaccess', 'SetEnvIf Request_URI "\.gif$" object_is_gif=gif
SetEnvIf Request_URI "\.png$" object_is_png=png
SetEnvIf Request_URI "\.jpg$" object_is_jpg=jpg
Order deny,allow
Deny from all
Allow from env=object_is_gif
Allow from env=object_is_png
Allow from env=object_is_jpg
');
                if($res1===false || $res2===false){
                    LogUtil::registerStatus($this->__('The installer could not create formicula_cache/index.html and/or formicula_cache/.htaccess, please refer to the manual before using the module!'));
                }
            } else {
                LogUtil::registerStatus($this->__('The installer could not create the formicula_cache folder, please refer to the manual before using the module!'));
            }
        } else {
            // tempdir starts with /, so it is an absolute path, probably outside the webroot
            LogUtil::registerStatus($this->__('pnTemp folder found outside of the webroot, please consult the manual of how to create the formicula_cache folder in this case.'));
        }

        // create the formicula table
        if (!DBUtil::createTable('formcontacts')) {
            return LogUtil::registerError($this->__('The installer could not create the formcontacts table'));
        }

        ModUtil::apiFunc('Formicula',
                'admin',
                'createContact',
                array('name'     => 'Webmaster',
                'email'    => System::getVar('adminmail'),
                'public'   => 1,
                'sname'    => 'Webmaster',
                'semail'   => System::getVar('adminmail'),
                'ssubject' => _FOR_EMAILFROM . ' %s'));

        $this->setVar('show_phone', 1);
        $this->setVar('show_company', 1);
        $this->setVar('show_url', 1);
        $this->setVar('show_location', 1);
        $this->setVar('show_comment', 1);
        $this->setVar('send_user', 1);
        $this->setVar('spamcheck', 1);

        $this->setVar('upload_dir', 'pnTemp');
        $this->setVar('delete_file', 1);

        $this->setVar('default_form', 0);

        // Initialisation successful
        return true;
    }


    public function upgrade($oldversion)
    {
        // Get database information
        ModUtil::dbInfoLoad('Formicula');

        // perform a global db change for all versions >= 0.4
        if(version_compare($oldversion, '0.5', '>')) {
            if (!DBUtil::changeTable('formcontacts')) {
                return LogUtil::registerError($this->__('Database upgrade failed'));
            }
        }

        // Upgrade dependent on old version number
        switch($oldversion) {
            case '0.1':
                $this->setVar('upload_dir', 'pnTemp');
                $this->setVar('delete_file', 1);
            case '0.2':
            // nothing to do
            case '0.3':
            // nothing to do
            case '0.4':
            // create the formicula table
                if (!DBUtil::createTable('formcontacts')) {
                    LogUtil::registerError($this->__('The installer could not create the formcontacts table'));
                    return '0.4';
                }

                // migrate contacts from config var to table
                $contacts = $this->getVar('contacts');
                if( @unserialize( $contacts ) != "" ) {
                    $contacts_array = unserialize( $contacts );
                } else {
                    $contacts_array = array();
                }
                foreach ($contacts_array as $contact) {
                    $name  = DataUtil::formatForStore($contact['name']);
                    $email = DataUtil::formatForStore($contact['email']);
                    ModUtil::apiFunc('formicula',
                            'admin',
                            'createContact',
                            array('name'     => $name,
                            'email'    => $email,
                            'public'   => 1,
                            'sname'    => '',
                            'semail'   => '',
                            'ssubject' => ''));
                }
                $this->delVar('contacts');
                $this->delVar('version');
            case '0.5':
            // nothing to do
            case '0.6':
            // the db change has been already
                $this->setVar('spamcheck', 1);
                $this->setVar('excludespamcheck', '');
            case '1.0':
            // nothing to do
            case '1.1':

                $tempdir = System::getVar('temp');
                if(StringUtil::right($tempdir, 1) <> '/') {
                    $tempdir .= '/';
                }
                if(!is_dir($tempdir . 'formicula_cache')) {
                    if(FileUtil::mkdirs($tempdir . 'formicula_cache')) {
                        $res1 = FileUtil::writeFile($tempdir . 'formicula_cache/index.html');
                        $res2 = FileUtil::writeFile($tempdir . 'formicula_cache/.htaccess', 'SetEnvIf Request_URI "\.gif$" object_is_gif=gif
SetEnvIf Request_URI "\.png$" object_is_png=png
SetEnvIf Request_URI "\.jpg$" object_is_jpg=jpg
Order deny,allow
Deny from all
Allow from env=object_is_gif
Allow from env=object_is_png
Allow from env=object_is_jpg
');
                        if($res1===false || $res2===false){
                            LogUtil::registerStatus($this->__('The installer could not create formicula_cache/index.html and/or formicula_cache/.htaccess, please refer to the manual before using the module!'));
                        }
                    } else {
                        LogUtil::registerStatus($this->__('The installer could not create the formicula_cache folder, please refer to the manual before using the module!'));
                    }
                }
            case '2.0':
            // set the default form
                $this->setVar('default_form', 0);
            case '2.2.1':
                // future upgrades

        }

        // clear compiled templates
        Zikula_View::getInstance('Formicula')->clear_compiled();

        // Update successful
        return true;
    }


    public function uninstall()
    {
        // drop the table
        if (!DBUtil::dropTable('formcontacts')) {
            return LogUtil::registerError(_FOR_DROPTABLEFAILED);
        }

        $tempdir = System::getVar('temp');
        if(StringUtil::right($tempdir, 1) <> '/') {
            $tempdir .= '/';
        }
        if(is_dir($tempdir . 'formicula_cache')) {
            FileUtil::deldir($tempdir . 'formicula_cache');
        }

        // Remove module variables
        $this->delVars();

        return true;
    }
}