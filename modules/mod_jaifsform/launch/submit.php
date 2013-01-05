<?php
		
	###Include our XMLRPC Library###
	include ("xmlrpc-3.0/lib/xmlrpc.inc");
	global $client,$key,$TID,$GID;	
	###Set our Infusionsoft application as the client###
	$client = new xmlrpc_client ( "https://ull89610.infusionsoft.com/api/xmlrpc" );
	
	###Return Raw PHP Types###
	$client->return_type = "phpvals";
	
	###Dont bother with certificate verification###
	$client->setSSLVerifyPeer ( FALSE );
	
	###Our API Key###
	$key = $params->get('ifskey',"ff68dcce9ac23a88c76037681e731b78");

	### Email Template ID ###
	$TID = intval($params->get('ifstid',"112213"));
	
	
	### Group or Tag ID ###
	$GID = intval($params->get('ifsgid',"291"));
	
	//Generate invitation codes
	function generateCode($CID) {
		$code = $CID . rand ( 1000000, 9999999 ); // Code = contact ID + a random 7-digist-number
		return $code;
	}
	
	//Set an email to subscribed list (Opt-in)
	function optIn($email) {
		global $client, $key;
		$call = new xmlrpcmsg ( "APIEmailService.optIn", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $email ), php_xmlrpc_encode ( 'subscribed for ThemeBrain beta test' ) ) );
		$result = $client->send ( $call );

		if (! $result->faultCode ()) {
			return true;
		} else {
			return false;
		}
		
	}
	
	//Add contact to a group/customer tag
	function addToGroup($CID, $GID) {
		global $client, $key;

		$call = new xmlrpcmsg ( "ContactService.addToGroup", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $CID ), php_xmlrpc_encode ( $GID ) ) );
		$result = $client->send ( $call );
		
		if (! $result->faultCode ()) {
			return true;
		} else {
			return false;
		}
	}
	
	//Get contact ID from input values (Name + Email), if contact doesn't exist, this will create a new contact and also return contact ID
	function getContactID($name, $email) {
		global $client, $key;
		$client->request_charset_encoding = "UTF-8";
		$call = new xmlrpcmsg ( "ContactService.findByEmail", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $email ), php_xmlrpc_encode ( array ('Id', '_InvitationCode' ) ) ) );
		$result = $client->send ( $call );

		if (! $result->faultCode ()) {
			
			$CID = $result->val [0] ['Id'];
			if (! $CID) {
				
				$name = explode ( ' ', $name, 2 ); //split name to get first name and last name
				
				
				$contact = array ("FirstName" => $name [0], "LastName" => $name [1], "Email" => $email, "_InvitationCode" => $code,'_usedcode'=>0 );
				$call = new xmlrpcmsg ( "ContactService.add", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $contact ) ) );
				$result = $client->send ( $call );
				if (! $result->faultCode ()) {
					$CID = $result->val;
				} else {
					return false;
				}
			}
			$code = generateCode ( $CID ); //generating invite code
			$call = new xmlrpcmsg ( "ContactService.update", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $CID ), php_xmlrpc_encode ( array ("_InvitationCode" => $code,'_usedcode'=>0 ) ) ) );
			$result = $client->send ( $call );
			return $CID;
		} else {
			return false;
		}
	}
	
	//Send mail to user with invitation code
	function sendEmail($CID, $TID) {
		global $client, $key;
		$call = new xmlrpcmsg ( "APIEmailService.sendEmail", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( array ($CID ) ), php_xmlrpc_encode ( $TID ) ) );
		
		$result = $client->send ( $call );
		if (! $result->faultCode ()) {
			return true;
		} else {
			return false;
		}
	}
	
	//Validate a code
	function validateCode($code) {
		global $client, $key;
		$CID = substr ( $code, 0, - 7 ); //remove last 7 digists to get contact ID
		$CID = intval ( $CID ); //to make sure this is a number
		$call = new xmlrpcmsg ( "ContactService.load", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $CID ), php_xmlrpc_encode ( array ("_InvitationCode" ) ) ) );
		$result = $client->send ( $call );
		if (! $result->faultCode ()) {
			if ($code == $result->val ['_InvitationCode']) {
				//Setting this value to let us know if an user used invite code
				$call = new xmlrpcmsg ( "ContactService.update", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $CID ), php_xmlrpc_encode ( array ("_usedcode" => 1 ) ) ) );
				$result = $client->send ( $call );
				return true;
			}
		} else {
			print $result->faultString () . "<BR>";
			return false;
		}
	}
	
	function findContact($TID) {
		global $client, $key;
		
		$call = new xmlrpcmsg ( "DataService.findByField", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( "ContactGroupAssign" ), php_xmlrpc_encode ( 600 ), php_xmlrpc_encode ( 0 ), php_xmlrpc_encode ( "GroupId" ), php_xmlrpc_encode ( $TID ), php_xmlrpc_encode ( array ("Contact.Email", "Contact.FirstName", "Contact.LastName" ) ) ) );
		
		$result = $client->send ( $call );
		if (! $result->faultCode ()) {
			return $result->value ();
			
		} else {
			print $result->faultString () . "<BR>";
			
		}
	}
	function updateCode($data) {
		foreach ( $data as $key => $contact ) {
			$email = $contact ['Contact.Email'];
			$fname = $contact ['Contact.FirstName'];
			$lname = $contact ['Contact.LastName'];
			//$CID = $contact['ContactId'];
			//$code = generateCode($CID);
			$CID = getContactID ( $fname . " " . $lname, $email );
			echo $CID . "<br>";
		}
	}
	
	//Load a Contact data
	function loadContact($CID, $data) {
		global $client, $key;
		
		$call = new xmlrpcmsg ( "ContactService.load", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $CID ), php_xmlrpc_encode ( $data ) ) );
		
		$result = $client->send ( $call );
		if (! $result->faultCode ()) {
			$val = $result->value();
			if($val)return $val;
			else return true;
		} else {
			return false;
		}
	}
	
	/*
	 * Log download package name
	 * 
	 * @CID: Contact ID
	 * @PName: Package Name (TB Palicico..)
	 * 
	 * @Return: Boolean
	 */
	function updateDownloadInfo($CID, $PName) {
		global $client, $key;
		$data = loadContact ( $CID, array ("_DownloadedPackages0" ) );
		if(is_array($data) && isset($data ["_DownloadedPackages0"])) {
			$data = $data ["_DownloadedPackages0"];
		}
		if (strpos ( $data, $PName ) === false) {
			if (!strlen ( $data ) || $data === true) {
				$dl = $PName;
			} else {
				$dl = $data . ", " . $PName;
			}
			$call = new xmlrpcmsg ( "ContactService.update", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( $CID ), php_xmlrpc_encode ( array ("_DownloadedPackages0" => $dl ) ) ) );
			$result = $client->send ( $call );
			return true;
		} else {
			
			//do nothing here
		}
		
		return false;
	}
	
	function signUp($FName, $LName, $Email) {
		global $client, $key;
		$call = new xmlrpcmsg ( "ContactService.addWithDupCheck", array (php_xmlrpc_encode ( $key ), php_xmlrpc_encode ( array ("FirstName" => $FName, "LastName" => $LName, "Email" => $Email ) ), php_xmlrpc_encode ( "Email" ) ) );
		$result = $client->send ( $call );
		if (! $result->faultCode ()) {
			return true;
		} else {
			return false;
		}
	}

	if (isset ( $_REQUEST ['inf_field_FirstName'], $_REQUEST ['inf_field_Email'])) {
		$name = $_REQUEST ['inf_field_FirstName'];
		$email = $_REQUEST ['inf_field_Email'];
		$CID = getContactID ( $name, $email );
		if ($CID) {
			optIn ( $email );
		      addToGroup ( $CID, $GID ); //adding to Joomla30 tag
		      sendEmail ( $CID, $TID );
			echo "Done";
		} else {
			echo "Not Done!";
		}
	} else {
		echo "No parameters";
	}
	
	?>