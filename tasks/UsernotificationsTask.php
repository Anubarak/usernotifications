<?php
/**
 * Usernotifications plugin for Craft CMS
 *
 * Usernotifications Task
 *
 * --snip--
 * Tasks let you run background processing for things that take a long time, dividing them up into steps.  For
 * example, Asset Transforms are regenerated using Tasks.
 *
 * Keep in mind that tasks only get timeslices to run when Craft is handling requests on your website.  If you
 * need a task to be run on a regular basis, write a Controller that triggers it, and set up a cron job to
 * trigger the controller.
 *
 * https://craftcms.com/classreference/services/TasksService
 * --snip--
 *
 * @author    Robin Schambach
 * @copyright Copyright (c) 2017 Robin Schambach
 * @link      www.secondre.de
 * @package   Usernotifications
 * @since     1
 */

namespace Craft;

class UsernotificationsTask extends BaseTask
{

    /**
     * Returns the default description for this task.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Usernotifications Tasks - remove old records';
    }

    /**
     * Gets the total number of steps for this task.
     *
     * @return int
     */
    public function getTotalSteps()
    {
        return 1;
    }

    /**
     * Runs a task step.
     *
     * @param int $step
     * @return bool
     */
    public function runStep($step)
    {
        // remove all records older than 2 weeks
        $someDateInThePast = date('U', strtotime("-14 days"));
        /** @var UsernotificationsRecord[] $records */
        $records = UsernotificationsRecord::model()->findAll([
            'condition' => 'dateCreated >= :dateCreated',
            'limit' => 50,
            'params' => array(':dateCreated' => $someDateInThePast)
        ]);
        if($records){
            foreach($records as $record){
                $record->delete();
            }
        }
        return true;
    }
}
