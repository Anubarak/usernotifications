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
     *     {{ craft.pFplugin.getEntriesForImage }}
     *
     * Or, if your variable requires input from Twig:
     *
     *     {{ craft.pFplugin.getEntriesForImage($assetId, $fieldId) }}
     */
    public function getAllEntriesForAsset($assetId)
    {
        // list the ids of the sections, so you need the id of news, information and events
        $sectionIds = [2, 7];
        // id of your insertedImage field
        $fieldIdMatrixContent = 6;
        // id of your imageGallery field
        $fieldIdImageGallery = 7;

        $entryIdsFromMatrix = craft()->db->createCommand()
            ->select('craft_matrixblocks.ownerId')
            ->from('relations')
            ->join('matrixblocks', 'craft_matrixblocks.id = craft_relations.sourceId')
            ->where('craft_relations.targetId = :assetId', ['assetId' => $assetId])
            ->join('entries', 'craft_entries.id = craft_matrixblocks.ownerId')
            ->andWhere('craft_relations.fieldId = :fieldId', ['fieldId' => $fieldIdMatrixContent])
            ->andWhere(['in', 'craft_entries.sectionId', $sectionIds])
            ->queryColumn();

        $entryIdsImageGallery = craft()->db->createCommand()
            ->select('craft_relations.sourceId')
            ->from('relations')
            ->where('craft_relations.targetId = :assetId', ['assetId' => $assetId])
            ->join('entries', 'craft_entries.id = craft_relations.sourceId')
            ->andWhere(['in', 'craft_entries.sectionId', $sectionIds])
            ->andWhere('craft_relations.fieldId = :fieldId', ['fieldId' => $fieldIdImageGallery])
            ->queryColumn();

        return array_unique(array_merge($entryIdsFromMatrix,$entryIdsImageGallery));
    }

    /**
     * @return array
     */
    public function getAllAssetsRelatedToEntries(){
        // list the ids of the sections, so you need the id of news, information and events
        $sectionIds = [2, 7];
        // id of your insertedImage field
        $fieldIdMatrixContent = 6;
        // id of your imageGallery field
        $fieldIdImageGallery = 7;

        // fetches all assets that have a relation in the matrix field
        $imagesFromMatrixContent =  craft()->db->createCommand()
            ->select('craft_relations.targetId')
            ->from('relations')
            ->join('matrixblocks', 'craft_matrixblocks.id = craft_relations.sourceId')
            ->join('entries', 'craft_entries.id = craft_matrixblocks.ownerId')
            ->where('craft_relations.fieldId = :fieldId', ['fieldId' => $fieldIdMatrixContent])
            ->andWhere(['in', 'craft_entries.sectionId', $sectionIds])
            ->queryColumn();

        // fetches all assets that are related with the ImageGallery Field
        $imagesFromNormalField = craft()->db->createCommand()
            ->select('craft_relations.targetId')
            ->from('relations')
            ->where('craft_relations.fieldId = :fieldId', ['fieldId' => $fieldIdImageGallery])
            ->join('entries', 'craft_entries.id = craft_relations.sourceId')
            ->andWhere(['in', 'craft_entries.sectionId', $sectionIds])
            ->queryColumn();

        return array_unique(array_merge($imagesFromMatrixContent,$imagesFromNormalField));
    }

    // you can use this like that
    /**
    {% set allAssetIds = craft.pFplugin.getAllAssetsRelatedToEntries() %}
    {% for asset in craft.entries.id(allAssetIds).find() %}
        {{ asset.id }} {{ asset.title }}
        Entries for Image<br>
        {% set entriesForAsset = craft.pFplugin.getAllEntriesForAsset(asset.id) %}
            {% for entry in craft.entries().id(entriesForAsset).order('title') %}
            {{ entry.id }} {{ entry.title }}
        {% endfor %}
    {% endfor %}
     */


























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
    public function getAllNotificationsForUser($userId = null){
        $html = '';
        $entries = craft()->usernotifications->getAllNotificationsForUser($userId);
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