<?php if (! empty($toolbar)): ?>

    <div class="toolbar navbar navbar-default" role="navigation">
        <div class="navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">

            <?php foreach ($toolbar as $button): ?>
                <li>
                    <?php if(isset($button['route_params'])): ?>
                        <a href="<?php echo $router->generate($button['url_route'], $button['route_params']); ?>">
                    <?php else: ?>
                        <a href="<?php echo $router->generate($button['url_route']); ?>">
                    <?php endif; ?>

                    <?php if (! empty($button['icon'])): ?>
                        <i class="<?php echo $button['icon']; ?>"></i>
                    <?php endif; ?>

                    <?php if (! empty($button['label'])): ?>
                        <?php echo $lang->get($button['label']); ?>
                    <?php endif; ?>

                    </a>
                </li>
            <?php endforeach; ?>

            </ul>
        </div>
    </div>

<?php endif; ?>
