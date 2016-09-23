<!DOCTYPE html>
<html>
    <head>
        <title>WHSuite Admin Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <?php
            echo $assets->style('bootstrap.min.css');
            echo $assets->style('style.min.css');

            echo $assets->script('jquery.min.js');
            echo $assets->script('jquery-ui.min.js');
            echo $assets->script('bootstrap.min.js');
        ?>
    </head>
    <body class="login_page">
        <div class="login">
            <div class="branding">WHSuite</div>
            <div class="panel">
                <div class="panel-content">
                    <?php
                    if (isset($message)):
                        echo $message;
                    endif;
                    ?>

                    <?php
                        echo $forms->open(array(
                            'action' => $router->generate('admin-login'),
                            'method' => 'post',
                            'class' => 'login-form'
                        ));

                        echo $forms->input('email', false, array(
                            'wrap' => false,
                            'class' => 'input-large input-block text-center',
                            'placeholder' => 'email address'
                        ));

                        echo $forms->password('password', false, array(
                            'wrap' => false,
                            'class' => 'input-large input-block text-center',
                            'placeholder' => 'password'
                        ));

                        echo $forms->checkbox('remember', 'Remember Me', array(
                            'wrap_class' => 'text-center',
                            'label' => array(
                                'class' => 'text-center'
                            )
                        ));

                        echo $forms->submit('submit', 'Login', array(
                            'class' => 'btn btn-primary btn-block btn-large'
                        ));
                    ?>
                        <p class="text-center">
                            <a href="<?php echo $router->generate('admin-forgotten-password'); ?>">
                                <small>Forgotten Your Login Details?</small>
                            </a>
                        </p>
                    <?php echo $forms->close(); ?>
                </div>
            </div>
        </div>
    </body>
</html>
