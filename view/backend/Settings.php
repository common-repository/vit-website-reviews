<!--

 * Author URI: https://vincoit.com
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * File Description: This is the view for the admin page "Settings"
 * 
-->    <style>
        /*override uikit white bg*/
        html.wp-toolbar {
            background: transparent !important;
        }

        .uk-alert-primary {
            width: fit-content;
            padding: 15px 40px 15px 15px;
        }

        .vit-wr-wrapper {
            padding: 35px;
            position: relative;
        }

        .vit-wr-wrapper > .form-margin {
            margin-bottom: 20px !important;
        }

        .uk-text-muted {
            display: block;
        }

        .vit-wr-form-check-label {
            margin-left: 5px;
            margin-top: -8px;
            -webkit-touch-callout: none; /* iOS Safari */
            -webkit-user-select: none; /* Safari */
            -khtml-user-select: none; /* Konqueror HTML */
            -moz-user-select: none; /* Firefox */
            -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none;
            /* Non-prefixed version, currently
                                             supported by Chrome and Opera */
        }

        .form-margin {
            margin-bottom: 40px !important;
        }

        .color-select input {
            padding: 1px;
        }

        .color-select .vit-wr-form-check-input {
            margin-top: 0px;
        }

        .color-select .vit-wr-form-check-label {
            margin-top: 0px;
        }

        .vit-wr-form-check {
            padding-bottom: 10px;
        }

        .color-select > .vit-wr-form-check-label {
            margin-left: 0;
        }

        .color-select > .uk-text-muted {
            margin-bottom: 10px;
        }

        .col-4 {
            background-color: rgba(0, 0, 0, 0.05);
            margin: 15px 15px 15px 15px;
        }

        .vit-wr-formsubmitbtn {
            position: absolute;
            right: 20px;
            bottom: 20px;
        }

        .vit-wr-formsubmitbtn-reset {
            position: absolute;
            right: 35px;
            top: 35px;
        }

        .vit-wr-formsubmitbtn-reset-label {
            position: absolute;
            left: 20px;
            bottom: 10px;
        }

        small {
            font-size: 90%;
            line-height: normal;
        }

        .vit-wr-form-textarea textarea {
            display: flex;
            flex-direction: column;
            margin: 10px 0;
        }

        .vit-wr-content form {
            display: flex;
            justify-content: space-between;
        }

        .full-width {
            width: 100%;
        }

        .content-margin-left {
            margin-left: 90px;
        }

        .input-checkbox {
            display: block;

        }

        .input-checkbox-div {
            display: block;
        }

        .padding-bottom-10 {
            padding-bottom: 10px;
        }

        .uk-notification {
            margin-top: 30px;
            width: 50%;

            margin-left: -25%;
        }

        .uk-badge {
            float: right;
            background: #79C753;
        }

        .free-opacity {
            opacity: 0.7;
        }
        @media screen and (max-width: 500px) {
            .vit-wr-form-textarea textarea {
                max-width: -webkit-fill-available;
            }
            .vit-wr-formsubmitbtn-reset {
                top: 85px;
                left: 35px;
            }
            .vit-wr-wrapper > .uk-grid {
                margin-top: 50px;
            }
        }

        @media screen and (max-width: 960px) {
            .vit-wr-wrapper > .uk-grid {
                display: flex;
                flex-direction: column;
            }

            .vit-wr-wrapper > .uk-grid > .uk-card {
                margin-bottom: 25px;
            }

            .vit-wr-wrapper .vit-wr-content form {
                display: flex;
                flex-direction: column;
            }

            .vit-wr-wrapper .vit-wr-content .content-margin-left {
                margin-left: 0 !important;
            }
        }

        @media screen and (min-width: 960px) and (max-width: 1040px) {
            .vit-wr-wrapper .vit-wr-content .content-margin-left {
                margin-left: 30px;
            }
        }

        .cookieTimer {
            width: 60px;
        }
    </style>
    <div class="vit-wr-wrapper">
        <h2>Settings</h2>
        <form class="uk-text-left form-margin" method="post"
              action="admin.php?page=vit-wr-settings">
            <div class="vit-wr-form-check">
                <button type="submit" class="vit-wr-formsubmitbtn-reset uk-button uk-button-danger"
                        name="submitConfirmationReset"
                        id="submitConfirmationReset"
                        onclick="return confirm('Are you sure you want to reset all settings back to its default? You cannot undo this anymore after confirming.' );">
                    Reset all Settings
                </button>
                <input type="hidden" name="ResetAllSettingsToDefaults"/>

            </div>
        </form>
        <div class="uk-grid uk-grid-small uk-child-width-expand@s uk-text-center">
            <div class="uk-card uk-card-default uk-card-body uk-margin-small-right uk-padding-medium">
                <h4>General options</h4>

                <form class="uk-text-left form-margin" method="post"
                      action="admin.php?page=vit-wr-settings">
                    <input type="hidden" name="generalOptions" value="true">

                    <div class="vit-wr-form-check">
                        <h5>General</h5>
                        <div class="subtext">
                            <div class="vit-wr-form-check">
                                <input type="checkbox" class="vit-wr-form-check-input" name="allowComments" value="true"
                                       id="allowComments" <?php 
