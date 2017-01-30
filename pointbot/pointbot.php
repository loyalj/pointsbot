<?php

require_once '../lib/minipg.php';

class PointBot {

    //private $mongoServer = 'mongodb://mongodb:27017';
    private $miniPg = null;
    

    /*
    *
    *
    */
    function __construct($databaseUrl) {
        $databaseUrl = getenv('DATABASE_URL');
        $this->miniPg = new MiniPG($databaseUrl);
    }
    

    /*
    *
    *
    */
    public function recordTransaction($action, $awardValue, $awardType, $toUser, $fromUser) {
        
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
        
        // Write data to database
        $this->miniPg->save(
            array(
               ':action' => $saveAction,
               ':fromUser'   => $fromUser,
               ':toUser'     => $toUser,
               ':award'  => $awardType,
               ':value'  => (int) $awardValue
            )
        );

        return ['text' => $returnMessage];
    }


    /*
    *
    *
    */
    public function getUserStats($user) {

        $userStats = $this->miniPg->getUserStats($user);

        if(empty($userStats)) {
            return ['text' => 'Dunno who that is!'];
        }

        $fields = [];
        
        foreach ($userStats as $row)
        {
            $fields[] = [
                'title' => $row['award'] . ' x ' . $row['tval'],
                'short' => true
            ];
        }

        return [
            'text' => "{$user} has been awarded all of these wonderful prizes:\n",
            'attachments'   =>  array([
                'fallback' => "award stats for {$user}:\n",
                'color' => '#87CEEB',
                'fields'  => $fields
            ])
        ];
    }


    /*
    *
    *
    */
    public function getTopGiver() {
        
        //$mongo = new MongoClient($this->mongoServer);
        /*$mongo = new MongoClient();
        $db = $mongo->highscores;
        $collection = $db->ledger;
        

        $userStats = $collection->aggregate(array(
            array('$match' => array('value' => array('$gte' => 0))), 
            array('$group' => array('_id' => '$from', 'value' => array('$sum' => '$value'))),
            array('$match' => array('value' => array('$ne' => 0))),
            array('$sort'  => array('value' => -1))
        ));*/

        if(empty($userStats['result'])) {
            return '';
        }

        $result =  "the most giving-est users is:\n";

        foreach($userStats['result'] as $stat) {
            $result .= $stat['_id'] . ' with ' . $stat['value'] . " gives\n";
        }

        return ['text' => $result];
    }

    /*
    *
    *
    */
    public function getTopTaker() {
        return ['text' => 'the top taker is: '];
    }


    /*
    *
    *
    */
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

        $result .= "*Misc Commands*\n";
        $result .= ">_pbhelp_ - Shows the PointsBot help\n";
        $result .= ">_pbgive_ - Shows a list of  the top givingest users\n";
        $result .= ">_pbstat <user>_ - Shows the awards given to <user>.  If user is omitted then you will see your own awards\n";

        return ['text' => $result];
    }
}
