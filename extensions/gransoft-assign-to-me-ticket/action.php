<?php

// if ($oObj instanceof Ticket) {
    // $oTicket = MetaModel::GetObject(get_class($oObj), $oObj->GetKey(), false);
    // $iAgentid = UserRights::GetContactId();
    $oTicket->Set('agent_id', $iAgentId);
// }