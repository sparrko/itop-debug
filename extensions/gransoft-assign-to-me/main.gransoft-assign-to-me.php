<?php

require_once('const.php');

class TicketAssignToMeExtension implements iPopupMenuExtension
{
	public static function EnumItems($iMenuId, $param)
	{
		$aResult = array();
		
		switch($iMenuId)
		{
            case iPopupMenuExtension::MENU_OBJDETAILS_ACTIONS:
                $oObj = $param;
                // Только в запросах
                if ($oObj instanceof Ticket) {
                    // Только с определенным статусом
                    if (array_key_exists($oObj->Get('status'), GATM_STATUSES_REGULATIONS)) {
                        // Только когда есть "Тип запроса"
                        if ($oObj->Get('request_type') != null && strlen($oObj->Get('request_type')) > 0) {
                            // Только когда агентом ты еще не являешься
                            if ($oObj->Get('agent_id') != UserRights::GetContactId()) {
                                $aParams['class'] = get_class($oObj);
                                $aParams['id'] = $oObj->GetKey();
                                $aParams['agent_id'] = UserRights::GetContactId();
                                $sMenu = GATM_STATUSES_REGULATIONS[$oObj->Get('status')] == 'ev_assign' ? 'Menu:AssignToMe' : 'Menu:ReassignToMe';
                                $aResult = array(
                                    new SeparatorPopupMenuItem(),
                                    new URLPopupMenuItem(
                                        $sMenu.' from '.get_class($oObj),
                                        Dict::S($sMenu),
                                        utils::GetAbsoluteUrlModulePage('gransoft-assign-to-me', 'action.php', $aParams)
                                    ),
                                );
                            }
                        }
                    }
                }                  
                break;
            default:
                $aResult = array();
                break;
		}
		return $aResult;
	}
}
