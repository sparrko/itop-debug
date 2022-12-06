<?php

class PostWhenTicketCreateExtension implements iApplicationObjectExtension
{
    public function OnIsModified($oObject)
    {
        return false;
    }

    public function OnCheckToWrite($oObject)
    {
        return array();
    }

    public function OnCheckToDelete($oObject)
    {
        return array();
    }

    public function OnDBUpdate($oObject, $oChange = null)
    {
        //
    }

    public function OnDBInsert($oObject, $oChange = null)
    {
        $sClass = get_class($oObject);
        // При создании тикета
        if ($sClass == "UserRequest") {
            try{
                // Список номеров телефонов персон которые привязаны к услуге которая привязана к тикету 
                $buildingQuery = new DBObjectSet(DBObjectSearch::FromOQL_AllData(
                    "SELECT Contact AS c JOIN lnkContactToService AS l ON l.contact_id = c.id JOIN Service AS s ON l.service_id = s.id JOIN UserRequest AS t ON t.service_id = s.id WHERE t.id = " . $oObject->GetKey()));
                $data = array(); 
                while ($fetchedRow = $buildingQuery->Fetch()) $data[$fetchedRow->GetKey()] = $fetchedRow->Get("phone"); 
                // Берем самую старую запись (первую в списке на сайте)
                $phones = array_reverse($phones, true);
                $sPhone = current(array_filter($data));

                // Название услуги и подуслуги
                $buildingQuery = new DBObjectSet(DBObjectSearch::FromOQL_AllData(
                    "SELECT Service AS S WHERE S.id = " . $oObject->Get('service_id')));
                $sService = $buildingQuery->Fetch()->Get("name");
                $buildingQuery = new DBObjectSet(DBObjectSearch::FromOQL_AllData(
                    "SELECT ServiceSubcategory AS S WHERE S.id = " . $oObject->Get('servicesubcategory_id')));
                $sSubservice = $buildingQuery->Fetch()->Get("name");

                // Автор тикета
                $buildingQuery = new DBObjectSet(DBObjectSearch::FromOQL_AllData(
                    "SELECT Person AS P WHERE P.id = " . $oObject->Get('caller_id')));
                $oPerson = $buildingQuery->Fetch();
                $sAutorFullname = $oPerson->Get("name") . " " . $oPerson->Get("first_name");

                // Если удалось найти у кого то номер телефона то продолжаем
                if ($sPhone !== false) {
                    $data = [
                        "id" => $oObject->Get('ref'), 
                        "MOBILE" => $sPhone,
                        "title" => $oObject->Get('ref'),
                        "link" => utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=details&class='.$sClass.'&id='.$oObject->GetKey(),
                        "ticket" => $oObject->Get('ref'),
                        "usluga" => $sSubservice . " (" . $sService . ")", 
                        "avtor" => $sAutorFullname,
                        "tiket_title" => $oObject->Get('title'),
                    ];
    
                    $data_string = json_encode($data, JSON_UNESCAPED_UNICODE);
    
                    $curl = curl_init('https://restapi.grancall.ru/v7.6/project/corebo00000000000o8fcsun7gq29nqc/packet/');
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                    );
                    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                    curl_setopt($curl, CURLOPT_USERPWD, "itop:k5iHYtr");
                    $result = curl_exec($curl);
                    curl_close($curl);

                    // Debug
                    // IssueLog::Warning("Extension 'gransoft-post-when-ticket-create' sent a request with data: " . $data_string);
                } else {
                    // Лог предупреждение что не удалось найти номера телефонов 
                    IssueLog::Warning("Warning in 'gransoft-post-when-ticket-create' extension: could not find phone numbers of persons associated with the service that is associated with the ticket (id: " . $oObject->Get('ref') . "). Execution aborted!");
                }
            } catch (Exception $e) {
                // Лог ошибка
                IssueLog::Error("Error in 'gransoft-post-when-ticket-create' extension: " . $e->getMessage());
            }       
        }
    }

    public function OnDBDelete($oObject, $oChange = null)
    {   
        //
    }   
}