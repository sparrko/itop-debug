<?php

class AddMenuExtension implements iPopupMenuExtension
{
	public static function EnumItems($iMenuId, $param)
	{
		$aResult = array();
		
		switch($iMenuId) // type of menu in which to add menu items
		{
            case iPopupMenuExtension::MENU_OBJDETAILS_ACTIONS:
				$oObj = $param;

                $aParams['class'] = $sTicketClass;
                $aParams['id'] = $oObj->GetKey();
                $aParams['operation'] = 'stimulus';
                $aParams['stimulus'] = $sEventToApply;
                $aParams['agent_id'] = $iAgentid;
                $sMenu = 'Menu:AssignToMe';
                $aResult = array(
                        new SeparatorPopupMenuItem(),
                        new URLPopupMenuItem(
                                $sMenu.' from '.$sTicketClass,
                                Dict::S($sMenu),
                                utils::GetAbsoluteUrlModulePage('gransoft-assign-to-me', 'action.php', $aParams)
                        ),
                );

				
            break;
		}
		return $aResult;
	}
}
