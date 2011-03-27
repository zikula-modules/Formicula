<?php
/**
 * Formicula - the contact mailer for Zikula
 * -----------------------------------------
 *
 * @copyright  (c) Formicula Development Team
 * @link       http://code.zikula.org/formicula
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Frank Schummertz <frank@zikula.org>
 * @package    Third_Party_Components
 * @subpackage formicula
 */

class Formicula_Controller_User extends Zikula_AbstractController
{
    public function postInitialize()
    {
        $this->view->setCaching(false)->add_core_data();
    }

    /**
     * main
     * main entry point for the user
     *
     *@param form int number of form to show
     *@returns view output
     */
    public function main($args=array())
    {
        $default_form = $this->getVar('default_form', 0);
        $form = (int)FormUtil::getPassedValue('form', (isset($args['form'])) ? $args['form'] : $default_form, 'GETPOST');
        $cid  = (int)FormUtil::getPassedValue('cid',  (isset($args['cid'])) ? $args['cid'] : -1,  'GETPOST');

        // get submitted information - will be passed to the template
        // addinfo is an array:
        // addinfo[name1] = value1
        // addinfo[name2] = value2
        $addinfo  = FormUtil::getPassedValue('addinfo',  (isset($args['addinfo'])) ? $args['addinfo'] : array(),  'GETPOST');

        // reset captcha
        SessionUtil::delVar('formicula_captcha');

        if ($cid == -1) {
            $contacts = ModUtil::apiFunc('Formicula', 'User', 'readValidContacts',
                                         array('form' => $form));
        } else {
            $contacts[] = ModUtil::apiFunc('Formicula', 'User', 'getContact',
                                           array('cid'  => $cid,
                                                 'form' => $form));
        }

        if (count($contacts) == 0) {
            return LogUtil::registerPermissionError(System::getHomepageUrl());
        }

        if (UserUtil::isLoggedIn()) {
            $uname = (UserUtil::getVar('name') == '') ? UserUtil::getVar('uname') : UserUtil::getVar('name');
            $uemail = UserUtil::getVar('email');
        } else {
            $uname = '';
            $uemail = '';
        }

        $spamcheck = $this->getVar('spamcheck');
        if($spamcheck == 1) {
            $excludespamcheck = explode(',', $this->getVar('excludespamcheck'));
            if(is_array($excludespamcheck) && array_key_exists($form, array_flip($excludespamcheck))) {
                $spamcheck = 0;
            }
        }

        $this->view->add_core_data()->setCaching(false);
        $this->view->assign('uname', $uname);
        $this->view->assign('uemail', $uemail);
        $this->view->assign('contacts', $contacts);
        $this->view->assign('addinfo', $addinfo);
        $this->view->assign('spamcheck', $spamcheck);
        return $this->view->fetch('forms' . DIRECTORY_SEPARATOR . $form.'_userform.html');
    }

