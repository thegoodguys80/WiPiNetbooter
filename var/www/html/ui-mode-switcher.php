<?php
include 'ui_mode.php';

// Handle mode switch
if (isset($_POST['mode'])) {
    $new_mode = $_POST['mode'];
    if (set_ui_mode($new_mode)) {
        header('Location: ui-mode-switcher.php?success=1');
        exit;
    }
}

$current_mode = get_ui_mode();
$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Mode Switcher - WiPi Netbooter</title>
    <?php load_ui_styles(); ?>
</head>
<body<?php if ($current_mode === 'modern') echo ' class="kiosk-mode"'; ?>>

<?php if ($current_mode === 'classic'): ?>
    <?php include 'menu_include.php'; ?>
<?php endif; ?>

<section><center>
    <h1><a href="setup.php">User Interface Mode</a><?php ui_mode_indicator(); ?></h1>
    <br>
    
    <?php if ($success): ?>
        <div style="padding: 16px; background-color: #D1FAE5; color: #059669; border-radius: 8px; margin-bottom: 24px; max-width: 600px;">
            <strong>✓ Success!</strong> UI mode has been changed. The new mode will take effect immediately.
        </div>
    <?php endif; ?>
    
    <div style="max-width: 800px; text-align: left; margin: 0 auto;">
        <p style="margin-bottom: 24px; color: #64748B;">
            Choose between the original Classic UI or the new Modern UI with enhanced touchscreen support and improved visuals.
        </p>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
            <!-- Classic Mode Card -->
            <div style="padding: 24px; background-color: <?php echo $current_mode === 'classic' ? '#DBEAFE' : '#F8FAFC'; ?>; border: 2px solid <?php echo $current_mode === 'classic' ? '#2563EB' : '#E2E8F0'; ?>; border-radius: 12px; position: relative;">
                <?php if ($current_mode === 'classic'): ?>
                    <div style="position: absolute; top: 12px; right: 12px; background-color: #2563EB; color: white; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                        ACTIVE
                    </div>
                <?php endif; ?>
                
                <h3 style="margin-top: 0; margin-bottom: 12px; color: #0F172A;">Classic UI</h3>
                <p style="color: #64748B; font-size: 14px; line-height: 1.5; margin-bottom: 16px;">
                    The original WiPiNetbooter interface. Proven and reliable design that you're familiar with.
                </p>
                
                <ul style="list-style: none; padding: 0; margin-bottom: 20px; color: #64748B; font-size: 14px;">
                    <li style="margin-bottom: 8px;">✓ Original layout and styling</li>
                    <li style="margin-bottom: 8px;">✓ Familiar navigation</li>
                    <li style="margin-bottom: 8px;">✓ Lightweight and fast</li>
                    <li style="margin-bottom: 8px;">✓ Stable and tested</li>
                </ul>
                
                <?php if ($current_mode !== 'classic'): ?>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="mode" value="classic">
                        <button type="submit" style="width: 100%; padding: 12px; background-color: #4C9CF1; color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                            Switch to Classic
                        </button>
                    </form>
                <?php else: ?>
                    <button disabled style="width: 100%; padding: 12px; background-color: #E2E8F0; color: #94A3B8; border: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                        Currently Active
                    </button>
                <?php endif; ?>
            </div>
            
            <!-- Modern Mode Card -->
            <div style="padding: 24px; background-color: <?php echo $current_mode === 'modern' ? '#DBEAFE' : '#F8FAFC'; ?>; border: 2px solid <?php echo $current_mode === 'modern' ? '#2563EB' : '#E2E8F0'; ?>; border-radius: 12px; position: relative;">
                <?php if ($current_mode === 'modern'): ?>
                    <div style="position: absolute; top: 12px; right: 12px; background-color: #2563EB; color: white; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                        ACTIVE
                    </div>
                <?php endif; ?>
                
                <div style="background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 600; display: inline-block; margin-bottom: 12px;">
                    NEW
                </div>
                
                <h3 style="margin-top: 0; margin-bottom: 12px; color: #0F172A;">Modern UI</h3>
                <p style="color: #64748B; font-size: 14px; line-height: 1.5; margin-bottom: 16px;">
                    Enhanced interface with modern design, improved touchscreen support, and better visuals for arcade cabinets.
                </p>
                
                <ul style="list-style: none; padding: 0; margin-bottom: 20px; color: #64748B; font-size: 14px;">
                    <li style="margin-bottom: 8px;">✨ Modern card-based design</li>
                    <li style="margin-bottom: 8px;">📱 Touchscreen optimized (48px targets)</li>
                    <li style="margin-bottom: 8px;">🎨 Enhanced game cards with overlays</li>
                    <li style="margin-bottom: 8px;">🌓 Dark mode support</li>
                    <li style="margin-bottom: 8px;">📐 LCD screen size profiles</li>
                    <li style="margin-bottom: 8px;">🎮 Kiosk mode for arcade cabinets</li>
                </ul>
                
                <?php if ($current_mode !== 'modern'): ?>
                    <form method="POST" style="margin: 0;">
                        <input type="hidden" name="mode" value="modern">
                        <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #3B82F6 0%, #2563EB 100%); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer;">
                            Switch to Modern
                        </button>
                    </form>
                <?php else: ?>
                    <button disabled style="width: 100%; padding: 12px; background-color: #E2E8F0; color: #94A3B8; border: none; border-radius: 8px; font-size: 16px; font-weight: 600;">
                        Currently Active
                    </button>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="padding: 16px; background-color: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 8px; margin-bottom: 24px;">
            <strong style="color: #D97706;">⚠️ Note:</strong>
            <span style="color: #D97706;"> You can switch between modes at any time. All your settings, games, and configurations remain unchanged.</span>
        </div>
        
        <div style="text-align: center; margin-top: 32px;">
            <a href="setup.php" style="display: inline-block; padding: 12px 24px; background-color: #64748B; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;">
                ← Back to Setup
            </a>
        </div>
    </div>

</center></section>

<?php if ($current_mode === 'modern'): ?>
    <!-- Modern UI Bottom Nav -->
    <nav class="kiosk-nav">
        <a href="gamelist.php?display=all" class="kiosk-nav-item">
            <span class="kiosk-nav-icon">🎮</span>
            <span class="kiosk-nav-label">Games</span>
        </a>
        <a href="dimms.php" class="kiosk-nav-item">
            <span class="kiosk-nav-icon">🔌</span>
            <span class="kiosk-nav-label">NetDIMMs</span>
        </a>
        <a href="setup.php" class="kiosk-nav-item active">
            <span class="kiosk-nav-icon">⚙️</span>
            <span class="kiosk-nav-label">Setup</span>
        </a>
        <a href="options.php" class="kiosk-nav-item">
            <span class="kiosk-nav-icon">🎛️</span>
            <span class="kiosk-nav-label">Options</span>
        </a>
    </nav>
<?php endif; ?>

</body>
</html>
