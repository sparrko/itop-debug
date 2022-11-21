<?php

class CustomTriggersPlugIn implements iApplicationObjectExtension
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
        return array();
    }

    public function OnDBInsert($oObject, $oChange = null)
    {
        $sClass = get_class($oObject);
        if ($sClass == "UserRequest") {
            $data = [
                "id" => $oObject->Get('ref'), 
                "MOBILE" => "111111",
                "title" => $oObject->Get('title'),
                "link" => utils::GetAbsoluteUrlAppRoot().'pages/UI.php?operation=details&class='.$sClass.'&id='.$oObject->GetKey()
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
            
            // die(dump($result));
        }
    }

    public function OnDBDelete($oObject, $oChange = null)
    {   
    }   
}