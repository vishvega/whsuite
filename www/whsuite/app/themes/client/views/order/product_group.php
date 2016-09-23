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
            <div class="panel-heading">
                <?php echo $lang->get('new_order'); ?>
                <?php if(isset($group->name)): ?>
                    : <?php echo $group->name; ?>
                <?php endif; ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-9">
                    <?php if (count($products) > 0): ?>
                        <?php foreach($products as $product): ?>
                        <?php
                            $pricing = ProductPricing::where('product_id', '=', $product->id)->where('currency_id', '=', $currency->id)->get();
                            if($pricing->count() == 0):
                                continue;
                            endif;
                        ?>
                        <?php echo $forms->open(array('action' => $router->generate('client-order'), 'method' => 'post', 'class' => '')); ?>
                            <?php echo $forms->hidden('product_id', array('value' => $product->id)); ?>
                            <div class="well">
                                <div class="row">
                                    <div class="col-md-9">
                                        <h3 class="nomargin"><?php echo $product->name; ?></h3>
                                        <?php echo html_entity_decode($product->description); ?>
                                    </div>
                                    <div class="col-md-3 text-center">
                                        <?php if (count($pricing) > 1): ?>
                                            <select name="billing_period" class="form-control">
                                            <?php foreach($pricing as $price): ?>
                                                <option value="<?php echo $price->billing_period_id; ?>">
                                                    <?php echo App::get('money')->format($price->price, $price->currency_id, false, true); ?>
                                                    <?php
                                                    if (isset($billing_periods[$price->billing_period_id])):
                                                        echo '(' . $billing_periods[$price->billing_period_id] . ')';
                                                    endif;
                                                    ?>
                                                </option>
                                            <?php endforeach; ?>
                                            </select>
                                        <?php elseif (isset($pricing[0])): ?>
                                            <h4>
                                                <?php echo App::get('money')->format($pricing['0']->price, $pricing[0]->currency_id, false, true); ?>
                                                <?php if (isset($billing_periods[$pricing[0]->billing_period_id])): ?>
                                                    <input type="hidden" name="billing_period" value="<?php echo $pricing[0]->billing_period_id; ?>">
                                                    <small>(<?php echo $billing_periods[$pricing[0]->billing_period_id]; ?>)</small>
                                                <?php endif; ?>
                                            </h4>
                                        <?php endif; ?>
                                        <br>
                                        <?php echo $forms->submit('submit', $lang->get('order_now'), array('class' => 'btn btn-primary btn-lg btn-block')); ?>
                                    </div>
                                </div>
                            </div>
                            <?php echo $forms->close(); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">
                            <?php echo $lang->get('no_results_found'); ?>
                        </div>
                    <?php endif; ?>
                    </div>
                    <div class="col-md-3 pull-right">
                        <?php echo $view->fetch('order/sidebar.php'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $view->fetch('elements/footer.php'); ?>
