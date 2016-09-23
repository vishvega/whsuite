<?php echo $view->fetch('elements/header.php'); ?>

<div class="row">
    <?php if (count($group_links) >= 1 || count($currency_links) > 1): ?>
        <div class="col-lg-12">
            <div class="navbar navbar-default" role="navigation">
                <div class="col-xs-6">
                    <?php if(isset($group_links) && isset($group->name) && count($group_links) >= 1): ?>
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $group->name; ?> <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <?php foreach($group_links as $route => $group_name): ?>
                                        <li><a href="<?php echo $route; ?>"><?php echo $group_name; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-xs-6 pull-right">
                    <?php if(isset($currency_links) && count($currency_links) > 1): ?>
                        <ul class="nav navbar-nav pull-right">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$currency->code?> <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <?php foreach($currency_links as $route => $code): ?>
                                        <li><a href="<?php echo $route; ?>"><?php echo $code; ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="col-lg-12">
        <div class="panel panel-default" id="account-details">
            <div class="panel-heading"><?php echo $lang->get('new_order'); ?>: <?php echo $group->name; ?></div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
                        <?php echo $forms->open(array('action' => '', 'method' => 'post', 'class' => '', 'id' => 'domainForm')); ?>

                            <div class="row">
                                <div class="col-md-8 col-md-offset-2">
                                    <?php echo $forms->input('domain', '', array('class' => 'form-control input-lg', 'placeholder' => $lang->get('domain_placeholder'))); ?>
                                </div>
                            </div>

                            <div class="row">
                                <?php foreach($products as $product): ?>
                                    <?php $product_type = $product->ProductType()->first(); ?>

                                    <?php if($product_type->is_domain == '1'): ?>
                                    <?php $extension = $product->DomainExtension()->first(); ?>
                                        <div class="col-md-1">
                                            <div class="checkbox">
                                                <label for="ext_<?php echo $extension->id; ?>">
                                                    <input type="checkbox" name="extension[<?php echo $extension->id; ?>]" id="ext_<?php echo $extension->id; ?>">
                                                    <?php echo $extension->extension; ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                            </div>

                            <div class="row">
                                <div class="col-sm-8 col-md-offset-2 text-center">
                                    <?php echo $forms->submit('submit', $lang->get('check_availability'), array('class' => 'btn btn-primary btn-lg btn-block')); ?>
                                </div>
                            </div>
                        <?php echo $forms->close(); ?>

                        <div class="row">
                            <div class="col-lg-12" id="formResponse">
                                <div class="text-center" id="loading" style="display:none;">
                                    <hr>
                                    <div class="alert alert-info">
                                        <div class="loader"></div>
                                        <?php echo $lang->get('loading'); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-3">
                        <?php echo $view->fetch('order/sidebar.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$("#domainForm").submit(function( event ) {
    event.preventDefault();
    $('#loading').show();
    var extensions = [];
    $("input[type=checkbox]:checked").each(function() {
        extensions.push($(this).attr("id"));
    });

    $.ajax({
        type: "POST",
        url: "<?php echo $router->generate('client-domain-lookup-response', array('currency_id' => $currency->id)); ?>",
        data: {'domain': $("#domain").val(), 'extensions': extensions, '__csrf_value': $('#csrfValue').val()},
        success: function(data) {
            $("#formResponse").html(data);
        }
    });
});

</script>

<?php echo $view->fetch('elements/footer.php'); ?>
