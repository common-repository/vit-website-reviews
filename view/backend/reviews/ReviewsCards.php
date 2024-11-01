<!--
 *
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * File Description: This is one of the two views for the admin page "Reviews".
 *
-->


<style>
    /*override uikit white bg*/
    html.wp-toolbar {
        background: transparent !important;
    }
    .review-top {
        display: flex;
        justify-content: space-between;
    }
    .review-top-left {
        display: flex;
        flex-direction: column;
    }
    .review-top-left img {
        height: 35px; 
        width: 58px;
        padding-right: 5px;
        margin-bottom: 5px;
    }
    .review-top-right {
        display: flex;
        flex-direction: column;
    }
    .review-bottom {
        word-break: break-word;
    }
    .created-on {
        font-size: 14px;
        float: right;
    }

    .review-content {
        font-size: 14px;
        margin: 10px 0 0 0;
    }

    .nr {
        font-size: 14px;
        position: absolute;
        top: 10px;
        left: 15px;
    }

    .noselect {
        -webkit-touch-callout: none; /* iOS Safari */
        -webkit-user-select: none; /* Safari */
        -khtml-user-select: none; /* Konqueror HTML */
        -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
        user-select: none;
        /* Non-prefixed version, currently
                                         supported by Chrome and Opera */
    }
</style>
<div class="uk-child-width-1-3@s uk-grid-match" uk-grid>

    <?php 
foreach ( $allReviews as $review ) {
    ?>
        <div class="noselect">
            <div class="uk-card uk-card-default uk-card-hover uk-card-body">
                <p class="nr">#<?php 
    echo  $review->id ;
    ?></p>
                <div class="review-top">
                    <div class="review-top-left">
                        <?php 
    if ( website_review_plugin_sdk()->is_free_plan() ) {
        ?>
                            <svg class="svg-icon" viewBox="0 0 20 20" style="height: 43px;">
                                <path d="M12.075,10.812c1.358-0.853,2.242-2.507,2.242-4.037c0-2.181-1.795-4.618-4.198-4.618S5.921,4.594,5.921,6.775c0,1.53,0.884,3.185,2.242,4.037c-3.222,0.865-5.6,3.807-5.6,7.298c0,0.23,0.189,0.42,0.42,0.42h14.273c0.23,0,0.42-0.189,0.42-0.42C17.676,14.619,15.297,11.677,12.075,10.812 M6.761,6.775c0-2.162,1.773-3.778,3.358-3.778s3.359,1.616,3.359,3.778c0,2.162-1.774,3.778-3.359,3.778S6.761,8.937,6.761,6.775 M3.415,17.69c0.218-3.51,3.142-6.297,6.704-6.297c3.562,0,6.486,2.787,6.705,6.297H3.415z"></path>
                            </svg>
                        <?php 
    }
    ?>
                    </div>
                    <div class="review-top-right">
                        <span>
                            <?php 
    for ( $i = 0 ;  $i < 5 ;  $i++ ) {
        
        if ( $review->rating > $i ) {
            ?>
                                    <svg class="svg-icon" viewBox="0 0 20 20" style="height: 24px;">
        						          <path fill="gold"
                                          d="M16.85,7.275l-3.967-0.577l-1.773-3.593c-0.208-0.423-0.639-0.69-1.11-0.69s-0.902,0.267-1.11,0.69L7.116,6.699L3.148,7.275c-0.466,0.068-0.854,0.394-1,0.842c-0.145,0.448-0.023,0.941,0.314,1.27l2.871,2.799l-0.677,3.951c-0.08,0.464,0.112,0.934,0.493,1.211c0.217,0.156,0.472,0.236,0.728,0.236c0.197,0,0.396-0.048,0.577-0.143l3.547-1.864l3.548,1.864c0.18,0.095,0.381,0.143,0.576,0.143c0.256,0,0.512-0.08,0.729-0.236c0.381-0.277,0.572-0.747,0.492-1.211l-0.678-3.951l2.871-2.799c0.338-0.329,0.459-0.821,0.314-1.27C17.705,7.669,17.316,7.343,16.85,7.275z M13.336,11.754l0.787,4.591l-4.124-2.167l-4.124,2.167l0.788-4.591L3.326,8.5l4.612-0.67l2.062-4.177l2.062,4.177l4.613,0.67L13.336,11.754z"></path>
        						    </svg>
                                <?php 
        } else {
            ?>
                                    <svg class="svg-icon" viewBox="0 0 20 20" style="height: 24px;">
        							    <path fill="grey"
                                          d="M16.85,7.275l-3.967-0.577l-1.773-3.593c-0.208-0.423-0.639-0.69-1.11-0.69s-0.902,0.267-1.11,0.69L7.116,6.699L3.148,7.275c-0.466,0.068-0.854,0.394-1,0.842c-0.145,0.448-0.023,0.941,0.314,1.27l2.871,2.799l-0.677,3.951c-0.08,0.464,0.112,0.934,0.493,1.211c0.217,0.156,0.472,0.236,0.728,0.236c0.197,0,0.396-0.048,0.577-0.143l3.547-1.864l3.548,1.864c0.18,0.095,0.381,0.143,0.576,0.143c0.256,0,0.512-0.08,0.729-0.236c0.381-0.277,0.572-0.747,0.492-1.211l-0.678-3.951l2.871-2.799c0.338-0.329,0.459-0.821,0.314-1.27C17.705,7.669,17.316,7.343,16.85,7.275z M13.336,11.754l0.787,4.591l-4.124-2.167l-4.124,2.167l0.788-4.591L3.326,8.5l4.612-0.67l2.062-4.177l2.062,4.177l4.613,0.67L13.336,11.754z"></path>
        						    </svg>
                                <?php 
        }
    
    }
    ?> 
                        </span>
                        <span class="created-on uk-text-muted">
                            <?php 
    $reviewDateTime = strtotime( $review->createdOn );
    echo  esc_html( "on " . date( 'Y-m-d H:i', $reviewDateTime ) ) ;
    ?>
                        </span>
                    </div>
                </div>
                <div class="review-bottom">
                     <p class="review-content"><?php 
    echo  esc_html( $review->comment ) ;
    ?></p>
                </div>
            </div>
        </div>

        <?php 
}
if ( empty($allReviews) ) {
    ?>
        <div class="uk-card uk-card-hover uk-card-body uk-card-primary" style="margin-left: 40px;">
            <div class="uk-card-title">
                <h3 class="uk-card-title">Oops!</h3>
                <p>There are no reviews yet! Have someone post a review to see them popping up here.</p>
            </div>
        </div>
        <?php 
}
?>

</div>