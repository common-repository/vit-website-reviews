<?php

/*
 *
 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * File Description: This is one of the two views for the admin page "Reviews".
 *
 */
/*
 * In case the user selects multiple records and submits the delete form it will delete the records through this function.
 * */
if ( isset( $_POST['submitMassDelete'] ) ) {
    $this->vit_wr_deleteSelectedReviews( $_POST['massDelete'] );
}
?>


<head>
    <style>
        /*override uikit white bg*/
        html.wp-toolbar {
            background: transparent !important;
        }

        td.filter{
            word-break: break-word;
        }

        .vit-wr-page-nav {
            text-align: center;
            margin: 0;
            font-size: 16px;
            padding: 5px 0px 5px 0px;
            color: #008ffb;
        }

        .vit-wr-nav-item {
            margin: 0 auto;
            padding: 5px 10px 5px 10px;
            color: #0B0C10;
            border-right: 2px solid #5e5d5f;
        }

        .vit-wr-nav-item:last-child {
            border: none;
        }

        .vit-wr-nav-item::after {
            background-color: #5e5d5f;
        }

        .svg-delete:hover path {
            fill: #BD1D3B !important;
        }

        .vit-wr-formsubmitbtn-delete-all-selected {
            margin-left: -35px;
            cursor: pointer;
        }

    </style>
</head>


<div>

    <div style="float:left; padding-right: 80px;">

        <span>Filter on stars</span>
        <select id="filter2">
            <option value="">Show all</option>
            <option value="1 star">1 star</option>
            <option value="2 stars">2 stars</option>
            <option value="3 stars">3 stars</option>
            <option value="4 stars">4 stars</option>
            <option value="5 stars">5 stars</option>
        </select>


    </div>

</div>
<form method="post">
    <input type="submit" class="vit-wr-formsubmitbtn-delete-all-selected uk-button-small uk-button-danger"
           name="submitMassDelete" value="Delete selected reviews"
           onclick="return confirm('Are you sure you want to delete all selected reviews? There is no way to retreive them after they have been deleted');">
    <a></a>
    <table id="table" class="uk-table uk-table-middle uk-table-striped uk-table-hover">
        <thead>
        <tr>
            <th class="uk-table-shrink"><input type="checkbox" id="select_all"></th>
            <th>Rating</th>
            <th class=""><span>Comment</span>


                <input class="uk-search-input" type="text" id="filter1" placeholder="Search..."
                       style="max-width: 200px; float: right">
                <span class="uk-search-icon-flip" style="float: right; margin-right: -194px; margin-top: 3px;"
                      uk-search-icon></span>

            </th>
            <?php 
?>
            <th>Date</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>

        <?php 
/*
 * This part is for pagination
 * */
// @info start of pagination code:
//Records per page
$per_page = 25;

if ( isset( $_GET["review_page"] ) ) {
    $currentPage = $_GET["review_page"];
} else {
    $currentPage = 1;
}

// Page will start from 0 and Multiple by Per Page

if ( isset( $currentPage ) ) {
    $start_from = ($currentPage - 1) * $per_page;
    // Selects all data of record Column
}

// @info end of Pagination code
/*
 * Get the reviews for the current page
 * */
$dbObj = new VIT_WR_Database();
$LimitedReviews = $dbObj->vit_wr_getLimitedReviews( $start_from, $per_page );
// For each review create a table row and insert review data.
foreach ( $LimitedReviews as $review ) {
    ?>

            <tr>
                <td><input class="checkbox" type="checkbox" name="massDelete[]" value="<?php 
    echo  esc_attr( $review->id ) ;
    ?>"></td>
                <td class="filter2"><?php 
    echo  esc_html( $review->rating ) ;
    ?> stars</td>
                <td class="filter"><?php 
    echo  esc_html( $review->comment ) ;
    ?></td>

                <?php 
    ?>
                <!-- Display The date: -->
                <td><?php 
    echo  esc_html( $review->createdOn ) ;
    ?></td>

                <td>
                    <span uk-icon="icon: check"></span>
                    <a href="?page=vit-wr-my-reviews&view=list&review_page=<?php 
    echo  $currentPage ;
    ?>&delete=<?php 
    echo  $review->id ;
    ?>" class=""
                       onclick="return confirm('Are you sure you want to delete this review?');">
                        <svg xmlns="http://www.w3.org/2000/svg" class="svg-delete" width="19" height="19"
                             viewBox="0 0 24 24">
                            <path fill="#f0506e"
                                  d="M20 4h-20v-2h5.711c.9 0 1.631-1.099 1.631-2h5.316c0 .901.73 2 1.631 2h5.711v2zm-7 15.5c0-1.267.37-2.447 1-3.448v-6.052c0-.552.447-1 1-1s1 .448 1 1v4.032c.879-.565 1.901-.922 3-1.006v-7.026h-18v18h13.82c-1.124-1.169-1.82-2.753-1.82-4.5zm-7 .5c0 .552-.447 1-1 1s-1-.448-1-1v-10c0-.552.447-1 1-1s1 .448 1 1v10zm5 0c0 .552-.447 1-1 1s-1-.448-1-1v-10c0-.552.447-1 1-1s1 .448 1 1v10zm13-.5c0 2.485-2.017 4.5-4.5 4.5s-4.5-2.015-4.5-4.5 2.017-4.5 4.5-4.5 4.5 2.015 4.5 4.5zm-3.086-2.122l-1.414 1.414-1.414-1.414-.707.708 1.414 1.414-1.414 1.414.707.708 1.414-1.414 1.414 1.414.708-.708-1.414-1.414 1.414-1.414-.708-.708z"/>
                        </svg>
                    </a></td>
                <input
                        type="hidden" class="uk-button-danger"

                        value="delete">
            </tr>
            <?php 
}
if ( empty($allReviews) ) {
    ?>
            <tr>
                <td colspan="6" style="text-align: center"><span
                            class="uk-text-danger">There are no website reviews yet!</span>
                </td>
            </tr>
            <?php 
}
?>

        </tbody>

        <!-- @info Pagination code  -->
        <tfoot>

        <?php 
