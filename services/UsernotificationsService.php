<?php

namespace Craft;

class UsernotificationsService extends BaseApplicationComponent{


    /**
     * Get all notifications for user
     * if no user is set, it will fetch the current user
     * so you can see how it would look like for others without logging out
     *
     * @param null $userId
     * @return array|bool
     */
    public function getAllNotificationsForUser($userId = null){
        // check if user is logged in...
        if(!$user = craft()->userSession){
            return false;
        }

        if($userId === null){
            $userId = $user->id;
        }


        // fetch all removed notifications of the user to not fetch them in our query
        $removedNotifications = craft()->db->createCommand()
            ->select('entryId')
            ->from('usernotifications')
            ->where(['userId' => $userId])
            ->queryColumn();

        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->section = 'usernotifications';
        // user should not be overwhelmed with too many notification when they didn't log in for too long
        // so show only notifications 14 days ago, you can change this length or remove it if you want
        // but make sure to change the value in the task as well.
        // you could also create settings for the plugin where you can store how long
        // then entries should be displayed
        $someDateInThePast = date('U', strtotime("-14 days"));

        $criteria->postDate = "> " . $someDateInThePast;
        // exclude the ids of the removed notifications
        if($removedNotifications){
            $criteria->id = 'and, not ' . implode(', not ', $removedNotifications);
        }

        return $criteria->find();
    }
}
