<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

require("vendor/autoload.php");

/**
Template Name: Chore Wheel
 */

$agents = get_users(array("role"=>"chore_wheel"));
$chores = explode(",", get_post_meta($wp_query->post->ID, "chores", true));

?>
<?php get_header(); ?>
<script>
//set default degree (360*5)
var degree = 1800;
//number of clicks = 0
var clicks = 0;

jQuery(document).ready(function(){
    $ = jQuery;
    /*WHEEL SPIN FUNCTION*/
    $('#spin').click(function(){
        
        //add 1 every click
        clicks ++;
        
        /*multiply the degree by number of clicks
      generate random number between 1 - 360, 
    then add to the new degree*/
        var newDegree = degree*clicks;
        var extraDegree = Math.floor(Math.random() * (360 - 1 + 1)) + 1;
        totalDegree = newDegree+extraDegree;
        
        /*let's make the spin btn to tilt every
        time the edge of the section hits 
        the indicator*/
        $('#wheel .sec').each(function(){
            var t = $(this);
            var noY = 0;
            
            var c = 0;
            var n = 700;        
            var interval = setInterval(function () {
                c++;                            
                if (c === n) { 
                    clearInterval(interval);                            
                }       
                    
                var aoY = t.offset().top;
                
                /*23.7 is the minumum offset number that 
                each section can get, in a 30 angle degree.
                So, if the offset reaches 23.7, then we know
                that it has a 30 degree angle and therefore, 
                exactly aligned with the spin btn*/
                if(aoY < 23.89){
                    console.log('<<<<<<<<');
                    $('#spin').addClass('spin');
                    setTimeout(function () { 
                        $('#spin').removeClass('spin');
                    }, 100);    
                }
            }, 10);
            
            $('#inner-wheel').css({
                'transform' : 'rotate(' + totalDegree + 'deg)'                  
            });
         
            noY = t.offset().top;
            
        });
    });
    
    
    
});//DOCUMENT READY
</script>
<style>
*{      margin:0;       padding:0; }

body{
    background:#eaeaea;
    font-size:18px;
    font-family: 'Exo 2', sans-serif;
}

/*WRAPPER*/
#wheel-wrapper{ 
    margin: 40px auto 0;        
    width:266px; 
    position:relative;
}

/*WHEEL*/
#wheel{
    width:250px;
    height:250px;
    border-radius:50%;  
    position:relative;
    overflow:hidden;
    border:8px solid #fff;
    box-shadow:rgba(0,0,0,0.2) 0px 0px 10px, rgba(0,0,0,0.05) 0px 3px 0px;
    transform: rotate(0deg);
}

#wheel:before{
    content:'';
    position:absolute;
    border:4px solid rgba(0,0,0,0.1);
    width:242px;
    height:242px;
    border-radius:50%;
    z-index:1000;       
}

#inner-wheel{
    width:100%;
    height:100%;
    
    -webkit-transition: all 6s cubic-bezier(0,.99,.44,.99);
    -moz-transition:    all 6 cubic-bezier(0,.99,.44,.99);
    -o-transition:      all 6s cubic-bezier(0,.99,.44,.99);
    -ms-transition:     all 6s cubic-bezier(0,.99,.44,.99);
    transition:         all 6s cubic-bezier(0,.99,.44,.99);     
}

#wheel div.sec{
    position: absolute;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 130px 75px 0;
    border-color: #19c transparent;
    transform-origin: 75px 129px;
    left:50px;
    top:-4px;   
    opacity:1;
}

#wheel div.sec .fa {
    width: 100px;
    margin-left: -50px !important;
    text-align: center !important;
}

<?php $num = sizeof($chores);
foreach ($chores as $i=>$chore) {
    $n = $i + 1;
    $deg = (360 / $num) * $n;?>
#wheel div.sec:nth-child(<?php echo $n;?>){
    transform: rotate(<?php echo $deg;?>deg);
    -webkit-transform: rotate(<?php echo $deg;?>deg);
    -moz-transform: rotate(<?php echo $deg;?>deg);
    -o-transform: rotate(<?php echo $deg;?>deg);
    -ms-transform: rotate(<?php echo $deg;?>deg);
    border-color: #<?php echo substr(md5($chore), 0, 6);?> transparent;
}

#wheel div.sec:nth-child(<?php echo $n;?>) .fa{
    color: white;
    margin-top: -100px;
    position: relative;
    z-index: 10000000;
    display: block;
    text-align: center;
    font-size:12pt;
    margin-left: <?php echo strpos($chore, " ") > 0 ? "-15px" : "-33px";?>;
    
    text-shadow: rgba(255, 255, 255, 0.1) 0px -1px 0px, rgba(0, 0, 0, 0.2) 0px 1px 0px;
}
<?php } ?>

#spin{
    width:68px;
    height:68px;
    position:absolute;
    top:50%;
    left:50%;
    margin:-34px 0 0 -34px;
    border-radius:50%;
    box-shadow:rgba(0,0,0,0.1) 0px 3px 0px;
    z-index:1000;
    background:#fff;
    cursor:pointer;
    font-family: 'Exo 2', sans-serif;
  
  -webkit-user-select: none; 
  -moz-user-select: none;    
  -ms-user-select: none;     
  -o-user-select: none;
  user-select: none;   
}


