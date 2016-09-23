<?php
if (isset($error)) {
    echo $error;
}
?>
<div class="wrapper">
    <div class="login">
        <div class="branding">WHSuite</div>
        <div class="panel">
            <div class="panel-content">
                <form action="" method="post" class="login-form">
                    <input type="text" name="email" placeholder="email address" class="input-large input-block text-center">
                    <input type="password" name="password" placeholder="password" class="input-large input-block text-center">
                    <button type="submit" name="submit" class="btn btn-primary btn-block btn-large">Login</button>
                    <p class="text-center"><a href="/admin/login/forgotten-password/"><small>Forgotten Your Login Details?</small></a></p>
                </form>
            </div>
        </div>
    </div>
</div>