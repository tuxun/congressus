<?php /*
	Copyright 2015-2017 Cédric Levieux, Parti Pirate
	
	This file is part of Congressus.
	
	Congressus is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	Congressus is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Congressus.  If not, see <http://www.gnu.org/licenses/>.
*/

class InternalAuthenticator {
	var $pdo = null;
	var $database = "";

	function __construct($pdo, $config) {
		$this->config = $config;
		if ($config["galette"]["db"]) {
			$this->database = $config["galette"]["db"] . ".";
		}
		else if ($config["database"]["db"]) {
			$this->database = $config["database"]["db"] . ".";
		}
		$this->pdo = $pdo;
	}

	static function newInstance($pdo, $config) {
		return new InternalAuthenticator($pdo, $config);
	}

	function computePassword($password) {
		require_once("engine/libs/password.php");
		return password_hash($password, PASSWORD_BCRYPT);
	}

	function forgotten($email, $password) {
		$internalPassword = $this->computePassword($password);

		$query = "	UPDATE ".$this->database."galette_adherents
					SET mdp_adh = :mdp_adh
					WHERE
						email_adh = :email_adh \n";

		$args = array("mdp_adh" => $internalPassword, "email_adh" => $email);

//		echo showQuery($query, $args);

		$statement = $this->pdo->prepare($query);
		$statement->execute($args);
	}

	function get($userId) {
		$query = "	SELECT *
					FROM ".$this->database."galette_adherents
					WHERE
						id_adh = :userId \n";
	
		$args = array("userId" => $userId);
		$statement = $this->pdo->prepare($query);
	
		//		echo showQuery($query, $args);
	
		$statement->execute($args);
		$results = $statement->fetchAll();
	
		foreach($results as $key => $member) {
			foreach($member as $field => $value) {
				if (is_numeric($field)) {
					unset($results[$key][$field]);
				}
			}
	
			return $results[$key];
		}
	
		return false;
	}

	function authenticate($login, $password) {
		require_once("engine/libs/password.php");

		$query = "	SELECT *
					FROM ".$this->database."galette_adherents
					WHERE
						login_adh = :login OR pseudo_adh = :login OR email_adh = :login \n";

		$args = array("login" => $login);
		$statement = $this->pdo->prepare($query);

//		echo showQuery($query, $args);

		$statement->execute($args);
		$results = $statement->fetchAll();

		foreach($results as $key => $member) {
			foreach($member as $field => $value) {
				if (is_numeric($field)) {
					unset($results[$key][$field]);
				}
			}

			if (!$results[$key]["use_activated"]) return false;
			if (password_verify($password, $results[$key]["mdp_adh"])) return $results[$key];
		}

		return false;
	}

	function changePassword($memberId, $password) {
		$internalPassword = $this->computePassword($password);
	}
}
