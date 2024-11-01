<?php
/*
Plugin Name: Simple Nested Menu
Plugin URI: https://github.com/mostafa272/Simple-Nested-Menu
Description: The Simple Nested Menu is a lightweight plugin for showing menu items in a pretty sliding style.
Version: 1.0
Author: Mostafa Shahiri<mostafa2134@gmail.com>
Author URI: https://github.com/mostafa272
*/
/*  Copyright 2009  Mostafa Shahiri(email : mostafa2134@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'wp_enqueue_scripts', 'simpl_simple_nested_menu_scripts' );

/**
 * Add our JS and CSS files
 */

function simpl_simple_nested_menu_scripts() {
  wp_register_script( 'simplenestedmenu-script', plugins_url( 'js/script.js', __FILE__ ),array('jquery'),'1.0',false );
        wp_register_style( 'simplenestedmenu-style', plugins_url( 'css/style.css', __FILE__ ) );
}
function recurseMenu($parent,$menu_items) {
           for($i=0;$i<count($menu_items);$i++){
$parents[$i]=$menu_items[$i]->menu_item_parent;
}
            $s = '<ul>';
            foreach($menu_items as $x) {
                if ($x->menu_item_parent == $parent) {
                    $s .= '<li><a href="'.$x->url.'"><span>'. $x->title .'</span></a>';

                    if (in_array($x->ID,$parents)) {
                        $s .= recurseMenu($x->ID,$menu_items);
                    }
                    $s .= '</li>';
                }

            }
            return $s .'</ul>';
        }

//callback function of shortcode
function simpl_simple_nested_menu_makeshortcode($atts) {
wp_enqueue_script( 'simplenestedmenu-script');
wp_enqueue_style( 'simplenestedmenu-style');
//Get attributes for shortcode
$a = shortcode_atts( array('name' => '', 'id'=>'', 'classname'=>'simpl-menu-class-name','bgcolor'=>'#f6f7f8','font'=>'#000000','fonthover'=>'#FFFFFF','bghover'=>'rgba(90, 200, 250, 0.25)','border'=>'dotted 2px #F0F0F0'), $atts );
$menu['name']=sanitize_text_field($a['name']);
$menu['id']=intval(sanitize_text_field($a['id']));
$classname=sanitize_text_field($a['classname']);
$bgcolor=sanitize_text_field($a['bgcolor']);
$font=sanitize_text_field($a['font']);
$fonthover=sanitize_text_field($a['fonthover']);
$hovercolor=sanitize_text_field($a['bghover']);
$border=sanitize_text_field($a['border']);
//Getting all navigation menus
$allmenus = get_terms('nav_menu' );
if((!empty($menu['id'])) || (!empty($menu['name'])) )
{
  foreach($allmenus as $m)
  {
    if(($m->term_id==$menu['id']) ||($m->name==$menu['name']))
    $menuid=$m->term_id;
  }
}
$menu_items = wp_get_nav_menu_items($menuid);


   if(!empty($menu_items))
   {
   $menu_list=recurseMenu(0,$menu_items);
    }
 ob_start();
 echo '<div class="simpl-classname" style="display:none;" data-name=".'.$classname.'" data-bgcolor="'.$bgcolor.'" data-font="'.$font.'" data-fonthover="'.$fonthover.'" data-hovercolor="'.$hovercolor.'" data-border="'.$border.'"></div>';
 echo '<div class="'.$classname.'">';

 echo $menu_list;
 echo '</div>';

 return ob_get_clean();
}

//add shortcode
add_shortcode('simple_nested_menu','simpl_simple_nested_menu_makeshortcode');