echo  ( $this->settings->getAllowComments() == "0" ? "" : "checked" ) ;
?>>
                                <label class="vit-wr-form-check-label" for="allowComments">Allow comments</label>
                                <br/>
                                <small class="form-text uk-text-muted">Allow people to comment while reviewing your
                                    website.
                                    Disable this if you only want
                                    the review stars to show and no text box where they can leave a note.
                                </small>
                            </div>

                            <div class="vit-wr-form-check">
                                <input type="checkbox" class="vit-wr-form-check-input"  name="disableFeedbackLabel" value="true"
                                       id="disableFeedbackLabel" <?php 
echo  ( $this->settings->getDisableFeedbackLabel() == "0" ? "onclick=\"return confirm('Warning: If you enable this options user will not be able to leave any reviews anymore for the time')\"" : "checked" ) ;
?> >
                                <label class="vit-wr-form-check-label uk-text-danger" for="disableFeedbackLabel">Disable the feedback label</label>
                                <br/>
                                <small class="form-text uk-text-muted">Disable the feedback label on your website. Enable
                                    this to take away the feedback label from your website without needing to deactivate
                                    the plugin. It is recommended to leave this option unchecked otherwise visitors can
                                    no longer leave any reviews.
                                </small>
                            </div>

                            <div class="vit-wr-form-check">
                                <input type="checkbox" class="vit-wr-form-check-input"  name="disableOnMobile" value="true"
                                       id="disableOnMobile" <?php 
echo  ( $this->settings->getDisableOnMobile() == "0" ? "" : "checked" ) ;
?> >
                                <label class="vit-wr-form-check-label uk-text-danger" for="disableOnMobile">Disable on mobile</label>
                                <br/>
                                <small class="form-text uk-text-muted">Disables the plugin for mobile users.
                                </small>
                            </div>


                        </div>
                        <h5>Security settings</h5>
                        <div class="uk-margin uk-grid-small uk-child-width-auto uk-grid input-checkbox-div">

                            <label class="input-checkbox"><input class=""
                                                                 type="radio" <?php 
echo  ( $this->settings->getLockSystem() == "0" ? "checked" : "" ) ;
?>
                                                                 name="reviewLockSystem" value="0">No review lock
                                system</label>
                            <small class="form-text uk-text-muted padding-bottom-10">No protection against people from
                                repeatively sending
                                reviews. This option is not recommended.
                            </small>
                            <label class="input-checkbox"><input class="" type="radio"
                                                                 name="reviewLockSystem" <?php 
echo  ( $this->settings->getLockSystem() == "1" ? "checked" : "" ) ;
?>
                                                                 value="1">Session based review lock system</label>
                            <small class="form-text uk-text-muted padding-bottom-10">Prevent people from repetitively
                                sending
                                reviews. When the
                                website visitor closes the internet browser the session also gets closed. This means
                                they
                                can only
                                send another review when they open a new internet browser window.
                            </small>
                            <?php 
?>

                        </div>

                    </div>

                    <button type="submit" class="vit-wr-formsubmitbtn uk-button uk-button-primary" value="1">Apply</button>
                </form>

            </div>


            <div class="uk-card uk-card-default uk-card-body" style="padding: 35px;">
                <h4>Layout options</h4>
                <form class="uk-text-left form-margin" method="post"
                      action="admin.php?page=vit-wr-settings">
                    <input type="hidden" name="layoutOptions" value="true">

                    <h5>Styling</h5>
                    <?php 
?>


                    <div class="vit-wr-form-check color-select subtext">
                        <label class="vit-wr-form-check-label">Review button location</label>
                        <small class="uk-text-muted">This option lets you choose the location of the review button in
                            the
                            screen. (the popup where the customers will be able to give their rating/feedback)
                        </small>
                        <div class="subtext">
                            <select name="buttonLocation">
                                <?php 
foreach ( $this->buttonLocations as $location ) {
    ?>
                                    <option value="<?php 
    echo  $location[0] ;
    ?>" <?php 
    echo  ( $this->settings->getReviewButtonLocation() == $location[0] ? "selected" : "" ) ;
    ?> ><?php 
    echo  $location[1] ;
    ?></option>
                                    <?php 
}
?>

                            </select>
                        </div>
                    </div>
                    <button type="submit" value="1" class="vit-wr-formsubmitbtn uk-button uk-button-primary">Apply</button>
                </form>
            </div>
        </div>
        <?php 
?>
    </div>
