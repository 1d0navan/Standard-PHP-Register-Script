<?php

	//Standard Register Form
	//Copyright 2019 Lukas Dörr / RZDEV.DE | Alle Rechte vorbehalten.
	
	//Load all required files
	require 'config.php';
	require 'functions.php';
	
	
	//Set true, if you need Terms of service acceptance.
	$check_tos = true;
	$tos_url = "info.php?page=tos";
	
	//Set true, if you need Privacy Policy acceptance.
	$check_privacy = true;
	$privacy_url = "info.php?page=privacy";
	
	//Where to send user when register validation fails. eave blank if the same page is used.
	$fail_url = "register.php"; //?err=1234 will be added.
	
	//Where to send the user, when register was successful?
	$success_url = "index.php?register=success";
	
	
	//Check if connection is ok
	if(!$con)
	{
		//Config is wrong or db is not connected
		?>
		<h1>FAILED TO CONNECT TO DATABASE!</h1>
		<?php
	}
	else
	{
		//Start User Session
		session_start();
		
		//Check if user is allready logged in.
		if(isset($_SESSION['user_id']))
		{
			//User is logged in. send him back
			header('location: user.php?err=1000');
			exit;
		}
		else
		{
			//Login Form or Login Procedere?
			if(!isset($_GET['do']))
			{
				//Register Form
				?>
<html>
	<head>
		<title>Register</title>
	</head>
	<body>
		<h1>Standard Register Procedere</h1>
		<form action="register.php?do=validate" method="post">
			<p>
				Username:<br>
				<input type="text" name="username" class="register_form" required autofocus>
			</p>
			<p>
				Password:<br>
				<input type="password" name="password_1" class="register_form" required>
			</p>
			<p>
				Repeat Password:<br>
				<input type="password" name="password_2" class="register_form" required>
			</p>
			<p>
				E-Mail Address:<br>
				<input type="email" name="email" class="register_form" required>
			</p>
			<p>
				<?php
					if($check_tos)
					{
						?>
						<input type="checkbox" name="accept_tos" id="1tos" required><span for="tos">I accept the <a href="<?php echo $tos_url; ?>">terms of service</a>.</span>
						<br>
						<?php
					}
					
					if($check_privacy)
					{
						?>
						<input type="checkbox" name="accept_privacy" id="1privacy" required><span for="tos">I accept the <a href="<?php echo $privacy_url; ?>">privacy policy</a>.</span>
						<br>
						<?php
					}
				?>
			</p>
			<p>
				<br>
				<input type="submit" class="register_form" value="Register">
			</p>
		</form>
	</body>
</html>
				<?php
			}
			else
			{
				//Do Register Procedere
				if($_GET['do'] == "validate")
				{
					//Clean all Form inputs
					$username = clean($_POST['username']);
					$password_1 = sha1(clean($_POST['password_1']));
					$password_2 = sha1(clean($_POST['password_2']));
					$email = clean($_POST['email']);
					$accept_tos = clean($_POST['accept_tos']);
					$accept_privacy = clean($_POST['accept_privacy']);
					
					//Check if passwords match
					if($password_1 <> $password_2)
					{
						//Passwords dont match.
						header('location: '.$fail_url.'?err=1001');
						exit;
					}
					else
					{
						//Passwords match
						//Check if tos or privacy policy is needed.
						if($check_tos)
						{
							if(!$accept_tos) //TODO
							{
								//Terms of service not accepted.
								header('location: '.$fail_url.'?err=1002');
								exit;
							}
						}
						if($check_privacy)
						{
							if(!$accept_privacy) //TODO
							{
								//Privacy Policy not accepted.
								header('location: '.$fail_url.'?err=1003');
								exit;
							}
						}
						
						
						//Check if username or email is allready in use
						
						$check_duplicate_text = "
						SELECT `username` FROM `".pfx."user` WHERE `username` = '$username' OR `email` = '$email' LIMIT 1";
						
						$check_duplicate_query = mysqli_query($con, $check_duplicate_text);
						if(mysqli_num_rows($check_duplicate_query))
						{
							//Username or email allready in use.
							header('location: '.$fail_url.'?err=1004');
							exit;
						}
						else
						{
							//everything looks fine. Lets add a user.
							$user_id = idgen(8, 2);
							$client_ip = $_SERVER['REMOTE_ADDR'];
							$rank = 1;
							$validation_key = idgen();
							$register_date = date('Y-m-d H:i:s');
							
							$insert_user_text = "
							INSERT INTO `user`(
							`user_id`, 
							`username`, 
							`password_hash`, 
							`email`, 
							`rank`, 
							`validation_key`, 
							`register_ip`, 
							`register_date`, 
							`deleted`) VALUES (
							'$user_id',
							'$username',
							'$password_1',
							'$email',
							'$rank',
							'$validation_key',
							'$client_ip',
							'$register_date',
							'0')";
							
							$inser_user_query = mysqli_query($con, $insert_user_text);
							
							//Check if insert worked.
							if(!$inser_user_query)
							{
								//The Insert command failed.
								header('location: '.$fail_url.'?err=1005');
								exit;
							}
							else
							{
								//Send user verification Mail.
								
								//TODO
								
								
								
								//Send user to success page after mail was sent.
								header('location: '.$success_url);
								exit;
							}
						}
					}
				}
			}
		}
	}