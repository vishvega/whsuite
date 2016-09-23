
<?php if($pagination_last_page > 1): ?>

<ul class="pagination">
    <?php if ($pagination_current_page <= '1'): ?>
    <li class="disabled"><a href="#"><i class="fa fa-angle-left"></i></a></li>
    <li class="active"><a href="#">1</a></li>
    <?php else: ?>
    <li><a href="<?php echo $router->generate($pagination_route, array_merge($pagination_route_params, array('page' => ($pagination_current_page-1)))); ?><?php echo (isset($pagination_additional_params) ? $pagination_additional_params : null) ?>"><i class="fa fa-angle-left"></i></a></li>
    <li><a href="<?php echo $router->generate($pagination_route, array_merge($pagination_route_params, array('page' => '1'))); ?><?php echo (isset($pagination_additional_params) ? $pagination_additional_params : null) ?>">1</a></li>
    <?php endif; ?>

    <?php if ($pagination_current_page > 4): ?>
    <li class="disabled"><a>...</a></li>
    <?php endif; ?>

    <?php if ($pagination_last_page > 3): ?>
        <?php
        $start = 2;
        if ($pagination_current_page > 4):
            $start = $pagination_current_page-3;
        endif;

        for ($i=$start;$i<$pagination_current_page+4;$i++): ?>
            <?php if ($i > $pagination_last_page-1):
                continue;
            endif;
            ?>
            <?php if ($pagination_current_page == $i): ?>
            <li class="active"><a href="#"><?php echo $i; ?></a></li>
            <?php else: ?>
            <li><a href="<?php echo $router->generate($pagination_route, array_merge($pagination_route_params, array('page' => $i))); ?><?php echo (isset($pagination_additional_params) ? $pagination_additional_params : null) ?>"><?php echo $i; ?></a></li>
            <?php endif; ?>
        <?php endfor; ?>
    <?php else: ?>
        <?php for ($i=2;$i<$pagination_last_page;$i++): ?>
            <?php if ($pagination_current_page == $i): ?>
            <li class="active"><a href="#"><?php echo $i; ?></a></li>
            <?php else: ?>
            <li><a href="<?php echo $router->generate($pagination_route, array_merge($pagination_route_params, array('page' => $i))); ?><?php echo (isset($pagination_additional_params) ? $pagination_additional_params : null) ?>"><?php echo $i; ?></a></li>
            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>

    <?php if ($pagination_current_page < $pagination_last_page-3): ?>
    <li class="disabled"><a>...</a></li>
    <?php endif; ?>

    <?php if ($pagination_current_page >= $pagination_last_page): ?>
    <li class="active"><a href="#"><?php echo $pagination_current_page; ?></a></li>
    <li class="disabled"><a href="#"><i class="fa fa-angle-right"></i></a></li>
    <?php else: ?>
    <li><a href="<?php echo $router->generate($pagination_route, array_merge($pagination_route_params, array('page' => $pagination_last_page))); ?><?php echo (isset($pagination_additional_params) ? $pagination_additional_params : null) ?>"><?php echo $pagination_last_page; ?></a></li>
    <li><a href="<?php echo $router->generate($pagination_route, array_merge($pagination_route_params, array('page' => $pagination_current_page+1))); ?><?php echo (isset($pagination_additional_params) ? $pagination_additional_params : null) ?>"><i class="fa fa-angle-right"></i></a></li>
    <?php endif; ?>
</ul>
<?php endif; ?>
