<?php
include 'ui_mode.php';

$path = '/boot/roms/';
$scan = is_dir($path) ? scandir($path) : [];
$files = array_values(array_diff($scan, ['.', '..']));

/** Normalize for filter data-* / option matching */
function editgamelist_filter_key($s) {
    return strtolower(trim((string) $s));
}

$games = [];
    $f = fopen('csv/romsinfo.csv', 'r');
    if ($f !== false) {
        $header = fgetcsv($f);
        while (($row = fgetcsv($f)) !== false) {
            if (isset($row[12], $row[1]) && in_array($row[1], $files, true)) {
                $games[] = $row;
            }
        }
        fclose($f);
    }

    $systems = [];
    $genres = [];
    $orientations = [];
    $controlses = [];
    foreach ($games as $row) {
        $systems[] = $row[0];
        $genres[] = $row[8];
        $orientations[] = $row[10];
        $controlses[] = $row[11];
    }
    $systems = array_unique($systems);
    $genres = array_unique($genres);
    $orientations = array_unique($orientations);
    $controlses = array_unique($controlses);
    sort($systems, SORT_NATURAL | SORT_FLAG_CASE);
    sort($genres, SORT_NATURAL | SORT_FLAG_CASE);
    sort($orientations, SORT_NATURAL | SORT_FLAG_CASE);
    sort($controlses, SORT_NATURAL | SORT_FLAG_CASE);

    echo '<html lang="en"><head><meta charset="utf-8"><title>WiPi Netbooter - Edit Games</title>';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<link rel="stylesheet" href="css/modern-theme.css">';
    echo '<link rel="stylesheet" href="css/components.css">';
    echo '<link rel="stylesheet" href="css/arcade-icons.css">';
    echo '<link rel="stylesheet" href="css/kiosk-mode.css">';
    echo '<link rel="stylesheet" href="css/arcade-retro.css">';
    echo '</head><body>';

    echo modern_sliding_sidebar_nav('setup');
    echo '<div class="container p-6">';

    echo '<h1 class="text-3xl">' . arcade_icon('edit') . ' Edit Game List</h1>';
    echo '<p class="page-intro" style="margin-bottom: 16px;">Show or hide games from the main game list. Use filters to find duplicates or groups.</p>';

    echo '<div class="filter-toolbar edit-game-toolbar" style="margin-bottom: 24px; flex-wrap: wrap; gap: 10px;">';
    echo '<span class="filter-toolbar__label">Filter by</span>';

    echo '<select id="editStatusFilter" class="form-select form-select--compact" title="Enabled state">';
    echo '<option value="all">All statuses</option>';
    echo '<option value="enabled">Enabled only</option>';
    echo '<option value="disabled">Disabled only</option>';
    echo '</select>';

    echo '<select id="editSystemFilter" class="form-select form-select--compact">';
    echo '<option value="all">All systems</option>';
    foreach ($systems as $s) {
        $k = editgamelist_filter_key($s);
        echo '<option value="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($s, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';

    echo '<select id="editGenreFilter" class="form-select form-select--compact">';
    echo '<option value="all">All genres</option>';
    foreach ($genres as $g) {
        $k = editgamelist_filter_key($g);
        echo '<option value="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($g, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';

    echo '<select id="editOrientationFilter" class="form-select form-select--compact">';
    echo '<option value="all">All orientations</option>';
    foreach ($orientations as $o) {
        $k = editgamelist_filter_key($o);
        echo '<option value="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($o, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';

    echo '<select id="editControlsFilter" class="form-select form-select--compact">';
    echo '<option value="all">All controls</option>';
    foreach ($controlses as $c) {
        $k = editgamelist_filter_key($c);
        echo '<option value="' . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';

    echo '<button type="button" class="btn btn-secondary btn-sm" onclick="resetEditGameFilters()">Reset filters</button>';
    echo '</div>';

    echo '<form id="bulkEditForm" method="post" action="updatecsvenable_bulk.php" style="margin-bottom: 20px;">';
    echo '<input type="hidden" name="bulk_action" id="bulkActionField" value="">';
    echo '<div class="filter-toolbar edit-bulk-toolbar" style="flex-wrap: wrap; gap: 10px; align-items: center;">';
    echo '<span class="filter-toolbar__label">Bulk</span>';
    echo '<button type="button" class="btn btn-secondary btn-sm" onclick="selectVisibleEditGames()">Select filtered</button>';
    echo '<button type="button" class="btn btn-secondary btn-sm" onclick="clearEditGameSelection()">Clear selection</button>';
    echo '<button type="button" class="btn btn-success btn-sm" onclick="runBulkEnableDisable(\'enable\')">' . arcade_icon('lightning') . ' Enable selected</button>';
    echo '<button type="button" class="btn btn-warning btn-sm" onclick="runBulkEnableDisable(\'disable\')">○ Disable selected</button>';
    echo '</div>';

    echo '<div id="editGameGrid" class="grid grid-cols-3" style="grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 20px;">';

    foreach ($games as $row) {
        $toggle = ($row[12] == 'Yes') ? 'No' : 'Yes';
        $isEnabled = ($row[12] == 'Yes');
        $ds = htmlspecialchars(editgamelist_filter_key($row[0]), ENT_QUOTES, 'UTF-8');
        $dg = htmlspecialchars(editgamelist_filter_key($row[8]), ENT_QUOTES, 'UTF-8');
        $dor = htmlspecialchars(editgamelist_filter_key($row[10]), ENT_QUOTES, 'UTF-8');
        $dc = htmlspecialchars(editgamelist_filter_key($row[11]), ENT_QUOTES, 'UTF-8');
        $den = $isEnabled ? 'enabled' : 'disabled';

        echo '<div class="card edit-game-card" data-system="' . $ds . '" data-genre="' . $dg . '" data-orientation="' . $dor . '" data-controls="' . $dc . '" data-enabled="' . $den . '">';
        echo '<div class="card-header" style="display:flex;align-items:flex-start;gap:10px;">';
        echo '<input type="checkbox" class="edit-game-select" name="roms[]" value="' . htmlspecialchars($row[1], ENT_QUOTES, 'UTF-8') . '" title="Select for bulk" style="width:18px;height:18px;margin-top:4px;flex-shrink:0;accent-color:var(--arcade-cyan,#00d4ff);">';
        echo '<h3 class="card-title" style="flex:1;margin:0;">' . htmlspecialchars($row[4], ENT_QUOTES, 'UTF-8') . '</h3>';
        echo '</div>';
        echo '<div class="card-body">';
        echo '<p class="text-sm" style="margin: 0 0 6px;"><strong>System:</strong> ' . htmlspecialchars($row[0], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p class="text-sm" style="margin: 0 0 6px;"><strong>Genre:</strong> ' . htmlspecialchars($row[8], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p class="text-sm" style="margin: 0 0 6px;"><strong>Orientation:</strong> ' . htmlspecialchars($row[10], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p class="text-sm" style="margin: 0 0 10px;"><strong>Controls:</strong> ' . htmlspecialchars($row[11], ENT_QUOTES, 'UTF-8') . '</p>';

        if ($isEnabled) {
            echo '<span class="badge badge-success">✓ Enabled</span>';
        } else {
            echo '<span class="badge badge-secondary">○ Disabled</span>';
        }

        echo '<div style="margin-top: 16px;">';
        if ($isEnabled) {
            echo '<a href="updatecsvenable.php?rom=' . urlencode($row[1]) . '&enabled=' . urlencode($toggle) . '" class="btn btn-warning btn-sm">○ Disable</a>';
        } else {
            echo '<a href="updatecsvenable.php?rom=' . urlencode($row[1]) . '&enabled=' . urlencode($toggle) . '" class="btn btn-primary btn-sm">✓ Enable</a>';
        }
        echo '</div>';
        echo '</div></div>';
    }

    echo '</div>'; // grid
    echo '</form>';

    echo '</div>'; // container

    echo '<script>';
    echo 'function toggleSidebar(){const s=document.getElementById("sidebarNav"),o=document.getElementById("sidebarOverlay"),b=document.getElementById("burgerBtn");if(s)s.classList.toggle("open");if(o)o.classList.toggle("show");if(b)b.classList.toggle("open");}';
    echo 'function applyEditGameFilters(){';
    echo 'var st=document.getElementById("editStatusFilter").value;';
    echo 'var sy=document.getElementById("editSystemFilter").value;';
    echo 'var g=document.getElementById("editGenreFilter").value;';
    echo 'var o=document.getElementById("editOrientationFilter").value;';
    echo 'var c=document.getElementById("editControlsFilter").value;';
    echo 'document.querySelectorAll(".edit-game-card").forEach(function(card){';
    echo 'var okSt=st==="all"||card.dataset.enabled===st;';
    echo 'var okSy=sy==="all"||card.dataset.system===sy;';
    echo 'var okG=g==="all"||card.dataset.genre===g;';
    echo 'var okO=o==="all"||card.dataset.orientation===o;';
    echo 'var okC=c==="all"||card.dataset.controls===c;';
    echo 'var vis=okSt&&okSy&&okG&&okO&&okC;';
    echo 'card.style.display=vis?"":"none";';
    echo 'var cb=card.querySelector(".edit-game-select");';
    echo 'if(cb){cb.disabled=!vis;if(!vis)cb.checked=false;}';
    echo '});}';
    echo 'function resetEditGameFilters(){';
    echo '["editStatusFilter","editSystemFilter","editGenreFilter","editOrientationFilter","editControlsFilter"].forEach(function(id){document.getElementById(id).value="all";});';
    echo 'applyEditGameFilters();}';
    echo 'function selectVisibleEditGames(){';
    echo 'document.querySelectorAll(".edit-game-card").forEach(function(card){';
    echo 'if(card.style.display==="none")return;';
    echo 'var cb=card.querySelector(".edit-game-select");';
    echo 'if(cb&&!cb.disabled)cb.checked=true;';
    echo '});}';
    echo 'function clearEditGameSelection(){';
    echo 'document.querySelectorAll(".edit-game-select").forEach(function(cb){cb.checked=false;});';
    echo '}';
    echo 'function runBulkEnableDisable(action){';
    echo 'var n=document.querySelectorAll("#bulkEditForm input.edit-game-select:checked:not(:disabled)").length;';
    echo 'if(n===0){alert("Select at least one visible game (checkboxes).");return;}';
    echo 'var verb=action==="enable"?"Enable":"Disable";';
    echo 'if(!confirm(verb+" "+n+" game(s) in romsinfo?"))return;';
    echo 'document.getElementById("bulkActionField").value=action;';
    echo 'document.getElementById("bulkEditForm").submit();';
    echo '}';
    echo '["editStatusFilter","editSystemFilter","editGenreFilter","editOrientationFilter","editControlsFilter"].forEach(function(id){';
    echo 'var el=document.getElementById(id);if(el)el.addEventListener("change",applyEditGameFilters);});';
    echo '</script>';
    echo '</body></html>';
