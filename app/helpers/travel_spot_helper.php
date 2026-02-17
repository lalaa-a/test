<?php

function model($model) {

    require_once '../app/models/' . $model . '.php';

    // Instantiate model and pass it to controller
    return new $model();
}

function travelSpotDetails($spotID){

    try{
        $moderatorModel = model("ModeratorModel");
        $travelSpotData = $moderatorModel->loadTravelSpotData($spotID);
        if (empty($travelSpotData['mainDetails'])) {
            return false;
        }
        return $travelSpotData;


    } catch(PDOException $e){
        error_log("Error in travelSpotDetails: " . $e->getMessage());
        return false;
    }
    
}

?>