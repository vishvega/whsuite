<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Password Recovery</title>

    <style>
    body { background: #EEEEEE;}
    .content { padding: 10px; margin: 10px; color: #FFFFFF; width: 100%; color: #333; }
    </style>
</head>
<body>
    <div class="content">
        <h1>Password Reset Request</h1>
        <p>A password reset request was recieved at -SITENAME- for your staff account. If this was not you who made the request, please contact a system administrator as soon as possible. Otherwise, please click (or copy) the link below to have a new password emailed to you.</p>
        <p><b>Password Reset Link:</b> <?php echo $resetUrl; ?></p>
        <p>Regards,</p>
        <p>-SITENAME-</p>
        <p><b>Note:</b> This is an automated email.</p>
    </div>
</body>
</html>