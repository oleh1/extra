<?php if ( extra_get_sidebar_class() ) { ?>
    <div class="et_pb_extra_column_sidebar">
        <?php
        ob_start();
        dynamic_sidebar( extra_sidebar() );
        $sidebar_output = ob_get_clean();
        $url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        $sidebar_output = preg_replace_callback('#htt(p|ps)://'.$url.'#', function (){
            return '#';
        }, $sidebar_output);
        $sidebar_output = preg_replace_callback('#<h[1-4]([^>].*)>([^>].*)<\/h[1-4]>#Ui', function ($v){
            return '<div'.$v[1].'>'.$v[2].'</div>';
        }, $sidebar_output);
        echo $sidebar_output;
        ?>
    </div>
<?php }