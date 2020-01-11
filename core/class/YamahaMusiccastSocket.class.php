<?php
class YamahaMusiccastSocket extends pht\Thread {

	var $address = null;
	var $port = null;
	var $socket = null;

	public function __construct($adress, $port) {
		$this->adress = $adress;
		$this->port = $port;
	}

	function run() {
		$this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_bind($this->socket, $this->adress,$this->port) or $this->Logging(($this->close()));
		socket_listen($this->socket);
		while (true) {
			$this->socketMessage(socket_accept($this->socket));
		}
	}

	/**
	 * Méthode qui traite le message.
	 */
	private function socketMessage($socketMessage) {
		$message = socket_read($socketMessage, 1024);
		if ($message == 'stop') {
			$this->close();
		}
		//On tente d'obtenir l'IP du client.
		$adress = null;
		$port = null;
		socket_getpeername($socketMessage, $adress, $port);
		$this->Logging('Nouvelle connexion client : ' . $adress . ':' . $port);
		$this->Logging('Message : ' . $message);
		socket_close($socketMessage);
	}

	/**
	 * 
	 * @param type $msg
	 * @return type
	 */
	function Logging($msg) {
		log::add('YamahaMusiccast', 'debug', 'Message ' . $msg);
		return;
	}

	/**
	 * Permet de fermer le socket ouver
	 * @param type $err
	 */
	function close($err = null) {
		if ($err != null) {
			$this->Logging($err);
		} else {
			$this->Logging(socket_strerror(socket_last_error()));
		}

		if (is_resource($fp)) {
			fclose($fp);
		}
		@socket_close($this->socket);
	}

}