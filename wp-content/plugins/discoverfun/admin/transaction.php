<?php

function init_accomodation_log() {
?>
    <div class="wrap">
        <div id="icon-edit-pages" class="icon32 icon32-posts-page"><br></div>
        <h2>Accomodation Logs</h2>
        <?php 
            require(PLUGIN_PATH .'class/class.accomodation.php');
            $myListTable =  new Accomodation_List_Table();
            $myListTable->prepare_items(); 
        ?>
        <form method="post">
            <input type="hidden" name="page" value="hightlights">
            <?php
                $myListTable->search_box( 'search', 'search_id' );
                $myListTable->display(); 
            ?>
        </form>
    </div>

<?php    
}