<?php if ( extra_get_sidebar_class() ) { ?>
    <div class="et_pb_extra_column_sidebar">
        <?php
        ob_start();
        dynamic_sidebar( extra_sidebar() );
        $sidebar_output = ob_get_clean();
        $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        echo preg_replace_callback('#htt(p|ps)://'.$url.'#', function (){
            return '#';
        }, $sidebar_output);
        ?>
    </div>
<?php }