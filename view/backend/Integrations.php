<?php
/*
 *
 * License: GNU General Public License v3 (GPL-3)
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * File Description: This is the view for the admin page "Settings"
 * */

?>

<!doctype html>
<html>
<head>
    <title>Dashboard Page</title>
    <meta charset="UTF-8" name="Dashboard" content="Dashboard page:">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="<?= plugin_dir_url(dirname(__FILE__)) . '../view/css/dashboard-custom-style.css' ?>">
</head>

<body>

<div class="vit-wr-wrapper">
    <div class="uk-grid uk-grid-small uk-child-width-expand@m">
        <div class="uk-card uk-card-body uk-margin-small-right scaled-sm">
            <header>
                <!-- The beginning of the first row -->
                <div class="header premium">

                    <a href="http://www.vincoit.com" target="_blank" class="vit-logo"><img src="<?= plugin_dir_url(dirname(__FILE__)) . '../view/images/logo.png' ?>"></a>

                    <h2>Integrations:</h2>
                        <p class="card-info">
                            Welcome to the Vit Website Reviews Integrations page! <br>
                            Use this page to embedd your own unique VIT shortcode to apply it on any page. <br>
                        </p>

                </div>
            </header>
        </div>

        <div class="uk-card uk-card-default uk-card-body scaled-sm">
            <h4 class="card-header">Shortcode Integrations: </h4>
            <p class="card-info">
                To display the popup on one of your pages, please use the following shortcode:
                <br><br>
            <b>[vit_wr_popup]</b>
            </p>
            <br>
            <form class="uk-text-left form-margin" method="post"
                  action="admin.php?page=vit-wr-integrations">


                <div class="vit-wr-form-check">
                    <input type="checkbox" class="vit-wr-form-check-input" name="global_shortcodes" value="true"
                           id="global_shortcodes" <?= ($this->ig_settings == "0") ? "" : "checked" ?>>
                    <label class="vit-wr-form-check-label" for="global_shortcodes">Enable shortcode globally
                        </label>
                    <br/>
                    <small class="form-text uk-text-muted">This will enable the shortcode on all pages of your website.
                    </small>
                </div>
                <br>
                <button type="submit" class="vit-wr-formsubmitbtn uk-button uk-button-primary" name="integration_settings" value="1">Apply</button>
            </form>
        </div>
    </div>
</div>

<!--    Text apart in een div:   -->
</body>
</html>




