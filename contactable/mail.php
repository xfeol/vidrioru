<?php
	// Assign contact info
	$name = stripcslashes($_POST['name']);
	$emailAddr = stripcslashes($_POST['email']);
	$issue = stripcslashes($_POST['issue']);
	$comment = stripcslashes($_POST['message']);
	$subject = stripcslashes($_POST['subject']);
	$page = stripcslashes($_POST['page']);

	// Set headers
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

	// Format message
	$contactMessage =
	"<div>
	<p><strong>Имя отправителя:</strong> $name <br />
	<strong>E-mail:</strong> $emailAddr <br />
	<strong>Issue:</strong> $issue </p>

	<p><strong>Комментарий:</strong> $comment </p>

	<p><strong>IP отправителя:</strong> $_SERVER[REMOTE_ADDR]<br />
	<strong>Письмо отправлено со страницы:</strong> $page</p>
	</div>";

	// Send and check the message status
	$response = (mail('xfeol@mail.ru', $subject, $contactMessage, $headers) ) ? "success" : "failure" ;
	$output = json_encode(array("response" => $response));

	header('content-type: application/json; charset=utf-8');
	echo($output);

?>