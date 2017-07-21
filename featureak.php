<?php
/*
Plugin Name: Featureak
Plugin URI: http://www.appchain.com/featureak
Description: Special dynamiccally designed category-links in special places.
Version: 1.0
Author: Ciprian Turcu
Author URI: http://www.appchain.com
*/

function featureak_FCategories($args) {
    $xDBArr = unserialize(get_option('featureak_Values'));
    if($xDBArr[0][4]!="4") {
        $xFinal = $args;
    }else {
        $xFinal='';
    }

    $xDBArr = unserialize(get_option('featureak_Values'));
    for($i=0;$i<$xDBArr[0][0];$i++) {

        $xCatLink = get_category_link($xDBArr[1][$i]);
        $xCatName = get_cat_name( $xDBArr[1][$i] );
        $xCatExists = false;
        foreach((get_the_category()) as $category) {
            if($xCatName==$category->cat_name) {
                $xCatExists = true;
                break;
            }
        }
        if($xCatExists==true) {
            $xCatStyle =' style="background:#'.$xDBArr[0][3].';border:1px solid #'.$xDBArr[0][2].';'.$xDBArr[0][5].'color:##'.$xDBArr[0][1].'" ';
            $xFinal .='<a href="'.$xCatLink.'" '.$xCatStyle.' title="'.$xCatName.'">'.$xCatName.'</a>';
        }
    }


    return $xFinal;
}

