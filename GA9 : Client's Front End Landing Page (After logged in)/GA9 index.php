<?php


require_once __DIR__ . '/functions.php';


$logged = is_logged_in() ? 'true' : 'false';


$design_file = __DIR__ . '/Final_landpage_with_invoice.html';
if (!file_exists($design_file)) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "Missing file: Final_landpage_with_invoice.html\n";
    echo "Please upload the original HTML file (unchanged) into this folder.";
    exit;
}


$html = file_get_contents($design_file);


$injection = <<<HTML
<script>

(function(){
    
    window.APP = window.APP || {};
    window.APP.loggedIn = {$logged};

    
    window.openAccountOverlay = window.openAccountOverlay || function(view){
        if (view === 'login') { window.location.href = 'login.php'; return; }
        if (view === 'signup') { window.location.href = 'register.php'; return; }
        
        window.location.href = 'login.php';
    };

    
    window.performLogin = window.performLogin || function(){ window.location.href = 'login.php'; };
    window.performSignup = window.performSignup || function(){ window.location.href = 'register.php'; };

    
    window.addToCart = window.addToCart || function(){ window.location.href = 'cart.php'; };
    window.handleOrderNow = window.handleOrderNow || function(){ window.location.href = 'menu.php'; };
    window.triggerCheckoutAlert = window.triggerCheckoutAlert || function(){ window.location.href = 'order_summary.php'; };

    
    window.openDashboard = window.openDashboard || function(){ window.location.href = 'cart.php'; };

    
    window.performLogout = window.performLogout || function(){ window.location.href = 'logout.php'; };

    
    (function(){
        try {
            const params = new URLSearchParams(window.location.search);
            const track = params.get('track');
            if (track) {
                
                setTimeout(() => {
                    
                    if (typeof startLiveTracking === 'function') {
                        try { 
                            
                            startLiveTracking();
                        } catch(e) {
                            console.log('startLiveTracking exists but failed to run:', e);
                        }
                    } else {
                        
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


if (stripos($html, '</head>') !== false) {
    $html = preg_replace('/<\/head>/i', $injection . "\n</head>", $html, 1);
} else {
    
    $html = $injection . $html;
}


header('Content-Type: text/html; charset=utf-8');
echo $html;
exit;
?>
