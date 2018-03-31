<?php
	session_start();
	require_once('../models/user.php');
	require_once('../models/hash.php');

	$functions = array('login', 'register', 'update', 'validmail', 'unregister');

	function register(array $datas)
	{
		$err = NULL;

		if (!isset($datas['username']))
			$err[] = 'username';
		if (!isset($datas['email']))
			$err[] = 'email';
		if (!isset($datas['password']))
			$err[] = 'password';
		if (!isset($datas['firstname']))
			$err[] = 'firstname';
		if (!isset($datas['lastname']))
			$err[] = 'lastname';
		if ($err === NULL)
		{
			if (people_exist($datas['username']) === NULL)
			{
				$key = get_valid_key($datas['username']);
				return (people_create($datas['username'], $datas['email'],  $datas['password'], $datas['firstname'], $datas['lastname'], $key, 0));
			}
			else
				return (array('exist'));
		}
		else
			return $err;
	}

	function unregister(array $datas)
	{
		$err = NULL;
		if (!$datas['username'])
			$err[] = 'username';
		if ($err === NULL)
		{
			if (people_delete($datas['username']) === TRUE)
				return NULL;
			else
				return (array('accountnotfound'));
		}
		else
			return ($err);
	}

	function update(array $datas)
	{
		if ($_SESSION['admin']) {
			if (people_exist($datas['username']))
				return (people_update2($datas['username'], $datas['firstname'], $datas['lastname'], $datas['password'], $datas['address']));
			else
				return (array('no exist'));
		} else {
			if (people_exist($_SESSION['username']))
				return (people_update2($_SESSION['username'], $datas['firstname'], $datas['lastname'], $datas['password'], $datas['address']));
			else
				return (array('no exist'));
		}
	}

	function validmail(array $datas)
	{
		if ($datas['$key']) // ... imo, receive on validmail.html?key=...., send here on POST. AND Ajax this later :D
		{
			if ($_SESSION['username']) // Could ask password and change people_exist to people_get. Like in many other functions. I don't think it is necessary
			{
				$username = $_SESSION['username'];
				$people = people_exist($username);
				if ($datas['key'])
				{
					if ($people['key'] == $datas['$key'])
					{
						$tab['key'] = "";
					}
				}
				else
					return array("alreadyvalid");
			}
			else
				return array("notlogin");
		}
	}

	function login_bycookie(array $datas)
	{
		$err = NULL;

		if (!$datas['username'])
			$err[] = 'username';
		if (!$datas['cookie'])
			$err[] = 'cookie';
		if ($err !== NULL)
		{
			$datas = people_get($datas['username'], $datas['pasrd']);
			if ($datas === NULL)
				return (array('notfound'));
			$_SESSION['username'] = $datas['username'];
			return NULL;
		}
		else
			return ($err);
	}

	function login(array $datas)
	{
		$err = NULL;
		if (!isset($datas['username']))
			$err[] = 'username';
		if (!isset($datas['password']))
			$err[] = 'password';
		if ($err === NULL)
		{
			$datas = people_get($datas['username'], $datas['password']);
			if ($datas === NULL)
				return (array('notfound'));
			$_SESSION['username'] = $datas['username'];
			return NULL;
		}
		else
			return ($err);
	}

	if ($_POST['from'] && in_array($_POST['from'], $functions)) {
		$err = $_POST['from']($_POST);
		if (!($err === TRUE || $err === null)) {
			$str_error = implode('&', $err);
			if ($_POST['error']){
				header('Location: ../' . $_POST['error'] . '.php?' . $str_error);
				exit();
			}
			header('Location: ../' . $_POST['from'] . '.php?' . $str_error);
			exit();
		}
		header('Location: ../' . $_POST['success'] . '.php');
		exit();
	}
?>