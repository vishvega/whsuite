<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Your New Password</title>

    <style>
    body { background: #EEEEEE;}
    .content { padding: 10px; margin: 10px; color: #FFFFFF; width: 100%; color: #333; }
    </style>
</head>
<body>
    <div class="content">
        <h1>Your New Password</h1>
        <p>We recieved a password reset request, which you confirmed by clicking the reset link in the previous email. Your new password is now enclosed below.</p>
        <p>We recommend changing this to something more secure and memorable once you have logged in. You should delete this email for your own security.</p>
        <p><b>New Password:</b> <?php echo $password; ?></p>
        <p>Regards,</p>
        <p>-SITENAME-</p>
        <p><b>Note:</b> This is an automated email.</p>
    </div>
</body>
</html>