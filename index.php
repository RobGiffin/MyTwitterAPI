<?php
// Error debugging //
ini_set('display_errors', 'Off');
//error_reporting(E_ALL);

/*
* using file_get_contents
*/

$key = 'YOUR KEYS';
$secret = 'YOUR KEYS';
// replace with ACCOUNT
$api_endpoint = 'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=@_ACCOUNT&count=5'; // endpoint must support "Application-only authentication"

// request token
$basic_credentials = base64_encode($key.':'.$secret);
$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  =>    'Authorization: Basic '.$basic_credentials."\r\n".
        "Content-type: application/x-www-form-urlencoded;charset=UTF-8\r\n",
        'content' => 'grant_type=client_credentials'
    )
);

$context  = stream_context_create($opts);

// send request
$pre_token = file_get_contents('https://api.twitter.com/oauth2/token', false, $context);

$token = json_decode($pre_token, true);

if (isset($token["token_type"]) && $token["token_type"] == "bearer"){
    $opts = array('http' =>
        array(
            'method'  => 'GET',
            'header'  => 'Authorization: Bearer '.$token["access_token"]       
        )
    );

    $context  = stream_context_create($opts);

    $data = file_get_contents($api_endpoint, false, $context);

    //print $data;

    // get Json data and return an array - true is needed.
    $tweets = json_decode($data, true);

   // display returned json formatted data
   // echo "<pre>";
   //     print_r($tweets);
   // echo "</pre>";

    // capture and display error
    //if($tweets["errors"][0]["message"] != "") {echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$tweets[errors][0]["message"]."</em></p>";exit();}

}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width">
    <title>Twitter Feed</title>
  
    <link rel="stylesheet" href="styles.css">

    <!-- Load fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
 
</head>
<body>

    <!-- Build Header -->
    <nav>
        <div class="clearfix">
            <img style="position: absolute;right:0px;height:35px;padding:15px;float:left;padding-right:20px;" src="./images/twitter_logo.png"/>
            <h2 style="padding-left:5px;">@_ACCOUNT Twitter</h2>
        </div>
    </nav>


    <hr/>

    <!-- Main Contents -->

    <?php 
    for ($index = 0; $index <= count($tweets)-1; $index++) {
        $createdAt = $tweets[$index]["created_at"];
        $tweetText = $tweets[$index]["text"];
        $media = $tweets[$index]["entities"]["media"][0]["media_url"];
        //$URL = $tweets[$index]["entities"]["url"][0]["url"];
        //$media = $tweets[$index]["entities"]["media"];
        $video = $tweets[$index]["extended_entities"]["media"][0]["video_info"]['variants'][0]['url'];
     
        // Make HTTP a clickable link in tweet text.
        // The Regular Expression filter
        $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
        // Check if there is a url in the text
        if(preg_match($reg_exUrl, $tweetText, $url)) {
            // make the urls hyper links
            $tweetTextWithLink = preg_replace($reg_exUrl, "<a target='_blank' href=" . $url[0] . ">" . $url[0] . "</a> ", $tweetText);
        } else {
            // if no urls in the text just return the text
            $tweetTextWithLink = $tweetText;
        }

        echo '<div class="clearfix">';
        echo '<div class="tweetImageDiv">';
            if ($video)
                echo '<video style="padding:3px;" width="190px" controls poster="' . $media . '"><source src="' . $video . '" type="video/mp4">Your browser does not support the video tag.</video>';
            elseif ($media) {
                echo '<img style="width:190px;padding:3px;" src="' . $media . '"/>';
            } else {
                echo '<img style="height:50px;padding:15px;margin-left:50px;" src="./images/twitter_logo.png"/>';
            }
        echo '</div>';
        echo '<div class="tweetText">';
            echo '<p style="word-wrap:break-word;margin-right:0px;margin-top:0px;">' . $tweetTextWithLink . '</p>';
            echo '<span style="font-size:0.7em;line-height:3em;">' . substr($createdAt,0,16) . '</span>'; 
        echo '</div>';
        echo '</div>';
        echo '<hr/>';
    };
    ?>

</body>
</html>

