<?php

require_once 'YamahaMusiccast.class.php';

class YamahaMusiccastSocket {

	var $address = null;
	var $port = null;
	var $socket = null;

	public function __construct($adress, $port) {
		$this->adress = $adress;
		$this->port = $port;
	}

	function run() {
		$this->socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_bind($this->socket, $this->adress, $this->port) or $this->Logging(($this->close()));
		socket_listen($this->socket);
		while (true) {
			if ((socket_set_block($this->socket)) !== false) {
				//On tente d'obtenir l'IP du client.
				$message = null;
				$host = null;
				$port = null;
				$bytes_received = socket_recvfrom($this->socket, $message, 65536, 0, $host, $port);
				if ($message === 'stop')
					log::add('YamahaMusiccast', 'debug', 'Arrêt du socket');
				$this->close();
			} if ($message === 'test') {
				log::add('YamahaMusiccast', 'debug', 'Test du Socket');
			} else {
				YamahaMusiccast::traitement_message($host, $port, $message);
			}
		}
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
