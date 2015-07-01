<?php
namespace Craft;

class MembersPlugin extends BasePlugin
{

  // adding hooks 
  // public function init()
  // {
  //   craft()->on('elements.onBeforeDeleteElements', function(Event $event) {

  //   });
  // }

  public function init()
  {
    // After user has been saved attach them to 'members' user group
    craft()->on('users.onSaveUser', function(Event $event) {
      $segment = craft()->request->getSegment(-1);
      $userGroup = craft()->userGroups->getGroupByHandle('members'); // Must have 'members' user group created first

      // Check if front-end registration
      if ($segment == 'register') {
        $userId = $event->params['user']['id'];

        craft()->userGroups->assignUserToGroups($userId, $userGroup->id);
      }
    });
  }

  public function getName()
  {
    return Craft::t('Members');
  }

  public function getVersion()
  {
    return '1.0.0';
  }

  public function getDeveloper()
  {
    return 'Roundhouse Agency';
  }

  public function getDeveloperUrl()
  {
    return 'https://github.com/roundhouse';
  }

  public function hasCpSection()
  {
    return true;
  }

  public function registerCpRoutes()
  {
    return array(
      'members/'    => 'members/index'
    );
  }
    
}
