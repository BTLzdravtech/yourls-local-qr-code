<?php
/*
Plugin Name: QR Code Short URLS SVG Local
Plugin URI:
Description: Add .qr to shorturls to display QR Code in SVG on the your site
Version: 1.0
Author: Alex Kolodko
Author URI: https://alexkolodko.com/
*/

// Kick in if the loader does not recognize a valid pattern
yourls_add_action('redirect_keyword_not_found', 'alexk_yourls_qrcode', 1);

function alexk_yourls_qrcode( $request ) {
        // Get authorized charset in keywords and make a regexp pattern
        $pattern = yourls_make_regexp_pattern( yourls_get_shorturl_charset() );

        // Shorturl is like bleh.qr?
        if( preg_match( "@^([$pattern]+)\.qr?/?$@", $request[0], $matches ) ) {
                // this shorturl exists?
                $keyword = yourls_sanitize_keyword( $matches[1] );
                if( yourls_is_shorturl( $keyword ) ) {
                        // Show the QR code then!
                        header('Location: '.YOURLS_SITE.'/qr?content='.YOURLS_SITE.'/'.$keyword);
                        // header('Location: https://alexkolodko.com?link='.YOURLS_SITE.'/'.$keyword);
                        exit;
                }
        }
}

yourls_add_filter( 'action_links', 'alexk_add_qrcode_button' );
function alexk_add_qrcode_button( $action_links, $keyword, $url, $ip, $clicks, $timestamp ) {
    $surl = yourls_link( $keyword );
    $id = yourls_string2htmlid( $keyword ); // used as HTML #id

    // We're adding .qr to the end of the URL, right?
    $qr = '.qr';
    $qrlink = $surl . $qr;

    // Define the QR Code
    $qrcode = array(
        'href'    => $qrlink,
        'id'      => "qrlink-$id",
        'title'   => 'QR Code',
        'anchor'  => 'QR Code'
    );

    // Add our QR code generator button to the action links list
    $action_links .= sprintf( '<a href="%s" id="%s" title="%s" class="%s">%s</a>',
        $qrlink, $qrcode['id'], $qrcode['title'], 'button button_qrcode', $qrcode['anchor']
    );

    return $action_links;
}

// Add the CSS and some extra javascript to <head>
yourls_add_action( 'html_head', 'alexk_add_qrcode_css_head' );
function alexk_add_qrcode_css_head( $context ) {
    // expose what page we are on
    foreach($context as $k):
        // If we are on the index page, use this css code for the button
        if( $k == 'index' ):
            ?>
            <style type="text/css">
                td.actions .button_qrcode {
                    background: white url(data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAABwAAAAcCAQAAADYBBcfAAAAIGNIUk0AAHomAACAhAAA+gAAAIDoAAB1MAAA6mAAADqYAAAXcJy6UTwAAAACYktHRAD/h4/MvwAAAAlwSFlzAAAHRAAAB0QBKlH81AAAAAd0SU1FB+kBDgwHGpsZKLAAAAOhSURBVDjLddVraJZlGAfw3/u8j5vS1KWuuaXzCJppqZkSBnnWBJPMDzUxoRZBGkKHD2lGlGCeoBKSTDM1hGFpYTg8RPOwxHkYmYYfXJ6WTqfOedpe3+15+uDrlKDr/nRf/K/74n9x/f83kVuutp4Gl72FpA66eE3KecVKXNbwAOqWKNRoi3LNIFBolgTyleitr2tWOmC8euucF4HQKC8GUsptl3JHyjGlboBOpuuvxmrfqpZ0Q6ljGdR25VIhmj1rI2LrLXcvmn1phzxXMve2ZpspgWLNBCABYvcjQmCEBQofyMb30UEmEYtEGQ5pz1mkuxZ/+c07RktnHotE4rvlIQLV1iOyTxoJzWpt08VI+capEiFtn1iAagMJJRXam+GWViBL0lFLXfOFMW7rKBbIUqBCJWhSKBlKm2VaK4ss2W563gyLNdml1CcCTbKtcKcV1V469JGEFs1oIxC7abcxhnpPhSMuiSTs8akcLWJt7o3p7jx7GC+hTI1sU+UYa6wKC101RZF6da4rM1hS+b2moYGajVQi0OiIPDM8okCdJeq9ZLr3FZvrgENGyXPWBY13S6sct8oQg6xx1A7jDLDQQcMscc5RI3ztR8Nl6eYNmzyBQJgU6WuEJvtMlW2zp5wTmGKwUbqrtVmVvaq0uK6Pdw10Ui/zAj84CZISiIWGylXlaeeU2m+Qeodlm+xlI+UYo4dmLUmD9XfaKv8YZoBCm73gIcsU+Fmpi6Zr55TONpgk3005ttqjOuk729Uqdky5Xxw2xwjHbfO7qeYossGT+jllknWWOqG3Q7pakBTZq1q94+rUqZVSocVgB0yRq8zjGux22hnl2pmoXF+POhhqayJavCJA7KadxpsvVx8n7LLWKhVyBBL6m2CGIdgY+kx9Rrz3Vo4GTV53yx/y1WijmyIfWOeW8/JU3rWONtYp1bZ1yVfIUarB575xzWxfmWw/uuplq8Xe1s8mK0MtznvY7IysKtyRkFKjSa1Kl/xpmm6gs/YOWyNProUhIn3MFIvEKhHpZqhKXQ13tVXe0NMESY3ytAszZpCQaPWDtEnmWuQZrzprg3zXXNDTRYF5YoFN3gz/6yUZkygw3zJrFSox2k5bfOx720RiCXUaQ4R+VSwhVq0JoTKzhXar0aSHnyx3W8rfDipSokyRD0PZRpEx5IEKtZd0xmVjTRbrrqNAljqb5RnrpEgCaf/zBTxmnwZXNEhLWaqTfKutldBBliy5/wIn42jyUwXfsgAAACV0RVh0ZGF0ZTpjcmVhdGUAMjAyNS0wMS0xNFQxMjowNzoyMCswMDowMJ7K3pcAAAAldEVYdGRhdGU6bW9kaWZ5ADIwMjUtMDEtMTRUMTI6MDc6MjArMDA6MDDvl2YrAAAAKHRFWHRkYXRlOnRpbWVzdGFtcAAyMDI1LTAxLTE0VDEyOjA3OjI2KzAwOjAw21JyzgAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAAASUVORK5CYII=) center;
                    border-color: white;
                    padding: 2px;
                    border-radius: 2px;
                }
                }
            </style>
        <?php
        endif;
    endforeach;
}
?>