<?php
/**
* @version $Id$
* Kunena Component
* @package Kunena
*
* @Copyright (C) 2008 - 2010 Kunena Team All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.kunena.com
*
* Based on FireBoard Component
* @Copyright (C) 2006 - 2007 Best Of Joomla All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.bestofjoomla.com
*
* Based on Joomlaboard Component
* @copyright (C) 2000 - 2004 TSMF / Jan de Graaff / All Rights Reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @author TSMF & Jan de Graaff
**/

// Dont allow direct linking
defined( '_JEXEC' ) or die();


$id 		= JRequest::getInt('id', 0);
if (!$id) $id = JRequest::getInt('msg_id');
$catid 		= JRequest::getInt('catid', 0);
$reporter 	= JRequest::getInt('reporter', 0);
$reason 	= strval(JRequest::getVar('reason'));
$text 		= strval(JRequest::getVar('text'));
$do 		= JRequest::getCmd('do', '');

switch ($do)
{
    case 'report':
        ReportMessage($id, $catid, $reporter, $reason, $text);

        break;

    default:
        ReportForm($id, $catid);

        break;
}

function ReportMessage($id, $catid, $reporter, $reason, $text, $type=0)
{
    $kunena_my = &JFactory::getUser();
    $kunena_db = &JFactory::getDBO();
    $kunena_config =& CKunenaConfig::getInstance();

    if (!$kunena_my->id) {
        JError::raiseError( 403, JText::_("ALERTNOTAUTH") );;
        return;
        }

	if (!empty($reason) && !empty($text))
	{

    $kunena_db->setQuery("SELECT a.*, b.mesid, b.message AS msg_text FROM #__fb_messages AS a"
    . " LEFT JOIN #__fb_messages_text AS b ON b.mesid = a.id"
    . " WHERE a.id='{$id}'");

    $row = $kunena_db->loadObject();

    $kunena_db->setQuery("SELECT username FROM #__users WHERE id={$row->userid}");
    $baduser = $kunena_db->loadResult();

    $kunena_db->setQuery("SELECT username FROM #__users WHERE id={$reporter}");
    $sender = $kunena_db->loadResult();

    if ($reason) {
        $subject = "[".stripslashes($kunena_config->board_title)." "._GEN_FORUM."] "._KUNENA_REPORT_MSG . ": " . $reason;
        }
    else {
        $subject = "[".stripslashes($kunena_config->board_title)." "._GEN_FORUM."] "._KUNENA_REPORT_MSG . ": " . stripslashes($row->subject);
        }

	jimport('joomla.environment.uri');
	$uri =& JURI::getInstance(JURI::base());
	$msglink = $uri->toString(array('scheme', 'host', 'port')) . str_replace('&amp;', '&', CKunenaLink::GetThreadPageURL($kunena_config, 'view', $row->catid , $row->id, NULL,NULL,$row->id));

    $message  = "" . _KUNENA_REPORT_RSENDER . " " . $sender;
    $message .= "\n";
    $message .= "" . _KUNENA_REPORT_RREASON . " " . $reason;
    $message .= "\n";
    $message .= "" . _KUNENA_REPORT_RMESSAGE . " " . $text;
    $message .= "\n\n";
    $message .= "" . _KUNENA_REPORT_POST_POSTER . " " . $baduser;
    $message .= "\n";
    $message .= "" . _KUNENA_REPORT_POST_SUBJECT . " " . stripslashes($row->subject);
    $message .= "\n";
    $message .= "" . _KUNENA_REPORT_POST_MESSAGE . "\n-----\n" . stripslashes($row->msg_text);
    $message .= "\n-----\n\n";
    $message .= "" . _KUNENA_REPORT_POST_LINK . " " . $msglink;
    $message .= "\n\n\n\n** Powered by Kunena! - http://www.Kunena.com **";
    $message = strtr($message, array('&#32;'=>''));

    //get category moderators
    $kunena_db->setQuery("SELECT userid FROM #__fb_moderation WHERE catid={$row->catid}");
    $mods = $kunena_db->loadObjectList();
    	check_dberror("Unable to load moderators.");

    //get admins
    $kunena_db->setQuery("SELECT id FROM #__users WHERE gid IN (24, 25)");
    $admins = $kunena_db->loadObjectList();
    	check_dberror("Unable to load admin.");

    switch ($type)
    {
        default:
        case '0':
            SendReporttoMail($sender, $subject, $message, $msglink, $mods, $admins);

            break;
    }

    echo '<div align="center">' . _KUNENA_REPORT_SUCCESS;
    echo CKunenaLink::GetAutoredirectThreadPageHTML($kunena_config,'view',$catid,$id,NULL,NULL,$id,3500);

	}
    else
    {
    	echo '<div align="center">';
    	if (empty($reason)) echo _POST_FORGOT_SUBJECT;
    	else if (empty($text)) echo _POST_FORGOT_MESSAGE;

    }
    echo '<br /><br />';
    echo CKunenaLink::GetThreadPageLink($kunena_config,'view', $catid, $id ,NULL,NULL, _POST_SUCCESS_VIEW ,$id,'nofollow' ).'<br />';
    echo CKunenaLink::GetCategoryLink('showcat',$catid , _POST_SUCCESS_FORUM , 'nofollow').'<br />';
    echo '</div>';
}

