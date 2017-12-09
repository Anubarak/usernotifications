<?php
/**
 * Usernotifications plugin for Craft CMS
 *
 * Usernotifications Variable
 *
 * --snip--
 * Craft allows plugins to provide their own template variables, accessible from the {{ craft }} global variable
 * (e.g. {{ craft.pluginName }}).
 *
 * https://craftcms.com/docs/plugins/variables
 * --snip--
 *
 * @author    Robin Schambach
 * @copyright Copyright (c) 2017 Robin Schambach
 * @link      www.secondre.de
 * @package   Usernotifications
 * @since     1
 */

namespace Craft;

class UsernotificationsVariable{

    /**
     * Whatever you want to output to a Twig template can go into a Variable method. You can have as many variable
     * functions as you want.  From any Twig template, call it like this:
     *
     *     {{ craft.usernotifications.exampleVariable }}
     *
     * Or, if your variable requires input from Twig:
     *
     *     {{ craft.usernotifications.exampleVariable(twigValue) }}
     */
    public function getAllNotificationsForUser(){
        $html = '';
        $entries = craft()->usernotifications->getAllNotificationsForUser();
        if($entries){
            // I get a deprecated hint here, but the solution stated in craft throws
            // an exception... So I'm using this one
            $oldPath = craft()->path->getTemplatesPath();
            $newPath = craft()->path->getPluginsPath() . 'usernotifications/templates';
            craft()->path->setTemplatesPath($newPath);
            $html = craft()->templates->render('entries/index.twig', array(
                'entries' => $entries
            ));
            craft()->path->setTemplatesPath($oldPath);
        }
        return $html;
    }
}