//Using ceil function to divide the total records on per page
$total_pages = ceil( $ReviewRowCount / $per_page );
?>
        <div>
            <?php 
function goToNext()
{
    $currentPage = 1;
    if ( isset( $_GET["review_page"] ) ) {
        $currentPage = $_GET["review_page"];
    }
    $nextPage = $currentPage + 1;
    return $nextPage;
}

function goToPrev()
{
    $currentPage = 1;
    if ( isset( $_GET["review_page"] ) ) {
        $currentPage = $_GET["review_page"];
    }
    $prevPage = $currentPage - 1;
    return $prevPage;
}

//Going to first page
echo  "<tr><td colspan='6' ><div class = 'vit-wr-page-nav'>\r\n        <a href='admin.php?page=vit-wr-my-reviews&view=list&review_page=1' class='vit-wr-nav-item'>" . '« First' . "</a> " ;
$displayPrev = true;
$displayNext = true;
$displayLastPages = true;
// start at page 2 $i = 2; unless you go further than page 5 then show last 3 before & after:
for ( ( $currentPage >= 5 ? $i = $currentPage - 3 : ($i = 2) ) ;  $i <= $currentPage + 3 ;  $i++ ) {
    // Only show this if following situation occurs:
    
    if ( $currentPage >= 3 && $displayPrev == true ) {
        $prevPage = goToPrev();
        echo  "<a href='admin.php?page=vit-wr-my-reviews&view=list&review_page=" . $prevPage . "' class='vit-wr-nav-item'>" . 'Previous' . "</a> " ;
        // Set to false so it doesn't loop for every iteration.
        $displayPrev = false;
    }
    
    if ( $i < $total_pages ) {
        echo  "<a href='admin.php?page=vit-wr-my-reviews&view=list&review_page=" . $i . "' class='vit-wr-nav-item'>" . $i . "</a> " ;
    }
    // Only show this if following situation occurs:
    
    if ( $i >= $currentPage + 3 && $displayNext == true && $currentPage < $total_pages - 1 && $currentPage > 0 ) {
        $nextPage = goToNext();
        echo  "<a href='admin.php?page=vit-wr-my-reviews&view=list&review_page=" . $nextPage . "' class='vit-wr-nav-item'>" . 'Next' . "</a> " ;
        $displayNext = false;
        break;
    }

}
$page_url = 'admin.php?page=vit-wr-my-reviews&view=list&review_page=' . $total_pages;
?>

            <!-- Going to last page Swap total page for last page -->
            <a href='<?php 
echo  esc_url( $page_url ) ;
?>' class='vit-wr-nav-item'>Last »</a></td></tr></div>

        <?php 
echo  "<tr><td colspan='6' style='text-align: center; margin-top: -15px;' ><h5>" . esc_html( "you are on page " . $currentPage . " of " . $total_pages ) . "</h5></td></tr>" ;
?>
        <div>


        </tfoot>
        <!-- @info END of Pagination code  -->


    </table>
</form>
</div>
<script>
    //Makes the search bar case-insensitive
    jQuery.expr[':'].contains = function (a, i, m) {
        return jQuery(a).text().toUpperCase()
            .indexOf(m[3].toUpperCase()) >= 0;
    };


    /*
    * functionaly for the search filter on comments.
    * */
    jQuery('#filter1').on('input', function () {
        jQuery("#table td.filter:contains('" + jQuery(this).val() + "')").parent().show();
        jQuery("#table td.filter:not(:contains('" + jQuery(this).val() + "'))").parent().hide();
        if (jQuery('#filter1').val() != "") {
            jQuery('.uk-search-icon-flip').hide(200);
        } else {
            jQuery('.uk-search-icon-flip').show(200);
        }

    });

/*
* functionality for the filter on rating stars.
* */
    jQuery(function () {
        jQuery('#filter2').change(function () {
            jQuery("#table td.filter2:contains('" + jQuery(this).val() + "')").parent().show();
            jQuery("#table td.filter2:not(:contains('" + jQuery(this).val() + "'))").parent().hide();
        });
    });

    /*
    * Functionality for select all checkbox. (like deselect all if is already checked and vise versa)*/
    jQuery(document).ready(function () {
        jQuery('#select_all').on('click', function () {
            if (this.checked) {
                jQuery('.checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                jQuery('.checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        jQuery('.checkbox').on('click', function () {
            if (jQuery('.checkbox:checked').length == jQuery('.checkbox').length) {
                jQuery('#select_all').prop('checked', true);
            } else {
                jQuery('#select_all').prop('checked', false);
            }
        });
    });
    jQuery
</script>