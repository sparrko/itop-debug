<?php

require_once('const.php');

require_once('../approot.inc.php');
require_once(APPROOT.'/application/application.inc.php');
require_once(APPROOT.'/application/itopwebpage.class.inc.php');
require_once(APPROOT.'/application/wizardhelper.class.inc.php');

require_once(APPROOT.'/application/startup.inc.php');

try {
    $sClass = utils::ReadParam('class', '');
    $iTicketId = utils::ReadParam('id', '');
    $iAgentId = utils::ReadParam('agent_id', '');
    
    $oPerson = MetaModel::GetObject('Person', $iAgentId, false);
    $iTeamId = $oPerson->Get('team_id');

    $oTicket = MetaModel::GetObject('UserRequest', $iTicketId, false);
    $oTicket->Set('agent_id', $iAgentId);
    if ($iTeamId != null && $iTeamId != 0) $oTicket->Set('team_id', $iTeamId);

    $sStimulus = GATM_STATUSES_REGULATIONS[$oTicket->Get('status')];
    
    $oTicket->ApplyStimulus($sStimulus);
    $oTicket->DBUpdate();

    $oAppContext = new ApplicationContext();
    $oP = new iTopWebPage(Dict::S('UI:WelcomeToITop'));
    $oP->add_header('Location: '.utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=details&class='.$sClass.'&id='.$iTicketId.'&'.$oAppContext->GetForLink());
    $oP->output();
} catch(Exception $e) {
	require_once(APPROOT.'/setup/setuppage.class.inc.php');
	$oP = new SetupPage(Dict::S('UI:PageTitle:FatalError'));
	$oP->add("<h1>".Dict::S('UI:FatalErrorMessage')."</h1>\n");
	$oP->error(Dict::Format('UI:Error_Details', $e->getMessage()));
	$oP->output();

	if (MetaModel::IsLogEnabledIssue()) {
		if (MetaModel::IsValidClass('EventIssue')) {
			try {
				$oLog = new EventIssue();

				$oLog->Set('message', $e->getMessage());
				$oLog->Set('userinfo', '');
				$oLog->Set('issue', 'PHP Exception');
				$oLog->Set('impact', 'Page could not be displayed');
				$oLog->Set('callstack', $e->getTrace());
				$oLog->Set('data', array());
				$oLog->DBInsertNoReload();
			} catch(Exception $e) {
				IssueLog::Error("Failed to log issue into the DB");
			}
		}

		IssueLog::Error($e->getMessage());
	}
}