<?php if (! empty($data)): ?>
    <tbody>
        <?php foreach ($data as $row): ?>
            <?php $row_array = $row->toArray(); ?>
            <tr>
                <?php foreach ($columns as $column): ?>
                <td>
                    <?php
                        if (! empty($column['field']) && ! is_array($column['field'])):
                            // single field

                            echo App::get('listingshelper')->singleField($row, $row_array, $column);

                        elseif (! empty($column['field']) && is_array($column['field'])):

                            // multiple fields

                            echo App::get('listingshelper')->multipleFields($row, $row_array, $column);

                        elseif (! empty($column['action']) && ! empty($actions[$column['action']])):
                            // action button

                            echo App::get('listingshelper')->actionButton($row, $row_array, $column, $actions);

                        endif;
                    ?>
                </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
<?php endif; ?>