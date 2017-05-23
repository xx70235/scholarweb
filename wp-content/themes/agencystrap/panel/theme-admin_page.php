<?php

function FontAwesome_icons() {
    echo '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css"  rel="stylesheet">';
}

add_action('admin_head', 'FontAwesome_icons');
add_action('wp_head', 'FontAwesome_icons');

// admin area styling
function custom_admin_colors() {
   echo '<style type="text/css">

body {
	margin: 0px;
}


.grid {
	width: 100%;
	max-width: 1240px;
	min-width: 755px;
	margin: 0 auto;
	overflow: hidden;
}

.grid:after {
	content: "";
	display: table;
	clear: both;
}

.grid-pad {
	padding-top: 20px;
	padding-left: 0px; /* grid-space to left */
	padding-right: 0px; /* grid-space to right: (grid-space-left - column-space) e.g. 20px-20px=0 */
}

.col-1-4 {
	float: left;
	padding-right: 2%;
	width: 23%;
	text-align: center;
}

.col-1-3 {
	float: left;
	padding-right: 2%;
	width: 31.333%;
	text-align: center;
	}

.fa {
	font-size: 60px;
	color: #666;
}

.col-1-4 h4 {
	font-size: 16px;
}

button,
input[type="button"],
input[type="reset"],
input[type="submit"] {
	border: 2px solid;
	border-color: #57ad68;
	border-radius: 4px;
	background: #57ad68;
	box-shadow: none;
	font-size: 13px;
	line-height: 1;
	font-weight: 400;
	padding: 0.7em 1.5em 0.7em;
	text-shadow: none;
	color: #fff;
	cursor: pointer;
}

button:hover,
input[type="button"]:hover,
input[type="reset"]:hover,
input[type="submit"]:hover {
	border-color: #1cbda2;
}

button:focus,
input[type="button"]:focus,
input[type="reset"]:focus,
input[type="submit"]:focus,
button:active,
input[type="button"]:active,
input[type="reset"]:active,
input[type="submit"]:active {
	border-color: #1cbda2;
}

button.pro {
	font-size: 24px;
	padding: 1.25em 2em;
	text-align: center;
	margin: 20px auto 0;
	display: block;
}

a {
	text-decoration: none;
}

.custom-box {
    border: 1px solid #dadada;
    border-radius: 5px;
    cursor: pointer;
    margin-bottom: 30px;
    overflow: hidden;
    position: relative;
    width: 100%;
}
.custom-box:before {
    content: "";
    display: block;
    padding-top: 90%;
}
.home-collection {
    background: none repeat scroll 0 0 #fff;
}
.custom-content {
    bottom: 0;
    color: white;
    left: 0;
    position: absolute;
    right: 0;
    top: 0;
}
.custom-content div {
    display: table;
    height: 100%;
    width: 100%;
}
.custom-content span {
    color: #999;
    display: table-cell;
    padding: 20px;
    text-align: center;
    vertical-align: middle;
}
.custom-content span > .fa {
    color: #404040;
    display: block;
    font-size: 50px;
    margin: 0 auto;
    padding: 0 0 20px;
    transition: all 0.2s ease-in-out 0s;
}
.custom-content:hover .fa {
    color: #1cbda2;
    font-size: 58px;
    transition: all 0.2s ease-in-out 0s;
}
.custom-content span > h5 {
    color: #404040;
	font-size: 18px;
	line-height: 20px;
	margin: 0;
}
.custom-content span > p {
    font-size: 15px;
    margin-bottom: 0;
}

@media handheld, only screen and (max-width: 800px) {
	.grid {
		width: 100%;
		min-width: 0;
		margin-left: 0px;
		margin-right: 0px;
		padding-left: 0px; /* grid-space to left */
		padding-right: 10px; /* grid-space to right: (grid-space-left - column-space) e.g. 20px-10px=10px */
	}

	.col-1-4 {
		float: none;
		padding-right: 0px;
		width: 100%;
		text-align: center;
	}

	.col-1-3 {
		float: none;
		padding-right: 0px;
		width: 100%;
		text-align: center;
	}
}

}


         </style>';
}

add_action('admin_head', 'custom_admin_colors');


