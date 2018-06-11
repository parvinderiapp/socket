<?php

// $output = shell_exec('lsof -i :7085');
// echo $output;die;


// shell_exec('fuser -k -n tcp 9301');
// shell_exec('fuser -k -n tcp 9315');
// die;
// prevent the server from timing out
set_time_limit(0);


 


// include the web sockets server script (the server is started at the far bottom of this file)
require 'class.PHPWebSocket.php';


$port_number = 9305;
// $outout = shell_exec("netstat -tulnp | grep ".$port_number."");
// if(!empty($outout))
// {
// 	echo "Port is running";
// 	die;
// }
// if(empty($outout))
// {
// 	echo "Port is free";
// }

 
// when a client sends data to the server
function wsOnMessage($clientID, $message, $messageLength, $binary) {


	//$ar = json_decode($message, true);
	$socket_client_id = $clientID;
 
	if(!empty($ar))
	{
		 
			$result_data = array();
		 
		 
	}
	else
	{
		$result_data = array();
	}
	
	//$ar = json_decode($message, true);
	 
	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	// check if message length is 0
	if ($messageLength == 0) {
		$Server->wsClose($clientID);
		return;
	}
	// $DBServer = '127.0.0.1';
	// $DBUser   = 'root';
	// $DBPass   = 'welcome';
	// $DBName   = 'chatdb';
	// $conn = mysqli_connect($DBServer, $DBUser, $DBPass, $DBName);
	// $sql = "INSERT INTO friends (sharename, secret, username) VALUES ('studyeeee', 'maria', 'Juseman')";
	// $rs = $conn->query($sql);

	// $myfile = fopen("newfile".strtotime("now").".txt", "w") ;
	// $txt = $message;
	// fwrite($myfile, $txt);
	// fclose($myfile);
	$testData='deepuniyal';
	$Server->log( "$ip ($testData) has connected." );

	//The speaker is the only person in the room. Don't let them feel lonely.
	if ( sizeof($Server->wsClients) == 1 )
		$Server->wsSend($clientID, json_encode($result_data));
	else
		//Send the message to everyone but the person who said it


		$Server->wsSend($clientID, json_encode($result_data));
		foreach ( $Server->wsClients as $id => $client )
			if ( $id != $clientID )

				//print_r($client);

 				//$send_array = array('mode'=>'answer_result','answer_by'=>$ar['answer_by']);
				//$send_array = json_encode($ar);
			//	$Server->wsSend($id, json_encode($send_array));


				//$Server->wsSend($id, $send_array);
				//$Server->wsSend($id, "hello");
 				

				$Server->wsSend($id,$message);
}

// when a client connects
function wsOnOpen($clientID)
{

	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has connectedddddddd." );

	//Send a join notice to everyone but the person who joined
	foreach ( $Server->wsClients as $id => $client )
		if ( $id != $clientID )
			$Server->wsSend($id, "Visitor $clientID ($ip) has joined the room.");
}

// when a client closes or lost connection
function wsOnClose($clientID, $status) {


	  

	global $Server;
	$ip = long2ip( $Server->wsClients[$clientID][6] );

	$Server->log( "$ip ($clientID) has disconnected." );

	//Send a user left notice to everyone in the room
	foreach ( $Server->wsClients as $id => $client )
		$Server->wsSend($id, "Visitor $clientID ($ip) has left the room.");
}

// start the server
$Server = new PHPWebSocket();
$Server->bind('message', 'wsOnMessage');
$Server->bind('open', 'wsOnOpen');
$Server->bind('close', 'wsOnClose');
// for other computers to connect, you will probably need to change this to your LAN IP or external IP,
// alternatively use: gethostbyaddr(gethostbyname($_SERVER['SERVER_NAME']))
$Server->wsStartServer('192.168.1.195', $port_number);

?>