function featureak_init() {
    if(!is_admin()) {//must be sure we are not using admin version of the_author filter and that we are inside post
        $xDBArr = unserialize(get_option('featureak_Values'));
        switch($xDBArr[0][4]) {
            case "1";
                add_filter('the_author', 'featureak_FCategories');
                break;
            case "2";
                add_filter('the_date', 'featureak_FCategories');
                add_filter('get_the_time', 'featureak_FCategories');
                break;
            case "3";
                add_filter('the_tags', 'featureak_FCategories');
                break;
            case "4";
                add_filter('the_category', 'featureak_FCategories');
                break;
        }
    }
}
function featureak_AddStyle() {
    $myStyleUrl = WP_PLUGIN_URL . '/featureak/style.css';
    $myStyleFile = WP_PLUGIN_DIR . '/featureak/style.css';
    if ( file_exists($myStyleFile) ) {
        wp_register_style('featureak_StyleSheets', $myStyleUrl);
        wp_enqueue_style( 'featureak_StyleSheets');
    }
}
function featureak_Page() {
    if($_POST['featureak_Categories']) {
        $featureak_Categories= $_POST['featureak_Categories'];
        $featureak_TextColor= $_POST['featureak_TextColor'];
        $featureak_BorderColor= $_POST['featureak_BorderColor'];
        $featureak_BackgroundColor= $_POST['featureak_BackgroundColor'];
        $featureak_Place= $_POST['featureak_Place'];
        $featureak_CSS= $_POST['featureak_CSS'];

        $xPostArr[0][0] = $featureak_Categories;
        $xPostArr[0][1] = $featureak_TextColor;
        $xPostArr[0][2] = $featureak_BorderColor;
        $xPostArr[0][3] = $featureak_BackgroundColor;
        $xPostArr[0][4] = $featureak_Place;
        $xPostArr[0][5] = $featureak_CSS;

        for($i=0;$i<=$xPostArr[0][0];$i++) {
            $xPostArr[1][$i] = $_POST['featureak_Cat'.$i];
        }

        update_option('featureak_Values', serialize($xPostArr));
        $xDBArr = $xPostArr;

    }else {
        $xDBArr = unserialize(get_option('featureak_Values'));
    }
    if($xDBArr[0][5]=="") {
        $xDBArr[0][5] ='margin-left:3px;
padding:5px;
display:inline;
overflow:hidden;
';
    }


    ?>
<div class="wrap">
    <h2>Featureak</h2>
</div>
<form action="" id="featureak_Form" method="POST"/>
<table class="form-table">
    <tbody>
        <tr valign="top">
            <th>
                <h3>Design</h3>
            </th>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label>
                    Text color:
                </label>
            </th>
            <td>
                <input type="text" name="featureak_TextColor" value="<?php echo $xDBArr[0][1];?>" class="color" />( Click to select )
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label>
                    Border Color:
                </label>
            </th>
            <td>
                <input type="text" name="featureak_BorderColor" value="<?php echo $xDBArr[0][2];?>" class="color" />( Click to select )
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label>
                    Category BG Color:
                </label>
            </th>
            <td>
                <input type="text" name="featureak_BackgroundColor" value="<?php echo $xDBArr[0][3];?>" class="color" />( Click to select background color of the category box)
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label>
                    Categories Links CSS:
                </label>
            </th>
            <td>
                <textarea name="featureak_CSS" cols="50" rows="6"/><?php echo $xDBArr[0][5];?></textarea> (categories links css options here)
            </td>
        </tr>
        <tr valign="top">
            <th>
                <h3>Options</h3>
            </th>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label>
                    Place where:
                </label>
            </th>
            <td>
                <input type="radio" name="featureak_Place" value="1" <?php if($xDBArr[0][4]=="1") {echo " checked ";}?>> After Author<br>
                <input type="radio" name="featureak_Place" value="2" <?php if($xDBArr[0][4]=="2") {echo " checked ";}?>> After Date<br>
                <input type="radio" name="featureak_Place" value="3" <?php if($xDBArr[0][4]=="3") {echo " checked ";}?>> After Tags<br>
                <input type="radio" name="featureak_Place" value="4" <?php if($xDBArr[0][4]=="4") {echo " checked ";}?>> In place of Categories<br>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">
                <label>
                    Number of categories:
                </label>
            </th>
            <td>
                <select name="featureak_Categories" onchange="jQuery('#featureak_Form').submit();">
                        <?php
                        for($i=1;$i<=100;$i++) {
                            $xSelected='';
                            if($xDBArr[0][0]==$i) {$xSelected = " Selected ";}
                            $option = '<option value="'.$i.'"'.$xSelected.'>';
                            $option .= $i;
                            //$option .= ' ('.$cat->category_count.')';
                            $option .= '</option>';
                            echo $option;
                        }
                        ?>
                </select>( Click and select - it will submit content on selection )
            </td>
        </tr>
        <tr valign="top">
            <th>
                <h3>Categories</h3>
            </th>
        </tr>

            <?php
            for($i=0;$i<$xDBArr[0][0];$i++) {

                ?>
        <tr valign="top">
            <th scope="row">
                <label>
                    Category <?php echo ($i+1);?>:
                </label>
            </th>
            <td>
                <select name="featureak_Cat<?php echo $i;?>">
                            <?php
                            $args = array('hide_empty' => false);
                            $categories = get_categories($args);

                            foreach ($categories as $cat) {
                                $xSelected='';
                                if($xDBArr[1][$i]==$cat->cat_ID) {$xSelected = " Selected ";}
                                $option = '<option value="'.$cat->cat_ID.'"'.$xSelected.'>';
                                $option .= $cat->cat_name;
                                //$option .= ' ('.$cat->category_count.')';
                                $option .= '</option>';
                                echo $option;
                            }
                            ?>
                </select>
            </td>
        </tr>
            <?php } ?>
</table>
<br/><br/>
<input type="submit" value="Update" />
</form>
<?php
}
function featureak_admin_AddScript() {
    wp_register_script('featureak_Script', WP_PLUGIN_URL . '/featureak/includes/jscolor.js');
    wp_enqueue_script('featureak_Script');
}
function featureak_admin_AddStyle() {
    wp_register_style('featureak_admin_Style', WP_PLUGIN_URL . '/featureak/adminStyle.css');
    wp_enqueue_style('featureak_admin_Style');
}


function featureak_admin_menu() {
    add_options_page('My Plugin Options', 'Featureak', 8, __FILE__, 'featureak_Page');
}
add_action('init','featureak_init');
add_action('wp_print_styles', 'featureak_AddStyle');
add_action('admin_print_styles', 'featureak_admin_AddStyle');
add_action('admin_print_scripts', 'featureak_admin_AddScript');
add_action('admin_menu', 'featureak_admin_menu');

?>
