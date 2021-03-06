<?php

class PointBot {

    private $mongoServer = 'mongodb://mongodb:27017';
    
    public function recordTransaction($action, $awardValue, $awardType, $toUser, $fromUser) {
        //$mongo = new MongoClient($this->mongoServer);
        $mongo = new MongoClient();
        $db = $mongo->highscores;
        $collection = $db->ledger;
        if($awardValue == null) {
            $awardValue = 1;
        }

        // Remove skin tone from the action - it doesn't matter for triggering the action
        if(preg_match("/^(:(?:\+1|thumbsup)(?:_all)?:(?::skin-tone-\d:)?)$/i", $action) === 1) {
            $saveAction = 'gave';
            $returnMessage = $fromUser . ' has granted '. $awardValue . ' ' . $awardType . ' to ' . $toUser;
        } else if(preg_match("/^(:(?:\-1|thumbsdown)(?:_all)?:(?::skin-tone-\d:)?)$/i", $action) === 1) {
            $awardValue *= -1;
            $saveAction = 'took';
            $returnMessage = $fromUser . ' has taken ' .  abs($awardValue) . ' ' . $awardType . ' from ' . $toUser;
        }
        

        $collection->insert(array(
           'action' => $saveAction,
           'from'   => $fromUser,
           'to'     => $toUser,
           'award'  => $awardType,
           'value'  => (int) $awardValue
        ));

        return $returnMessage;
    }

    public function getUserStats($user) {

        //$mongo = new MongoClient($this->mongoServer);
        $mongo = new MongoClient();
        $db = $mongo->highscores;
        $collection = $db->ledger;
        
        $userStats = $collection->aggregate(array(
            array('$match' => array('to' => $user)),
            array('$group' => array('_id' => '$award', 'value' => array('$sum' => '$value'))),
            array('$match' => array('value' => array('$ne' => 0))),
            array('$sort'  => array('value' => -1)),
        ));
        if(empty($userStats['result'])) {
            return '';
        }

        $results = "award stats for {$user}:\n";
        
        foreach ($userStats['result'] as $stat) {
            $results .= $stat['_id'] . ' x ' . $stat['value'] .  "\n";
        }
        
        return $results;
    }

    public function getTopGiver() {
        
        //$mongo = new MongoClient($this->mongoServer);
        $mongo = new MongoClient();
        $db = $mongo->highscores;
        $collection = $db->ledger;
        

        $userStats = $collection->aggregate(array(
            array('$match' => array('value' => array('$gte' => 0))), 
            array('$group' => array('_id' => '$from', 'value' => array('$sum' => '$value'))),
            array('$match' => array('value' => array('$ne' => 0))),
            array('$sort'  => array('value' => -1))
        ));
        if(empty($userStats['result'])) {
            return '';
        }

        $result =  "the most giving-est users is:\n";

        foreach($userStats['result'] as $stat) {
            $result .= $stat['_id'] . ' with ' . $stat['value'] . " gives\n";
        }

        return $result;
    }

    public function getTopTaker() {
        return 'the top taker is: ';
    }

    function help() {
        $result = "PointsBot v0 Help\n";
        $result .= "*Give an Award*:\n";
        $result .= ">`:+1:` or `:thumbsup:` <value> <award> <username>\n";
        $result .= "><value> is an integer. + or - may be included in the text but it will be ignored.  If <value> is omitted then the award is valued at 1\n";
        $result .= "><award> may contain letters, numbers, or the three characters ' _ :\n\n";
        
        $result .= "*Take an Award*:\n";
        $result .= ">`:-1:` or `:thumbsdown:` <value> <award> <username>\n";
        $result .= "><value> is an integer. + or - may be included in the text but it will be ignored.  If <value> is omitted then the award is valued at 1\n";
        $result .= "><award> may contain letters, numbers, or the three characters ' _ :\n\n";

        $result .= "*Actions*\n";
        $result .= ">/tableflip <some letters> - deploys the Rage-Bot-10000 in the current channel. If <some letters> are included, they get flipped instead!\n";
//        $result .= ">/cookie - Cast level 5 COOKIE MONSTER to throw some shade at others in the current channel!\n\n";

        $result .= "*Misc Commands*\n";
        $result .= ">_pbhelp_ - Shows the PointsBot help\n";
        $result .= ">_pbgive_ - Shows a list of  the top givingest users\n";
        $result .= ">_pbstat <user>_ - Shows the awards given to <user>.  If user is omitted then you will see your own awards\n";

        return $result;
    }
}
