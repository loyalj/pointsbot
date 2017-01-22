<?php
require '../vendor/autoload.php';
require '../flight/Flight.php';
require '../pointbot/pointbot.php';
require '../flip/flip.php';
require '../lib/minipg.php';


/*
*
*
*/
Flight::route('GET /', function(){
    $databaseUrl = getenv('DATABASE_URL');
    $miniPg = new MiniPG($databaseUrl);

    echo "ptBot v1";
    $miniPg->testConnection();
});

/*
*
*
*/
Flight::route('POST /', function(){
    // Collect the POST vars that slack sends
    $request = Flight::request();

    $token         = $request->data['token'];
    $fromUser      = $request->data['user_id'];
    $messageText   = $request->data['text'];
    $triggerWord   = $request->data['trigger_word'];

    $ourToken = getenv('OUR_TOKEN');
    
    if(empty($triggerWord) || $token != $ourToken) {
        exit;
    }
    
    $pointBot = new PointBot();
    
    switch ($triggerWord) {
        case ':thumbsup:':
        case ':thumbsup_all:':
        case ':thumbsdown:':
        case ':+1:':
        case ':-1:':
	        $messageMatches = null;
            $pattern = '/^(:[\+\-]?(?:1|thumbs(?:up|down))(?:_all)?:(?::skin-tone-\d:)?)\s+[\+\-]?([0-9]+)?\s*(.*)(<\@[a-zA-Z0-9]+>).*$/i';
	        preg_match($pattern, $messageText, $messageMatches);
	    
            // Validate we got a good token, user, message, and enough message tokens
	        if((empty($fromUser) || empty($messageText) || count($messageMatches) != 5)) {
	    	    exit;
	        }
	    
            // Grab the parts of our message so we can process them
	        $action        = trim($messageMatches[1]);
	        $awardValue    = trim($messageMatches[2]);
	        $awardType     = trim($messageMatches[3]);
	        $toUser        = trim($messageMatches[4]);
	        $fromUser      = '<@' . $fromUser . '>';
	    
	        $result = $pointBot->recordTransaction($action, $awardValue, $awardType, $toUser, $fromUser);

	    break;
	    case 'pbstat':
            $messageMatches = null;
	        $pattern = '/(pbstat)\s*(<\@[a-zA-Z0-9]*>)?.*/i';
	        preg_match($pattern, $messageText, $messageMatches);

            $user = '<@' . $fromUser . '>';
            
            if(!empty($messageMatches[2])) {
                $user = $messageMatches[2];
            }

            $result = $pointBot->getUserStats($user);
	    break;
        case 'pbgive':
            $result = $pointBot->getTopGiver();
        break;
        case 'pbtake':
            $result = $pointBot->getTopTaker();
        break;
        case 'pbhelp':
            $result = $pointBot->help();
        break;
    }
    
    header('Content-Type: application/json');
    echo json_encode(array('text'=> $result));
});


Flight::route('POST /slash', function(){

    $request = Flight::request();
    $token         = $request->data['token'];
    $fromUser      = $request->data['user_id'];
    $messageText   = $request->data['text'];
    $commandWord   = $request->data['command'];
    $channelName   = $request->data['channel_name'];

    $ourTokenFlip = getenv('OUR_TOKEN_FLIP');
    
    if(empty($commandWord) ||  !in_array($token, array($ourTokenFlip))) {
        exit;
    }
    
    $flipper = new Flip();
    
    switch($commandWord) {
        case '/tableflip':
            $result = $flipper->flipText($messageText);
            $botName = "RAGE-BOT-10000";
            $botIcon = ":rage4:";
        break;
    }
    
        $channelName = ($channelName) ? $channelName : "temptestbed";
        $data = "payload=" . json_encode(array(
                "channel"       =>  "#{$channelName}",
                "username"      =>  $botName,
                "text"          =>  $result,
                "icon_emoji"    =>  $botIcon
            ));

        // You can get your webhook endpoint from your Slack settings
        $ch = curl_init(OUR_URL_FLIP);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
});
// Launch the Flight controller
Flight::start();