#spin:after{
    content:"SPIN";     
    text-align:center;
    line-height:68px;
    color:#CCC;
    text-shadow: 0 2px 0 #fff, 0 -2px 0 rgba(0,0,0,0.3) ;
    position: relative;
    z-index: 100000;
    width:68px;
    height:68px;
    display:block;
}

#spin:before{
    content:"";
    position:absolute;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 20px 28px 20px;
    border-color: transparent transparent #ffffff transparent;
    top:-12px;
    left:14px;
}

#inner-spin{
    width:54px;
    height:54px;
    position:absolute;
    top:50%;
    left:50%;
    margin:-27px 0 0 -27px;
    border-radius:50%;
    background:red;
    z-index:999;
    box-shadow:rgba(255,255,255,1) 0px -2px 0px inset, rgba(255,255,255,1) 0px 2px 0px inset,  rgba(0,0,0,0.4) 0px 0px 5px ;
    
    background: rgb(255,255,255); /* Old browsers */
    background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(234,234,234,1) 100%); /* FF3.6+ */
    background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(100%,rgba(234,234,234,1))); /* Chrome,Safari4+ */
    background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(234,234,234,1) 100%); /* Chrome10+,Safari5.1+ */
    background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(234,234,234,1) 100%); /* Opera 12+ */
    background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(234,234,234,1) 100%); /* IE10+ */
    background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(234,234,234,1) 100%); /* W3C */
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#eaeaea',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */    
}

#spin:active #inner-spin{
    box-shadow:rgba(0,0,0,0.4) 0px 0px 5px inset;
}

#spin:active:after{
    font-size:15px;     
}



#shine{
    width:250px;
    height:250px;
    position:absolute;
    top:0;
    left:0;
    background: -moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%, rgba(255,255,255,0.99) 1%, rgba(255,255,255,0.91) 9%, rgba(255,255,255,0) 100%); /* FF3.6+ */
background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,1)), color-stop(1%,rgba(255,255,255,0.99)), color-stop(9%,rgba(255,255,255,0.91)), color-stop(100%,rgba(255,255,255,0))); /* Chrome,Safari4+ */
background: -webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,0.99) 1%,rgba(255,255,255,0.91) 9%,rgba(255,255,255,0) 100%); /* Chrome10+,Safari5.1+ */
background: -o-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,0.99) 1%,rgba(255,255,255,0.91) 9%,rgba(255,255,255,0) 100%); /* Opera 12+ */
background: -ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,1) 0%,rgba(255,255,255,0.99) 1%,rgba(255,255,255,0.91) 9%,rgba(255,255,255,0) 100%); /* IE10+ */
background: radial-gradient(ellipse at center,  rgba(255,255,255,1) 0%,rgba(255,255,255,0.99) 1%,rgba(255,255,255,0.91) 9%,rgba(255,255,255,0) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#00ffffff',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */


opacity:0.1;
    
}



/*ANIMATION*/
@-webkit-keyframes hh {
  0%, 100%{
    transform: rotate(0deg);
    -webkit-transform: rotate(0deg);
  }

  50%{
    transform: rotate(7deg);
    -webkit-transform: rotate(7deg);
  }
}

@keyframes hh {
   0%, 100%{
    transform: rotate(0deg);
    -webkit-transform: rotate(0deg);
  }

  50%{
    transform: rotate(7deg);
    -webkit-transform: rotate(7deg);
  }
}

.spin {
  -webkit-animation: hh 0.1s; /* Chrome, Safari, Opera */
    animation: hh 0.1s;
}

</style>
<div id="content" class="grid col-940">

    <?php if ( have_posts() ) : ?>

        <?php while( have_posts() ) : the_post(); ?>

            <?php responsive_entry_before(); ?>
            <div id="post-<?php the_ID(); ?>" <?php post_class("timer"); ?>>
                <?php responsive_entry_top(); ?>

                <h1 class="post-title"><?php the_title(); ?></h1>
                <div class="post-edit"><?php edit_post_link( __( '[Edit]', 'responsive' ) ); ?></div>

                <div class="post-entry">
                    <div style="text-align: center;">
                    <select name="agent">
                    <?php foreach ($agents as $agent) { ?>
                        <option value="<?php echo $agent->nicename;?>"><?php echo $agent->display_name;?></option>
                    <?php } ?>
                    </select>
                    </div>

                    <div id="wheel-wrapper">
            
                        <div id="wheel">
                            <div id="inner-wheel">
                                <?php foreach ($chores as $chore) { ?>
                                <div class="sec"><span class="fa fa-bell-o"><?php echo $chore;?></span></div>
                                <?php } ?>
                            </div>

                            <div id="spin">
                                <div id="inner-spin"></div>
                            </div>
            
                            <div id="shine"></div>
                        </div>
                    </div>
                </div>
                <!-- end of .post-entry -->

                <?php responsive_entry_bottom(); ?>
            </div><!-- end of #post-<?php the_ID(); ?> -->
            <?php responsive_entry_after(); ?>

        <?php
        endwhile;

        get_template_part( 'loop-nav', get_post_type() );

    else :

        get_template_part( 'loop-no-posts', get_post_type() );

    endif;
    ?>

</div><!-- end of #content -->

<?php get_footer(); ?>
