            </div>
        </div>

        <div class="row">
            <div class="footer">
                <div class="col-lg-6">
                    <!-- The following copyright line MUST remain in place and visible unless you have purchased an unbranded WHSuite license. -->
                    <a href="http://whsuite.com" target="_blank">Powered by WHSuite Billing Software</a>
                </div>
                <div class="col-lg-6 text-right">
                    Copyright &copy; <?php echo date('Y'); ?> <?php echo $settings['general']['sitename']; ?>
                </div>
            </div>
        </div>
    </div>
    <?php echo $layout_js; // echo any JS added via assets->addScript() ?>
    <script>
        var please_wait_string = '<?php echo $lang->get('please_wait'); ?>';
    </script>
</body>
</html>