function SendReporttoMail($sender, $subject, $message, $msglink, $mods, $admins) {
    $kunena_config =& CKunenaConfig::getInstance();
    $kunena_db =& JFactory::getDBO();

    //send report to category moderators
    if (count($mods)>0) {
        foreach ($mods as $mod) {
            $kunena_db->setQuery("SELECT email FROM #__users WHERE id={$mod->userid}");
            $email = $kunena_db->loadResult();

            JUtility::sendMail($kunena_config->email, $kunena_config->board_title, $email, $subject, $message);
            }
    }

    //send report to site admins
    foreach ($admins as $admin) {
        $kunena_db->setQuery("SELECT email FROM #__users WHERE id={$admin->id}");
        $email = $kunena_db->loadResult();
        JUtility::sendMail($kunena_config->email, stripslashes($kunena_config->board_title)." ".JString::trim(_GEN_FORUM), $email, $subject, $message);
        }
    }

function ReportForm($id, $catid) {
    $kunena_app =& JFactory::getApplication();
    $kunena_config =& CKunenaConfig::getInstance();
    $kunena_my = &JFactory::getUser();

    $redirect = CKunenaLink::GetThreadPageURL($kunena_config,'view',$catid, $id,NULL,NULL,$id,true);

    if (!$kunena_my->id) {
        $kunena_app->redirect($redirect);
        return;
        }

    if ($kunena_config->reportmsg == 0) {
        $kunena_app->redirect($redirect);
        return;
        }
?>

<div class = "k_bt_cvr1">
    <div class = "k_bt_cvr2">
        <div class = "k_bt_cvr3">
            <div class = "k_bt_cvr4">
                <div class = "k_bt_cvr5">
                    <table class = "kblocktable" id = "kforumhelp" border = "0" cellspacing = "0" cellpadding = "0" width = "100%">
                        <thead>
                            <tr>
                                <th>
                                    <div class = "ktitle_cover">
                                        <span class = "ktitle"><?php echo _KUNENA_COM_A_REPORT ?></span>
                                    </div>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td class = "khelpdesc">
                                    <form method = "post" action = "<?php echo CKunenaLink::GetReportURL(); ?>">
                                        <table width = "100%" border = "0">
                                            <tr>
                                                <td width = "10%">
<?php echo _KUNENA_REPORT_REASON ?>:
                                                </td>

                                                <td>
                                                    <input type = "text" name = "reason" class = "inputbox" size = "30"/>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan = "2">
<?php echo _KUNENA_REPORT_MESSAGE ?>:
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan = "2">
                                                    <textarea id = "text" name = "text" cols = "40" rows = "10" class = "inputbox"></textarea>
                                                </td>
                                            </tr>
                                        </table>

                                        <input type = "hidden" name = "do" value = "report"/>
                                        <input type = "hidden" name = "id" value = "<?php echo $id;?>"/>
                                        <input type = "hidden" name = "catid" value = "<?php echo $catid;?>"/>
                                        <input type = "hidden" name = "reporter" value = "<?php echo $kunena_my->id;?>"/>
                                        <input type = "submit" name = "Submit" value = "<?php echo _KUNENA_REPORT_SEND ?>"/>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    }