//add_action('admin_menu', 'agencystrap_setup_menu');

    function agencystrap_setup_menu(){
            add_menu_page( 'Agencystrap', 'Agencystrap', 'manage_options', 'agencystrap-setup', 'agencystrap_init' );
    }

 	function agencystrap_init(){
	 	echo '<div class="grid grid-pad"><div class="col-1-1">'; ?>
        <?php do_action("agencystrap_theme_name");?>
  <?php  echo "</div></div>";


		echo '<div class="grid grid-pad" style="border-bottom: 1px solid #ccc; padding-bottom: 40px; margin-bottom: 30px;" ><div class="col-1-3"><h2>';
		printf(esc_html__('LIGHTWEIGHT & FAST', 'agencystrap' ));
        echo "</h2>";

		echo '<p>';
		printf(esc_html__(' AgencyStrap is a fast, lightweight WordPress theme. All the feature can customize from customizer.', 'agencystrap' ));
		echo "</p>";

		echo '</div>';

		echo '<div class="col-1-3"><h2>';
		printf(esc_html__('SUPPORT', 'agencystrap' ));
        echo "</h2>";


		echo '<p>';
		printf(__('We Love to help our Customer, Please feel free to ask any question releted to the theme.', 'agencystrap' ));
		echo "</p>";

		echo '<a href="https://themingpress.com/submit-ticket/" target="_blank"><button>';
		printf(esc_html__('Visit Our Support Page', 'agencystrap' ));
		echo "</button></a></div>";

		echo '<div class="col-1-3"><h2>';
		printf(esc_html__('ABOUT THEMINGPRESS', 'agencystrap' ));
        echo "</h2>";

		echo '<p>';
		printf(__('ThemingPress is a online store of WordPress themes. Its maintain by <a href="https://themingstrap.com">ThemingStrap</a> Team', 'agencystrap' ));
		echo "</p>";

		echo '<a href="https://themingpress.com/about-themingpress/" target="_blank"><button>';
		printf(esc_html__('About Us', 'agencystrap' ));
		echo "</button></a></div></div>";


		echo '<div class="grid grid-pad senswp"><div class="col-1-1"><h1 style="padding-bottom: 30px; text-align: center;">';
		printf( esc_html__('OUR SERVICES', 'agencystrap' ));
		echo '</h1></div>';

        echo '<div class="col-1-4"><i class="fa fa-paper-plane" aria-hidden="true"></i><h4>';
		printf( esc_html__('UI/UX DESIGNING', 'agencystrap' ));
		echo '</h4>';

        echo '<p>';
		printf( esc_html__('Beautiful, responsive web design for businesses of every size, shape and context.', 'agencystrap' ));
		echo '</p></div>';

		echo '<div class="col-1-4"><i class="fa fa-wordpress" aria-hidden="true"></i><h4>';
        printf( esc_html__('WORDPRESS DEVELOPMENT', 'agencystrap' ));
		echo '</h4>';

        echo '<p>';
		printf( esc_html__('WordPress ensures we deliver what we say we will, on time and in-budget, exceeding expectations every time.', 'agencystrap' ));
		echo '</p></div>';

        echo '<div class="col-1-4"><i class="fa fa-object-group" aria-hidden="true"></i><h4>';
        printf( esc_html__('PSD TO WORDPRESS', 'agencystrap' ));
		echo '</h4>';

        echo '<p>';
		printf( esc_html__('Your design, sliced and hand coded into a high-quality, fully functional WordPress site starting from $59 only.', 'agencystrap' ));
		echo '</p></div>';

		echo '<div class="col-1-4"><i class="fa fa-life-ring" aria-hidden="true"></i><h4>';
		printf( esc_html__( 'WORDPRESS MAINTAINANCE', 'agencystrap' ));
		echo '</h4>';

        echo '<p>';
		printf( esc_html__( 'WEBSITE SETUP AND MAINTENANCE SERVICE WITH OUR TEAM STARTS FROM JUST $19', 'agencystrap' ));
		echo '</p></div>';


		echo '<div class="grid grid-pad" style="border-bottom: 1px solid #ccc; padding-bottom: 50px; margin-bottom: 30px;"><div class="col-1-1"><a href="https://themingstrap.com/quote/" target="_blank"><button class="pro">';
		printf( esc_html__( 'GET A QUOTE', 'agencystrap' ));
		echo '</button></a></div></div>';


    }
?>