    /**
     * send
     * sends the mail to the contact and, if configured, to the user
     *@param cid         int contact id
     *@param form        int form id
     *@param userformat  string email format for user, either 'plain' (default) or 'html'
     *@param adminformat string email format for admin, either 'plain' (default) or 'html'
     *@param uname       string users name
     *@param uemail      string users email
     *@param url         string users homepage
     *@param phone       string users phone
     *@param company     string users company
     *@param location    string users location
     *@param comment     string users comment
     *@returns view output
     */
    public function send($args=array())
    {
        $form           = (int)FormUtil::getPassedValue('form',        (isset($args['form'])) ? $args['form'] : 0, 'GETPOST');
        $cid            = (int)FormUtil::getPassedValue('cid',         (isset($args['cid'])) ? $args['cid'] : 0,  'GETPOST');
        $captcha        = (int)FormUtil::getPassedValue('captcha',     (isset($args['captcha'])) ? $args['captcha'] : 0, 'GETPOST');
        $userformat     =      FormUtil::getPassedValue('userformat',  (isset($args['userformat'])) ? $args['userformat'] : 'plain',  'GETPOST');
        $adminformat    =      FormUtil::getPassedValue('adminformat', (isset($args['adminformat'])) ? $args['adminformat'] : 'plain', 'GETPOST');
        //$numfields      = (int)FormUtil::getPassedValue('numFields',   (isset($args['numFields'])) ? $args['numFields'] : 0,  'GETPOST');
        $returntourl    =      FormUtil::getPassedValue('returntourl', (isset($args['returntourl'])) ? $args['returntourl'] : '',  'GETPOST');
        $ud['uname']    =      FormUtil::getPassedValue('uname',       (isset($args['uname'])) ? $args['uname'] : '', 'GETPOST');
        $ud['uemail']   =      FormUtil::getPassedValue('uemail',      (isset($args['uemail'])) ? $args['uemail'] : '',  'GETPOST');
        $ud['url']      =      FormUtil::getPassedValue('url',         (isset($args['url'])) ? $args['url'] : '', 'GETPOST');
        $ud['phone']    =      FormUtil::getPassedValue('phone',       (isset($args['phone'])) ? $args['phone'] : '',  'GETPOST');
        $ud['company']  =      FormUtil::getPassedValue('company',     (isset($args['company'])) ? $args['company'] : '', 'GETPOST');
        $ud['location'] =      FormUtil::getPassedValue('location',    (isset($args['location'])) ? $args['location'] : '',  'GETPOST');
        $ud['comment']  =      FormUtil::getPassedValue('comment',     (isset($args['comment'])) ? $args['comment'] : '', 'GETPOST');

        if(empty($cid) && empty($form)) {
            return System::redirect(System::getHomepageUrl());
        }

        // remove tags from comment to avoid spam
        $ud['comment'] = strip_tags($ud['comment']);

        // check captcha
        $spamcheck = $this->getVar('spamcheck');
        if($spamcheck == 1) {
            $excludespamcheck = explode(',', $this->getVar('excludespamcheck'));
            if(is_array($excludespamcheck) && array_key_exists($form, array_flip($excludespamcheck))) {
                $spamcheck = 0;
            }
        }
        if($spamcheck==1) {
            $captcha_ok = false;
            $cdata = @unserialize(SessionUtil::getVar('formicula_captcha'));
            if(is_array($cdata)) {
                switch($cdata['z']) {
                    case '0':
                        $captcha_ok = (((int)$cdata['x'] + (int)$cdata['y'])== $captcha);
                        break;
                    case '1':
                        $captcha_ok = (((int)$cdata['x'] - (int)$cdata['y'])== $captcha);
                        break;
                    default:
                    // $captcha_ok is false
                }
            }

            if($captcha_ok==false) {
                SessionUtil::delVar('formicula_captcha');
                // todo: append params to $returntourl and redirect
                $params = array('form' => $form);
                if(is_array($addinfo) && count($addinfo)>0) {
                    $params['addinfo'] = $addinfo;
                }
                return LogUtil::registerError($this->__('Bad in mathematics? You can do better, try again.'), null, ModUtil::url('Formicula', 'user', 'main', $params));
            }
        }
        SessionUtil::delVar('formicula_captcha');

        if(!SecurityUtil::confirmAuthKey()) {
            $params = array('form' => $form);
            if(is_array($addinfo) && count($addinfo)>0) {
                $params['addinfo'] = $addinfo;
            }
            return LogUtil::registerAuthidError(ModUtil::url('Formicula', 'user', 'main', $params));
        }

        if(empty($userformat) || ($userformat<>'plain' && $userformat<>'html' && $userformat<>'none')) {
            $userformat = 'plain';
        }
        if(empty($adminformat) || ($adminformat<>'plain' && $adminformat<>'html')) {
            $adminformat = 'plain';
        }

        if(!SecurityUtil::checkPermission('Formicula::', "$form:$cid:", ACCESS_COMMENT)) {
            return LogUtil::registerPermissionError(ModUtil::url('Formicula', 'user', 'main', array('form' => $form)));
        }

        // very basic input validation against HTTP response splitting
        $ud['uemail'] = str_replace(array('\r', '\n', '%0d', '%0a'), '', $ud['uemail']);

        // addon: custom fields
        $uploaddir = $this->getVar('upload_dir');
        // check if it ends with / or we add one
        if(substr($uploaddir, strlen($uploaddir)-1, 1) <> "/") {
            $uploaddir .= "/";
        }
        $custom = array();
        // we read custom fields until we find three missing indices in a row
        $i = 0;
        $missing = 0;
        do {
            $custom[$i]['name'] = FormUtil::getPassedValue('custom'.$i.'name', null, 'POST');
            if($custom[$i]['name'] == null) {
                // increase the number of missing indices and clear this custom var
                $missing++;
                unset($custom[$i]);
            } else {
                $custom[$i]['mandatory'] = (FormUtil::getPassedValue('custom'.$i.'mandatory') == 1) ? true : false;

                // get uploaded files
                if(isset($_FILES['custom'.$i.'data']['tmp_name'])) {
                    $custom[$i]['data']['error'] = $_FILES['custom'.$i.'data']['error'];
                    if($custom[$i]['data']['error'] == 0) {
                        $custom[$i]['data']['size']     = $_FILES['custom'.$i.'data']['size'];
                        $custom[$i]['data']['type']     = $_FILES['custom'.$i.'data']['type'];
                        $custom[$i]['data']['name']     = $_FILES['custom'.$i.'data']['name'];
                        $custom[$i]['upload'] = true;
                        move_uploaded_file($_FILES['custom'.$i.'data']['tmp_name'], DataUtil::formatForOS($uploaddir.$custom[$i]['data']['name']));
                    } else {
                        // error - replace the 'data' with an errormessage
                        $custom[$i]['data'] = constant("_FOR_UPLOADERROR".$custom[$i]['data']['error']);
                    }
                } else {
                    $custom[$i]['data'] = FormUtil::getPassedValue('custom'.$i.'data');
                    $custom[$i]['upload'] = false;
                }
                // reset the errorcounter if an existing field is found
                $missing = 0;
                // increase the counter
                // $i++;
            }
            // increase the counter
            $i++;
        } while ($missing < 3);

        $contact = ModUtil::apiFunc('Formicula', 'User', 'getContact',
                                    array('cid'  => $cid,
                                          'form' => $form));

        $this->view->setCaching(false);
        $this->view->assign('contact', $contact);
        $this->view->assign('userdata', $ud);

        if(ModUtil::apiFunc('Formicula',
                'user',
                'checkArguments',
                array('userdata'   => $ud,
                'custom'     => $custom,
                'userformat' => $userformat)) == true) {
            if(ModUtil::apiFunc('Formicula', 'User', 'sendtoContact',
                                array('contact'  => $contact,
                                      'userdata' => $ud,
                                      'custom'   => $custom,
                                      'form'     => $form,
                                      'format'   => $adminformat)) == false) {
                return LogUtil::registerError($this->__('There was an error sending the email.'), null, ModUtil::url('Formicula', 'user', 'main', array('form' => $form)));
            }

            if(($this->getVar('send_user') == 1) && ($userformat <> 'none')) {
                // we replace the array of data of uploaded files with the filename
                $this->view->assign('sendtouser', ModUtil::apiFunc('Formicula', 'User', 'sendtoUser',
                                    array('contact'  => $contact,
                                          'userdata' => $ud,
                                          'custom'   => $custom,
                                          'form'     => $form,
                                          'format'   => $userformat )));
            }

            $this->view->assign('custom', ModUtil::apiFunc('Formicula', 'User', 'removeUploadInformation', array('custom' => $custom)));
            return $this->view->fetch('forms' . DIRECTORY_SEPARATOR . $form."_userconfirm.html");
        } else {
            $this->view->assign('custom', ModUtil::apiFunc('Formicula', 'User', 'removeUploadInformation', array('custom' => $custom)));
            return $this->view->fetch('forms' . DIRECTORY_SEPARATOR . $form."_usererror.html");
        }
    }

    /**
     * getimage
     * returns an image for the captcha even if pnTemp is located outside of the webroot
     *@param img  string the image filename
     *@returns image output
     */
    public function getimage()
    {
        $img = FormUtil::getPassedValue('img', '', 'GET');

        $temp = System::getVar('temp');
        if(StringUtil::right($temp, 1) <> '/') {
            $temp .= '/';
        }
        $imgfile = $temp . 'formicula_cache/' . DataUtil::formatForStore($img);
        $parts = explode('.', $img);
        $data = file_get_contents($imgfile);

        $mimetypes = array('png' => 'image/png',
                           'jpg' => 'image/jpeg',
                           'gif' => 'image/gif');

        // following code is based on Axels MediaAttach/pnuser/download.php
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: formicula image");
        header("Content-Disposition: inline; filename=" . DataUtil::formatForDisplay($img) . ";");
        header("Content-type: image/" . $mimetypes[$parts[1]]);
        header("Content-Transfer-Encoding: binary");

        echo $data;
        exit;
    }
}