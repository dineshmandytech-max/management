<?php

$email = $_POST["email"];
$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);
$mysqli = require __DIR__ . "/database.php";

$sql = "UPDATE user SET reset_token_hash = ?,reset_token_expires_at = ? WHERE email = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

if ($mysqli->affected_rows) {

    $mail = require __DIR__ . "/mailer.php";
    $mail->setFrom("YOUR_GMAIL@gmail.com");
    $mail->addAddress($email);
    $mail->Subject = "Password Reset";
    $mail->Body = <<<END

<p>Click the link below to reset your password:</p>

<p>
    <a href="http://127.0.0.1/my-projects/phpmailer/php-password-reset/reset-password.php?token=$token">
        Reset Password
    </a>
</p>

END;

    try {
        $mail->send();
        echo "Message sent, please check your inbox.";
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
    }
}