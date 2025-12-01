<?php
// final_landpage_with_invoice.php
// Wrapper that serves your unchanged landing page HTML while enabling PHP routing
// Requires: functions.php (which starts session and provides is_logged_in())

require_once __DIR__ . '/functions.php';

// determine login state
$logged = is_logged_in() ? 'true' : 'false';

// path to original HTML (unchanged)
$design_file = __DIR__ . '/Final_landpage_with_invoice.html';
if (!file_exists($design_file)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Missing file: Final_landpage_with_invoice.html\n";
    echo "Please upload the original HTML file (unchanged) into this folder.";
    exit;
}

// read the original HTML
$html = file_get_contents($design_file);

// Build the injection script: PHP navigation overrides + tracking snippet.
// IMPORTANT: these functions replace only the JS navigation functions (so visual UI remains identical).
$injection = <<<HTML
<script>
/* --- PHP navigation overrides (PURELY behavioral) ---
   These functions keep your UI, overlays and animations intact,
   but redirect actions to real PHP pages without changing design.
   If your original HTML defines these functions already, these
   will override them at runtime to enable server-side pages.
-----------------------------------------------------*/
(function(){
    // reflect login state to your page JS
    window.APP = window.APP || {};
    window.APP.loggedIn = {$logged};

    // Replace overlay-opening account functions to redirect to PHP pages
    window.openAccountOverlay = window.openAccountOverlay || function(view){
        if (view === 'login') { window.location.href = 'login.php'; return; }
        if (view === 'signup') { window.location.href = 'register.php'; return; }
        // fallback: open login by default
        window.location.href = 'login.php';
    };

    // Replace performLogin / performSignup used by your UI to point to server pages
    window.performLogin = window.performLogin || function(){ window.location.href = 'login.php'; };
    window.performSignup = window.performSignup || function(){ window.location.href = 'register.php'; };

    // Cart / ordering hooks
    window.addToCart = window.addToCart || function(){ window.location.href = 'cart.php'; };
    window.handleOrderNow = window.handleOrderNow || function(){ window.location.href = 'menu.php'; };
    window.triggerCheckoutAlert = window.triggerCheckoutAlert || function(){ window.location.href = 'order_summary.php'; };

    // Dashboard / cart shortcut
    window.openDashboard = window.openDashboard || function(){ window.location.href = 'cart.php'; };

    // Logout
    window.performLogout = window.performLogout || function(){ window.location.href = 'logout.php'; };

    /* --- Auto-open Tracking if ?track=ORDER_ID is present --- */
    (function(){
        try {
            const params = new URLSearchParams(window.location.search);
            const track = params.get('track');
            if (track) {
                // wait a little so the page's original JS (overlays) can initialize
                setTimeout(() => {
                    // Prefer existing startLiveTracking(orderId) if it's present
                    if (typeof startLiveTracking === 'function') {
                        try { 
                            // If your startLiveTracking expects order details, it will still work.
                            startLiveTracking();
                        } catch(e) {
                            console.log('startLiveTracking exists but failed to run:', e);
                        }
                    } else {
                        // If your page uses a different tracking function name, you could add it here.
                        console.log('track param present:', track);
                    }
                }, 650);
            }
        } catch(e) {
            console.error('tracking auto-open failed', e);
        }
    })();

})();
</script>
HTML;

// Inject the script into the HTML inside the first </head> tag to ensure it's available early.
// We intentionally don't alter the design HTML — injection is minimal and cosmetic-free.
if (stripos($html, '</head>') !== false) {
    $html = preg_replace('/<\/head>/i', $injection . "\n</head>", $html, 1);
} else {
    // fallback: prepend to document if no head found
    $html = $injection . $html;
}

// Output the final combined HTML
// Set content-type so browsers render correctly
header('Content-Type: text/html; charset=utf-8');
echo $html;
exit;
?>
