<?php

namespace Craft;

class UsernotificationsService extends BaseApplicationComponent{


    /**
     * Get all notifications for the current user
     *
     * @return string
     */
    public function getAllNotificationsForUser(){
        // check if user is logged in...
        if(!craft()->userSession->getUser()){
            return "";
        }

        // fetch all removed notifications of the user to not fetch them in our query
        $removedNotifications = craft()->db->createCommand()
            ->select('entryId')
            ->from('usernotifications')
            ->where(['userId' => craft()->userSession->getUser()->id])
            ->queryColumn();

        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->section = 'usernotifications';
        // user should not be overwhelmed with notification when they don't log in
        // for too many days, so show only notifications 14 days ago
        $someDateInThePast = date('U', strtotime("-14 days"));

        $criteria->postDate = "> " . $someDateInThePast;
        // exclude the ids of the removed notifications
        if($removedNotifications){
            $criteria->id = 'and, not ' . implode(', not ', $removedNotifications);
        }

        $html = '';
        // I get an deprecated hint here, but the solution stated above the method throws
        // an exception... So I'm using this one.
        $oldPath = craft()->path->getTemplatesPath();
        $newPath = craft()->path->getPluginsPath() . 'usernotifications/templates';
        craft()->path->setTemplatesPath($newPath);
        if($entries = $criteria->find()){
            $html = craft()->templates->render('entries/index.twig', array(
                'entries' => $entries
            ));
        }
        craft()->path->setTemplatesPath($oldPath);

        return $html;
    }
}
