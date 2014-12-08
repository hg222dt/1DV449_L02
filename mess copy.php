<?php

require_once("get.php");
require_once("sec.php");
sec_session_start();


checkUser();

?>
        <!DOCTYPE html>
            <html lang="sv">
              <head>
                <meta charset="utf-8">
                <meta http-equiv="X-UA-Compatible" content="IE=edge">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <meta name="description" content="">
                <meta name="author" content="">
                <link rel="apple-touch-icon" href="touch-icon-iphone.png">
                <link rel="apple-touch-icon" sizes="76x76" href="touch-icon-ipad.png">
                <link rel="apple-touch-icon" sizes="120x120" href="touch-icon-iphone-retina.png">
                <link rel="apple-touch-icon" sizes="152x152" href="touch-icon-ipad-retina.png">
                <link rel="shortcut icon" href="pic/favicon.png">
               
                <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
                
                <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
                <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>


            	<script type="text/javascript" src="js/longpoll.js"></script>

            	<style type="text/css">
            	
            body{

                margin:0;
                padding:0;
                
                background-color:#C3D9FF;
/*                background-repeat:no-repeat;
                background-position:center top;*/
                height:4000px;
                padding-top:200px;

                
                font-family:verdana, helvetica, Sans-Serif;
                font-size:12px;
            }

            #container {
              

            }

            #messageboard{
                width:600px;
                margin:auto;
                border:1px solid silver;
                background:white;
                padding:10px;
                padding-bottom:20px;
            }



            #numberOfMess {
                float:right;
                font-size:small;
                color:Gray;
            }
/*
            html {
                background:url(pic/b.jpg);
            }

*/
            html {
                background:url(pic/result.png);
                background-position: -0px -301px; 
                width: 100%; 
                height: 683px;

            }


            .message {
                width:590px;
                background-color:#CDEB8B;
                color:gray;
                margin-bottom:5px;
                padding:5px;
                font-size:22px;
                border:2px solid #73880A;
            }
            .message p {
                display:inline;
               
            }


            .message img {
                float:right;
                padding-left:5px;
                border:none;
            }


            .message span {
                display:block;
                font-size:10px;
                color:gray;
                width:100%;
                padding:0;
                margin:0;
                text-align:right;

            }

            #inputText{
                width:590px;
                height:150px;
                border:1px solid silver;
                font-family:verdana, helvetica, Sans-Serif;
                font-size:20px;
                padding:5px;

            }

            #inputName {
            	width:300px;
                height:50px;
                border:1px solid silver;
                font-family:verdana, helvetica, Sans-Serif;
                font-size:20px;
                padding:5px;
            }
            .blur{
                background-color: white;
            }

            .focus{
            background-color: #F3FCE4;
            }

            .clear {
                clear:both;

            }

            .logoImage {
                background: url(pic/result.png);
                background-position: -0px -0px;
                width: 1000px; 
                height: 257px;

                /*background-position:center top;*/
                
                padding-top:200px;

                margin-left: 30%;
/*                margin-right: auto;*/
                
            }

            .clockImage {
                background: url(pic/result.png);
                background-position: -0px -267px; 
                width: 24px; 
                height: 24px;
                float: right;
            }


            .debug {
                

            }
            	
            	</style>
            	
            	
            	<script src="MessageBoardMin.js"></script>
            	<script src="js/script.js"></script>
            	<script src="Message.js"></script>
                
            	<title>Messy Labbage</title>
              </head>
            	  
            	  	<!--<body background="http://www.lockley.net/backgds/big_leo_minor.jpg">     -->

                    <div class="logoImage"> </div>   

                    <div id="container">
                        
                        <div id="messageboard">
                            <form action="logout.php" method="POST">
                                <input id="logout" class="btn btn-danger" name="logout" type="submit" value="Logout" style="margin-bottom: 20px;"/>
                            </form>
                            <div id="messagearea"></div>
                            
                            <p id="numberOfMess">Antal meddelanden: <span id="nrOfMessages">0</span></p>
                            Name:<br /> <input id="inputName" type="text" name="name" /><br />
                            Message: <br />
                            <textarea name="mess" id="inputText" cols="55" rows="6"></textarea>
                            <input class="btn btn-primary" type="button" id="buttonSend" value="Write your message" />
                            <input type="hidden" id="csrfToken" value= <?php echo($_SESSION['csrfToken']) ?> >
                            <span class="clear">&nbsp;</span>

                        </div>

                    </div>
                    
                    <!-- This script is running to get the messages -->
            			<script>
            				$(document).ready(function() {
            					MessageBoard.getMessages();
            				});
            			</script>

            	</body>
            </html>