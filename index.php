
<?php

// Error debugging //
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);

// Include TwitterAPI to handle the heavy lifting //
require_once('TwitterAPIExchange.php');

// Set Twitter account //
//$TwitterAccount = "BBCBreaking";
//$TwitterAccount = "NASA";

// GET account html parameter, if none then use instagram. //

// MyTwitterApp/index.php?account=nasa

if (isset($_GET['account'])) {
    // echo $_GET['account'];
    $TwitterAccount = $_GET['account'];
}else{
    // Fallback behaviour goes here
    $TwitterAccount = "Instagram";
}


// oauth linked to your Twitter account
$settings = array(
    'oauth_access_token' => "YOUR KEYS",
    'oauth_access_token_secret' => "YOUR KEYS",
    'consumer_key' => "YOUR KEYS",
    'consumer_secret' => "YOUR KEYS"
);

$url = "https://api.twitter.com/1.1/statuses/user_timeline.json";

$requestMethod = "GET";
// define get fields / search terms ref  https://dev.twitter.com/rest/public/search
$getfield = '?screen_name=' . $TwitterAccount . '&count=100';

$twitter = new TwitterAPIExchange($settings);

// get Json data passing variables above using the TwitterAPIExchange scripts
$tweets = json_decode($twitter->setGetfield($getfield)
->buildOauth($url, $requestMethod)
->performRequest(),$assoc = TRUE);

// capture and display error
if($tweets["errors"][0]["message"] != "") {echo "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$tweets[errors][0]["message"]."</em></p>";exit();}

// display returned json formatted data
// echo "<pre>";
// print_r($tweets);
// echo "</pre>";

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
            <h2 style="padding-left:5px;">@<?php echo $TwitterAccount ?> Twitter</h2>
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
                echo '<video style="padding:5px;" width="310" controls poster="' . $media . '"><source src="' . $video . '" type="video/mp4">Your browser does not support the video tag.</video>';
            elseif ($media) {
                echo '<img style="width:310px;padding:5px;" src="' . $media . '"/>';
            } else {
                echo '<img style="height:50px;padding:15px;margin-left:120px;" src="./images/twitter_logo.png"/>';
            }
        echo '</div>';
        echo '<div class="tweetText">';
            echo '<p style="word-wrap:break-word;margin-right:10px;margin-top:5px;">' . $tweetTextWithLink . '</p>';
            echo '<span style="font-size:0.8em;">' . substr($createdAt,0,16) . '</span>'; 
        echo '</div>';
        echo '</div>';
        echo '<hr/>';
    };
    ?>

    <footer>
        <p>&copy; 2016 Robert Giffin.</p>
    </footer>
</body>
</html>


 