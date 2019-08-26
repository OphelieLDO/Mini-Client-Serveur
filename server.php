<?php 
// set some variables
$host = "127.0.0.1";
$port = 30393;
$nbMaxClient = 1;


	
// don't timeout!
set_time_limit(0);

// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

// bind socket to port
$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");

// start listening for connections
$result = socket_listen($socket, $nbMaxClient) or die("Could not set up socket listener\n");

// accept incoming connections
// spawn another socket to handle communication
$spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
$input = "sortie";
while($input!="quit")
{

	// read client input
	$input = socket_read($spawn, 1024) or die("Could not read input\n");

	// clean up input string
	$input = trim($input);
	echo "Client Message : ".$input . "\n";
	$input=explode(" ",$input);
	if ($input[0]=="0") break;	
	switch($input[0])
	{
	
		case '1':
			$command=str_replace("\n","","cd ".$input[1]." && ls");
			echo $command;
			$output=shell_exec($command);
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
			break;

		case '2':
			$command=str_replace("\n","","ls");
			$output=shell_exec($command);
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
			break;
		case '3':
			$filename="Serveur/".$input[1];
			$handle = fopen($filename, "r");
			$output = fread($handle, filesize($filename));
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
			fclose($handle);
			break;
		case '4':
			$filename="Client/".$input[1];
			$handle = fopen($filename, "r");
			$output = fread($handle, filesize($filename));
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output\n");
			fclose($handle);
			break;
		case '5':
			$command = "ls Serveur/".$input[1];
			$output = shell_exec($command);
			$output=trim($output);
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output \n");
			$copie=explode("\n",$output);
			foreach($copie as $ligne)
			{
				$filename="Serveur/".$input[1]."/".$ligne;
				$filename=trim($filename);
				$fichier = fopen($filename, "r");
				$contenu = fread($fichier,filesize($filename));
				fclose($fichier);
				socket_write($spawn, $contenu, strlen ($contenu)) or die("Could not write output \n");
			}
			break;
		case '6':
			$command = "ls Client/".$input[1];
			$output = shell_exec($command);
			$output=trim($output);
			socket_write($spawn, $output, strlen ($output)) or die("Could not write output \n");
			$copie=explode("\n",$output);
			foreach($copie as $ligne)
			{
				$filename="Client/".$input[1]."/".$ligne;
				$filename=trim($filename);
				$fichier = fopen($filename, "r");
				$contenu = fread($fichier,filesize($filename));
				fclose($fichier);
				socket_write($spawn, $contenu, strlen ($contenu)) or die("Could not write output \n");
			}
			break;
		case '7':
			$BD=fopen($input[1],"r");
			if ($BD!=NULL)
			{
				$inside=fread($BD, filesize($input[1]));
			}
			trim($inside);
			$inside=explode("\n",$inside);
			$rep="Etudiant non trouvé";
			foreach($inside as $ligne)
			{
				$val=explode(",",$ligne);
				if ($input[2] == $val[0])
				{
					$rep=$val[0]." correspond a : ".$val[1]." ".$val[2];
					break;
				}
			}
			fclose($BD);
			socket_write($spawn, $rep, strlen ($rep)) or die("Could not write output \n");


	}

}
// close sockets
socket_close($spawn);
socket_close($socket);
?>
