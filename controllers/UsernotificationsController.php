<?php
/**
 * Usernotifications plugin for Craft CMS
 *
 * Usernotifications Controller
 *
 * --snip--
 * Generally speaking, controllers are the middlemen between the front end of the CP/website and your plugin’s
 * services. They contain action methods which handle individual tasks.
 *
 * A common pattern used throughout Craft involves a controller action gathering post data, saving it on a model,
 * passing the model off to a service, and then responding to the request appropriately depending on the service
 * method’s response.
 *
 * Action methods begin with the prefix “action”, followed by a description of what the method does (for example,
 * actionSaveIngredient()).
 *
 * https://craftcms.com/docs/plugins/controllers
 * --snip--
 *
 * @author    Robin Schambach
 * @copyright Copyright (c) 2017 Robin Schambach
 * @link      www.secondre.de
 * @package   Usernotifications
 * @since     1
 */

namespace Craft;

class UsernotificationsController extends BaseController
{

    /**
     * Remove a notification for a user
     */
    public function actionRemoveNotification(){
        $this->requirePostRequest();
        $this->requireAjaxRequest();

        $response = [
            'success'   => false,
            'message'   => 'could not find entry'
        ];
        if($entryId = craft()->request->getParam('id')){
            $userId = craft()->userSession->getUser()->id;

            $record = UsernotificationsRecord::model()->find('entryId = :entryId AND userId = :userId', array(
                'entryId'   => $entryId,
                'userId'    => $userId
            ));
            if($record){
                // user already removed the message... there must be something wrong or he/ she cheated :P
                // you can actually ignore this... just wanted to show you how to search for entries^^
            }else{
                $record  = new UsernotificationsRecord();
            }

            $record->userId = $userId;
            $record->entryId = $entryId;

            if($record->save()){
                $response['success'] = true;
                $response['message'] = 'notification removed for user';
             }else{
                $response['message'] = 'could not save record';
                $response['errors'] = $record->getErrors();
            }
        }

        $this->returnJson($response);
    }

    public function actionRemoveOldRecords(){
        craft()->tasks->createTask('Usernotifications');
        echo 'Task created!';
        craft()->end();
    }
}