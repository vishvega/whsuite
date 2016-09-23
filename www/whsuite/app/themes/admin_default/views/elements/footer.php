                <!-- The below powered by notice must remain in place unless you have an active no-branding license for this installation. -->
                <p class="footer-copyright"><a href="http://whsuite.com" target="_blank">Powered by WHSuite Billing Software</a></p>
            </div>
        </div>
        <?php
            echo $assets->script('respond.min.js');
            echo $assets->script('retina.min.js');
            echo $assets->script('footable.min.js');
            echo $assets->script('bootstrap-progressbar.min.js');
            echo $assets->script('application.min.js');

            echo $layout_js; // echo any JS added via assets->addScript()
        ?>
    </body>
</html>
