<?php 
$host    = "127.0.0.1";
$port    = 30393;


// create socket
$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

// connect to server
$result = socket_connect($socket, $host, $port) or die("Could not connect to server\n");


$choix=1;
while($choix != 0)
{
	echo "\nMenu:\n Tapez 0 pour quitter.\n";
	echo "Tapez 1 si vous voulez allez dans un repertoire. (Forme: nombre espace adresse à atteindre)\n";
	echo "Tapez 2 pour voir les dossier et fichier dans le dossier courant. \n";
	echo "Tapez 3 pour copier un fichier d'un serveur vers un client (Forme: nombre espace adresse source)\n";
	echo "Tapez 4 pour copier un fichier d'un client vers un serveur (Forme: nombre espace adresse source)\n";
	echo "Tapez 5 pour copier un repertoire d'un serveur vers le client (Forme: nombre espace nom du fichier du serveur\n";
	echo "Tapez 6 pour copier un repertoire d'un client vers le serveur (Forme: nombre espace nom du fichier du serveur\n";
	echo "Tapez 7 pour voir les donnees d'un etudiant. ( Forme: nombre espace adresse BD espace numero_etudiant)\n";
	echo "Tous les fichiers du serveur sont dans un dossier Serveur appelé automatiquement, idem pour le client dans un dossier Client\n";
	$choix=fgets(STDIN);
                                                                                                                                                                                                                                                                                           
	if($choix[0]==3)//Copier un fichier d'un serveur vers un client
	{
		socket_write($socket, $choix, strlen($choix)) or die("Could not send data to server\n");
		$choix=explode(" ",$choix);
		$filename="Client/".$choix[1];		
		$handle = fopen($filename, "w");
		$result = socket_read ($socket, 1024) or die("Could not read server response\n");
		fwrite($handle, $result);
		fclose($handle);
	}

	else if ($choix[0]==4)//Copier un fichier d'un client vers un serveur
	{
		
		socket_write($socket, $choix, strlen($choix)) or die("Could not send data to server\n");
		$choix=explode(" ",$choix);
		$filename="Serveur/".$choix[1];
		$handle = fopen($filename, "w");
		$result = socket_read ($socket, 1024) or die("Could not read server response\n");
		fwrite($handle, $result);
		fclose($handle);
	}
	else if ($choix[0]==5)//Copier un repertoire d'un serveur vers un client
	{
		socket_write($socket, $choix, strlen($choix)) or die("Could not send data to server\n");
		$choix=explode(" ",$choix);
		$command="mkdir Client/".$choix[1] or die("File Already Exists\n");
		shell_exec($command);
		$result = socket_read ($socket, 1024) or die("Could not read server response \n");
		$copie=explode("\n",$result);
		foreach($copie as $ligne)
		{
			$choix[1]=trim($choix[1]);
			$filename="Client/".$choix[1]."/".$ligne;
			$filename=trim($filename);
			$fichier=fopen($filename,"w");
			$result=socket_read ($socket, 1024) or die("Could not read server response \n");
			fwrite($fichier,$result);
			fclose($fichier);
		}
		echo "\n Copie réussie\n";			
	}
	else if ($choix[0]==6)//Copier un repertoire d'un client vers un serveur
	{
		socket_write($socket, $choix, strlen($choix)) or die("Could not send data to server\n");
		$choix=explode(" ",$choix);
		$command="mkdir Serveur/".$choix[1] or die("File Already Exists\n");
		shell_exec($command);
		$result = socket_read ($socket, 1024) or die("Could not read server response \n");
		$copie=explode("\n",$result);
		foreach($copie as $ligne)
		{
			$choix[1]=trim($choix[1]);
			$filename="Serveur/".$choix[1]."/".$ligne;
			$filename=trim($filename);
			$fichier=fopen($filename,"w");
			$result=socket_read ($socket, 1024) or die("Could not read server response \n");
			fwrite($fichier,$result);
			fclose($fichier);
		}
		echo "\n Copie réussie\n";			
	}
	else 
	{	socket_write($socket, $choix, strlen($choix)) or die("Could not send data to server\n");
		//Réponse du serveur
		$result = socket_read ($socket, 1024) or die("Could not read server response\n");
		echo "Reply From Server  :".$result;
	}
}
// close socket
socket_close($socket);
?>
