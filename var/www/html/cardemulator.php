<?php
mb_internal_encoding("UTF-8");
include 'ui_mode.php';
$mode = $_GET['mode'] ?? 'main';
$emumode = trim(file_get_contents('/sbin/piforce/emumode.txt'));

echo '<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"><title>WiPi Netbooter - Card Emulator</title>';
echo '<link rel="stylesheet" href="css/modern-theme.css">';
echo '<link rel="stylesheet" href="css/components.css">';
echo '<link rel="stylesheet" href="css/arcade-icons.css">';
echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
echo '<link rel="stylesheet" href="css/arcade-retro.css">';
echo '</head><body>';

echo modern_sliding_sidebar_nav('setup');
echo '<div class="container">';
$modeTitles_bc = [
    'idas'  => 'Initial D',
    'id2'   => 'Initial D ver.2',
    'id3'   => 'Initial D ver.3',
    'wmmt'  => 'Wangan Midnight',
    'mkgp'  => 'Mario Kart GP/GP2',
    'fzero' => 'F-Zero AX',
];
$bc = [['label' => 'Dashboard', 'href' => 'menu.php'], ['label' => 'Card Emulator', 'href' => 'cardemulator.php?mode=main']];
if ($mode !== 'main' && isset($modeTitles_bc[$mode])) {
    $bc[] = ['label' => $modeTitles_bc[$mode]];
} elseif ($mode === 'main') {
    $bc[1] = ['label' => 'Card Emulator']; // current, no href
    array_pop($bc);
}
echo breadcrumb_render($bc);

echo '<h1 class="text-3xl">'.arcade_icon('card').' Card Reader Emulator</h1>';

