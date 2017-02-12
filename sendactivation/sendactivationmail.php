<?php
/**
 * @copyright	Copyright (C) 2005 - 2017 Hepta technologies SL. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

class plgUserSendactivationmail extends JPlugin
{
    /**
     * before store user method
     *
     * Method is called before user data is stored in the database
     *
     * @param    array        $user    Holds the old user data.
     * @param    boolean        $isnew    True if a new user is stored.
     * @param    array        $new    Holds the new user data.
     *
     * @return    void
     * @since    1.6
     */
    public function onUserBeforeSave($user, $isnew, $new)
    {
        $config	= JFactory::getConfig();
        $groupsToCheck = $this->params->get('sam_usergroups', 0);

        if ($user['block']=="1" && $new['block']=="0" && $this->checkGroups($groupsToCheck, $user['groups']))
        {
    		// Load user_joomla plugin language (not done automatically).
    		$lang = JFactory::getLanguage();
    		$lang->load('plg_user_sendactivationmail', JPATH_ADMINISTRATOR);

    		// Compute the mail subject.
    		$emailSubject = JText::sprintf(
    				'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_SUBJECT',
    				$user['name'],
    				$config->get('sitename'),
                    $user['username']
    		);

    		// Compute the mail body.
    		$emailBody = JText::sprintf(
    				'COM_USERS_EMAIL_ACTIVATED_BY_ADMIN_ACTIVATION_BODY',
    				$user['name'],
    				$config->get('sitename'),
    				JUri::root()
    		);

    		// Assemble the email data...the sexy way!
    		$mail = JFactory::getMailer()
    				->setSender(
    						array(
    								$config->get('mailfrom'),
    								$config->get('fromname')
    						)
    				)
    				->addRecipient($user['email'])
    				->setSubject($emailSubject)
    				->setBody($emailBody)
    				->isHtml(true);

    		if (!$mail->Send()) {
    				JFactory::getApplication()->enqueueMessage(JText::_('PLG_USER_SENDACTIVATIONMAIL_MAIL_ERROR'), 'error');
    		}
    	}

    }

    function checkGroups($groupsToCheck, $userGroups)
    {
    	if(!$groupsToCheck)
    	{
    		return true;
    	}

    	$intersect = array_intersect($groupsToCheck,$userGroups);

    	if(empty($intersect))
    	{
    		return false;
    	}

    	return true;
    }
}
