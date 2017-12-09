# Usernotifications plugin for Craft CMS

A plugin to display notifications

![Screenshot](resources/screenshots/plugin_logo.png)

## Installation

To install Usernotifications, follow these steps:

1. Download & unzip the file and place the `usernotifications` directory into your `craft/plugins` directory
2.  -OR- do a `git clone https://github.com/Anubarak/usernotifications.git` directly into your `craft/plugins` folder.  You can then update it with `git pull`
3.  -OR- install with Composer via `composer require Anubarak/usernotifications`
4. Install plugin in the Craft Control Panel under Settings > Plugins
5. The plugin folder should be named `usernotifications` for Craft to see it.  GitHub recently started appending `-master` (the branch name) to the name of the folder for zip file downloads.

Usernotifications works on Craft 2.4.x and Craft 2.5.x.

## Usernotifications Overview

During the install process a new section `usernotifications` is created with 5 test elements in it. Each entry type can have it's own template so you can create different entry types for different purposes which give you the freedom to define
 which user is able to see which message in your template like you are used to.

For example when you create a new entry type `onlyAdminsSeeMee` and the file `usernotifications/templates/entries/entrytypes/onlyAdminsSeeMee` and include 
```twig
{% if currentUser.idAdmin() %}
     hey admin, here is a new message {{ entry.content }}
{% endif %}
``` 

in the template only admins will see it and no one else.

This makes it really flexible.
You can add relation fields to your entry in order to have access to other entries as well in your template. For example if you would like to notify others about a new topic you'll relate this new topic to your notification and can do

```twig
{% set newTopic = entry.relationField.first() %}
hey dude :D want to check out {{ newTopic.title }}?
just click here <a href="{{ newTopic.getUrl()"></a>
```

To render notifications you have to do
```twig
{% set notifications = craft.usernotifications.getAllNotificationsForUser() %}
{{ notifications|raw }}
```


Brought to you by [Robin Schambach](www.secondre.de)