if ($mode == 'main') {

    echo '<p style="margin-bottom: 24px; color: var(--color-text-secondary);">Select a card reader emulator mode</p>';
    echo '<div class="grid grid-cols-3">';
    $games = [
        ['mode' => 'idas',  'img' => 'initd.png',    'title' => 'Initial D'],
        ['mode' => 'id2',   'img' => 'initd2.png',   'title' => 'Initial D ver.2'],
        ['mode' => 'id3',   'img' => 'initdv3e.png', 'title' => 'Initial D ver.3'],
        ['mode' => 'wmmt',  'img' => 'wmmt.gif',     'title' => 'Wangan Midnight'],
        ['mode' => 'mkgp',  'img' => 'mkgp.png',     'title' => 'Mario Kart GP'],
        ['mode' => 'fzero', 'img' => 'FZ.png',       'title' => 'F-Zero AX'],
    ];
    foreach ($games as $game) {
        echo '<a href="cardemulator.php?mode='.htmlspecialchars($game['mode'], ENT_QUOTES).'" class="card card-interactive" style="text-decoration: none;">';
        echo '<div class="card-body" style="text-align: center;">';
        echo '<img src="images/'.htmlspecialchars($game['img'], ENT_QUOTES).'" style="max-width: 100%; height: auto; display: block; margin: 0 auto;" alt="'.htmlspecialchars($game['title'], ENT_QUOTES).'">';
        echo '<p style="margin-top: 8px; font-weight: 600; color: var(--color-text-primary);">'.htmlspecialchars($game['title'], ENT_QUOTES).'</p>';
        echo '</div></a>';
    }
    echo '</div>';

} else {

    // Mode metadata: [title, slug, card image script, has auto/manual mode]
    $modeInfo = [
        'idas'  => ['Initial D',         'idas',  'idcards.php',  true ],
        'id2'   => ['Initial D ver.2',   'id2',   'idcards.php',  true ],
        'id3'   => ['Initial D ver.3',   'id3',   'idcards.php',  true ],
        'wmmt'  => ['Wangan Midnight',   'wmmt',  'cards.php',    false],
        'mkgp'  => ['Mario Kart GP/GP2', 'mkgp',  'cards.php',    false],
        'fzero' => ['F-Zero AX',         'fzero', 'fzcards.php',  true ],
    ];

    if (!array_key_exists($mode, $modeInfo)) {
        header('Location: cardemulator.php?mode=main');
        exit;
    }

    [$modeTitle, $modeSlug, $cardScript, $hasAutoMode] = $modeInfo[$mode];
    $launchmode = (!$hasAutoMode) ? 'manual' : $emumode;
    $cardDir = '/boot/config/cards/'.$modeSlug.'/';
    $files = is_dir($cardDir) ? array_values(array_diff(scandir($cardDir), ['.', '..'])) : [];

    // Collect valid cards
    $validCards = [];
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== '') continue;
        if ($hasAutoMode && !file_exists('cards/'.$modeSlug.'/'.$file.'.printdata.php')) continue;
        $validCards[] = $file;
    }

    // Back button + title card
    echo '<a href="cardemulator.php?mode=main" class="btn btn-secondary" style="margin-bottom: 20px; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">&#8592; Back</a>';
    echo '<div class="card" style="margin-bottom: 24px;">';
    echo '<div class="card-body">';
    echo '<div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 8px;">';
    echo '<h2 style="margin: 0;">'.htmlspecialchars($modeTitle).'</h2>';
    echo '<span class="badge badge-primary">'.htmlspecialchars($emumode).'</span>';
    echo '</div>';
    if ($hasAutoMode && $emumode === 'auto') {
        echo '<p style="color: var(--color-text-secondary); margin: 0;">Select a saved card or purchase a new one in the game.</p>';
    } else {
        echo '<p style="color: var(--color-text-secondary); margin: 0;">Select a saved card or create a new one below. The emulator stays running until a new card is launched or the Pi powers off.</p>';
    }
    echo '</div></div>';

    // Card grid
    if (count($validCards) > 0) {
        echo '<div class="grid grid-cols-3" style="margin-bottom: 24px;">';
        foreach ($validCards as $card) {
            $cardEnc = urlencode($card);
            $cardSafe = htmlspecialchars($card, ENT_QUOTES, 'UTF-8');
            echo '<a href="launchcard.php?card='.$cardEnc.'&mode='.$modeSlug.'&launchmode='.$launchmode.'" class="card card-interactive" style="text-decoration: none;">';
            echo '<div class="card-body" style="text-align: center;">';
            echo '<img src="'.$cardScript.'?name='.$cardEnc.'&amp;mode='.$modeSlug.'" style="max-width: 100%; height: auto; display: block; margin: 0 auto;" alt="'.$cardSafe.'">';
            echo '<p style="margin-top: 8px; font-weight: 600; color: var(--color-text-primary); word-break: break-all;">'.$cardSafe.'</p>';
            echo '</div></a>';
        }
        echo '</div>';
    } else {
        echo '<div class="empty-state" style="margin-bottom: 24px;">';
        echo '<span class="empty-state__icon arcade-icon arcade-icon--card" aria-hidden="true"></span>';
        echo '<h3>No Cards Found</h3>';
        echo '<p>No saved cards yet. Create a new one using the form below.</p>';
        echo '</div>';
    }

    // Create new card form (shown unless auto mode with no manual option)
    if (!$hasAutoMode || $emumode === 'manual') {
        echo '<div class="card" style="max-width: 480px;">';
        echo '<div class="card-header"><h3 class="card-title">Create New Card</h3></div>';
        echo '<form action="launchcard.php" method="get">';
        echo '<div class="card-body">';
        echo '<div style="margin-bottom: 16px;">';
        echo '<label class="form-label" for="cardname">Card Name</label>';
        echo '<input id="cardname" type="text" name="card" class="form-input" placeholder="Enter card name" required>';
        echo '</div>';
        echo '<input type="hidden" name="mode" value="'.htmlspecialchars($modeSlug, ENT_QUOTES).'">';
        echo '<input type="hidden" name="launchmode" value="'.htmlspecialchars($launchmode, ENT_QUOTES).'">';
        echo '</div>';
        echo '<div class="card-footer">';
        echo '<button type="submit" class="btn btn-primary">Create &amp; Launch</button>';
        echo '</div>';
        echo '</form></div>';
    }
}

echo '</div></div>';
echo '<script>function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}</script>';
echo '</body></html>';
?>
