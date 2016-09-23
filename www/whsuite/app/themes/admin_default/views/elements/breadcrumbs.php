<ul class="breadcrumbs">
    <?php
    foreach ($breadcrumb_links as $link):
        if (!$link['route']):
    ?>
        <li <?php echo $link['params']; ?>><?php echo $link['text']; ?></li>
    <?php
        else:
    ?>
        <li><a href="<?php echo $link['route']; ?>" <?php echo $link['params']; ?>><?php echo $link['text']; ?></a></li>
    <?php
        endif;
    endforeach;

    if (!empty($breadcrumb_notice)):
    ?>
        <li class="pull-right breadcrumb-alert"><a href="<?php echo $breadcrumb_notice['route']; ?>"><?php echo $breadcrumb_notice['text']; ?></a></li>
    <?php
    endif;
    ?>
</ul>
