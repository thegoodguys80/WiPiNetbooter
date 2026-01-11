<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UI Component Showcase - WiPiNetbooter</title>
    <link rel="stylesheet" href="css/modern-theme.css">
    <link rel="stylesheet" href="css/components.css">
    <style>
        /* Demo-specific styles */
        .demo-section {
            margin-bottom: var(--space-12);
        }
        .demo-section-title {
            font-size: var(--font-size-2xl);
            margin-bottom: var(--space-6);
            padding-bottom: var(--space-3);
            border-bottom: 2px solid var(--color-border);
        }
        .component-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-4);
            margin-top: var(--space-4);
        }
    </style>
</head>
<body>
    <!-- Theme Toggle -->
    <div style="position: fixed; top: 16px; right: 16px; z-index: 9999;">
        <button class="btn btn-ghost btn-sm" onclick="toggleTheme()">
            🌓 Toggle Dark Mode
        </button>
    </div>

    <div class="container" style="padding-top: var(--space-8); padding-bottom: var(--space-16);">
        <h1 style="text-align: center; margin-bottom: var(--space-2);">WiPiNetbooter UI Components</h1>
        <p class="text-center text-secondary" style="margin-bottom: var(--space-12);">Modern theme and component showcase</p>

        <!-- Colors -->
        <section class="demo-section">
            <h2 class="demo-section-title">Colors</h2>
            <div class="grid grid-cols-4 gap-4">
                <div class="card card-compact">
                    <div style="width: 100%; height: 60px; background-color: var(--color-primary); border-radius: var(--radius-md); margin-bottom: var(--space-2);"></div>
                    <div class="text-sm text-secondary">Primary</div>
                </div>
                <div class="card card-compact">
                    <div style="width: 100%; height: 60px; background-color: var(--color-success); border-radius: var(--radius-md); margin-bottom: var(--space-2);"></div>
                    <div class="text-sm text-secondary">Success</div>
                </div>
                <div class="card card-compact">
                    <div style="width: 100%; height: 60px; background-color: var(--color-error); border-radius: var(--radius-md); margin-bottom: var(--space-2);"></div>
                    <div class="text-sm text-secondary">Error</div>
                </div>
                <div class="card card-compact">
                    <div style="width: 100%; height: 60px; background-color: var(--color-warning); border-radius: var(--radius-md); margin-bottom: var(--space-2);"></div>
                    <div class="text-sm text-secondary">Warning</div>
                </div>
            </div>
        </section>

        <!-- Typography -->
        <section class="demo-section">
            <h2 class="demo-section-title">Typography</h2>
            <div class="card">
                <h1>Heading 1</h1>
                <h2>Heading 2</h2>
                <h3>Heading 3</h3>
                <h4>Heading 4</h4>
                <p>This is a paragraph with <a href="#">a link</a> and some <strong>bold text</strong>.</p>
                <p class="text-secondary">Secondary text color for less emphasis.</p>
            </div>
        </section>

        <!-- Buttons -->
        <section class="demo-section">
            <h2 class="demo-section-title">Buttons</h2>
            <div class="card">
                <div style="display: flex; gap: var(--space-3); flex-wrap: wrap; margin-bottom: var(--space-6);">
                    <button class="btn btn-primary">Primary</button>
                    <button class="btn btn-secondary">Secondary</button>
                    <button class="btn btn-success">Success</button>
                    <button class="btn btn-danger">Danger</button>
                    <button class="btn btn-outline">Outline</button>
                    <button class="btn btn-ghost">Ghost</button>
                    <button class="btn btn-primary" disabled>Disabled</button>
                </div>
                <div style="display: flex; gap: var(--space-3); flex-wrap: wrap; align-items: center;">
                    <button class="btn btn-primary btn-lg">Large</button>
                    <button class="btn btn-primary">Default</button>
                    <button class="btn btn-primary btn-sm">Small</button>
                </div>
            </div>
        </section>

        <!-- Badges -->
        <section class="demo-section">
            <h2 class="demo-section-title">Badges</h2>
            <div class="card">
                <div style="display: flex; gap: var(--space-3); flex-wrap: wrap; align-items: center;">
                    <span class="badge badge-primary">Primary</span>
                    <span class="badge badge-success">Success</span>
                    <span class="badge badge-error">Error</span>
                    <span class="badge badge-warning">Warning</span>
                    <span class="badge badge-secondary">Secondary</span>
                    <span class="badge badge-online">ONLINE</span>
                    <span class="badge badge-offline">OFFLINE</span>
                    <span class="badge badge-primary badge-lg">Large Badge</span>
                </div>
            </div>
        </section>

        <!-- Cards -->
        <section class="demo-section">
            <h2 class="demo-section-title">Cards</h2>
            <div class="grid grid-cols-3">
                <div class="card">
                    <h4 class="card-title">Simple Card</h4>
                    <p>This is a basic card component with some content inside it.</p>
                </div>
                <div class="card card-interactive">
                    <h4 class="card-title">Interactive Card</h4>
                    <p>Hover over this card to see the interactive effect.</p>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Card with Header</h4>
                        <div class="card-subtitle">Subtitle text</div>
                    </div>
                    <div class="card-body">
                        <p>Card content goes here.</p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-primary btn-sm">Action</button>
                        <button class="btn btn-ghost btn-sm">Cancel</button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Game Cards -->
        <section class="demo-section">
            <h2 class="demo-section-title">Game Cards (Improved)</h2>
            <p class="text-secondary mb-4">Hover over cards to see enhanced interactions, favorite button, and action buttons</p>
            <div class="grid grid-cols-3">
                <!-- Card 1: Naomi with Favorite -->
                <div class="card game-card">
                    <div class="game-card-image-container">
                        <img src="img/naomi.jpg" alt="Naomi" class="game-card-image">
                        <button class="game-card-favorite is-favorite" title="Favorite" onclick="toggleFavorite(this)">
                            ★
                        </button>
                        <div class="game-card-overlay">
                            <div class="game-card-overlay-buttons">
                                <button class="btn btn-primary btn-sm">🎮 Launch</button>
                                <button class="btn btn-ghost btn-sm" style="color: white; border: 1px solid rgba(255,255,255,0.3);">ℹ Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="game-card-info">
                        <h4 class="game-card-title">Marvel vs Capcom 2</h4>
                        <div class="game-card-meta">
                            <span class="game-card-system-badge naomi">Naomi</span>
                            <span class="badge badge-secondary">Fighting</span>
                        </div>
                    </div>
                </div>
                
                <!-- Card 2: Chihiro -->
                <div class="card game-card">
                    <div class="game-card-image-container">
                        <img src="img/chihiro.jpg" alt="Chihiro" class="game-card-image">
                        <button class="game-card-favorite" title="Favorite" onclick="toggleFavorite(this)">
                            ☆
                        </button>
                        <div class="game-card-overlay">
                            <div class="game-card-overlay-buttons">
                                <button class="btn btn-primary btn-sm">🎮 Launch</button>
                                <button class="btn btn-ghost btn-sm" style="color: white; border: 1px solid rgba(255,255,255,0.3);">ℹ Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="game-card-info">
                        <h4 class="game-card-title">OutRun 2</h4>
                        <div class="game-card-meta">
                            <span class="game-card-system-badge chihiro">Chihiro</span>
                            <span class="badge badge-secondary">Racing</span>
                        </div>
                    </div>
                </div>
                
                <!-- Card 3: Triforce with Favorite -->
                <div class="card game-card">
                    <div class="game-card-image-container">
                        <img src="img/triforce.jpg" alt="Triforce" class="game-card-image">
                        <button class="game-card-favorite is-favorite" title="Favorite" onclick="toggleFavorite(this)">
                            ★
                        </button>
                        <div class="game-card-overlay">
                            <div class="game-card-overlay-buttons">
                                <button class="btn btn-primary btn-sm">🎮 Launch</button>
                                <button class="btn btn-ghost btn-sm" style="color: white; border: 1px solid rgba(255,255,255,0.3);">ℹ Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="game-card-info">
                        <h4 class="game-card-title">F-Zero AX</h4>
                        <div class="game-card-meta">
                            <span class="game-card-system-badge triforce">Triforce</span>
                            <span class="badge badge-secondary">Racing</span>
                        </div>
                    </div>
                </div>
                
                <!-- Card 4: Naomi 2 -->
                <div class="card game-card">
                    <div class="game-card-image-container">
                        <img src="img/Sega Naomi 2.jpg" alt="Naomi 2" class="game-card-image">
                        <button class="game-card-favorite" title="Favorite" onclick="toggleFavorite(this)">
                            ☆
                        </button>
                        <div class="game-card-overlay">
                            <div class="game-card-overlay-buttons">
                                <button class="btn btn-primary btn-sm">🎮 Launch</button>
                                <button class="btn btn-ghost btn-sm" style="color: white; border: 1px solid rgba(255,255,255,0.3);">ℹ Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="game-card-info">
                        <h4 class="game-card-title">Virtua Fighter 4</h4>
                        <div class="game-card-meta">
                            <span class="game-card-system-badge naomi2">Naomi 2</span>
                            <span class="badge badge-secondary">Fighting</span>
                        </div>
                    </div>
                </div>
                
                <!-- Card 5: Atomiswave -->
                <div class="card game-card">
                    <div class="game-card-image-container">
                        <img src="img/atomiswave.jpg" alt="Atomiswave" class="game-card-image">
                        <button class="game-card-favorite" title="Favorite" onclick="toggleFavorite(this)">
                            ☆
                        </button>
                        <div class="game-card-overlay">
                            <div class="game-card-overlay-buttons">
                                <button class="btn btn-primary btn-sm">🎮 Launch</button>
                                <button class="btn btn-ghost btn-sm" style="color: white; border: 1px solid rgba(255,255,255,0.3);">ℹ Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="game-card-info">
                        <h4 class="game-card-title">Metal Slug 6</h4>
                        <div class="game-card-meta">
                            <span class="game-card-system-badge atomiswave">Atomiswave</span>
                            <span class="badge badge-secondary">Run & Gun</span>
                        </div>
                    </div>
                </div>
                
                <!-- Card 6: Long Title Example -->
                <div class="card game-card">
                    <div class="game-card-image-container">
                        <img src="img/naomi.jpg" alt="Naomi" class="game-card-image">
                        <button class="game-card-favorite" title="Favorite" onclick="toggleFavorite(this)">
                            ☆
                        </button>
                        <div class="game-card-overlay">
                            <div class="game-card-overlay-buttons">
                                <button class="btn btn-primary btn-sm">🎮 Launch</button>
                                <button class="btn btn-ghost btn-sm" style="color: white; border: 1px solid rgba(255,255,255,0.3);">ℹ Info</button>
                            </div>
                        </div>
                    </div>
                    <div class="game-card-info">
                        <h4 class="game-card-title">The King of Fighters XI</h4>
                        <div class="game-card-meta">
                            <span class="game-card-system-badge atomiswave">Atomiswave</span>
                            <span class="badge badge-secondary">Fighting</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Forms -->
        <section class="demo-section">
            <h2 class="demo-section-title">Forms</h2>
            <div class="grid grid-cols-2">
                <div class="card">
                    <h4 class="card-title mb-4">Form Elements</h4>
                    <div class="form-group">
                        <label class="form-label form-label-required">Name</label>
                        <input type="text" class="form-input" placeholder="Enter name">
                    </div>
                    <div class="form-group">
                        <label class="form-label">IP Address</label>
                        <input type="text" class="form-input is-valid" value="192.168.1.100">
                        <span class="form-success">✓ Valid IP address</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">System Type</label>
                        <select class="form-select">
                            <option>Sega Naomi</option>
                            <option>Sega Naomi 2</option>
                            <option>Sega Chihiro</option>
                            <option>Sega Triforce</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="check1" checked>
                        <label class="form-check-label" for="check1">Enable OpenJVS</label>
                    </div>
                </div>
                <div class="card">
                    <h4 class="card-title mb-4">Validation States</h4>
                    <div class="form-group">
                        <label class="form-label">Valid Input</label>
                        <input type="text" class="form-input is-valid" value="Valid value">
                        <span class="form-success">✓ This field is valid</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Invalid Input</label>
                        <input type="text" class="form-input is-invalid" value="Invalid">
                        <span class="form-error">✗ This field has an error</span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">With Help Text</label>
                        <input type="text" class="form-input" placeholder="Enter value">
                        <span class="form-help">Help text explaining the field</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Alerts -->
        <section class="demo-section">
            <h2 class="demo-section-title">Alerts</h2>
            <div class="alert alert-info">
                <strong>Info:</strong> This is an informational message.
            </div>
            <div class="alert alert-success">
                <strong>Success:</strong> Operation completed successfully!
            </div>
            <div class="alert alert-warning">
                <strong>Warning:</strong> Please review before continuing.
            </div>
            <div class="alert alert-error">
                <strong>Error:</strong> Something went wrong.
            </div>
        </section>

        <!-- Loading States -->
        <section class="demo-section">
            <h2 class="demo-section-title">Loading States</h2>
            <div class="card">
                <div style="display: flex; gap: var(--space-6); align-items: center; margin-bottom: var(--space-6);">
                    <div class="loading-spinner-sm"></div>
                    <div class="loading-spinner"></div>
                    <div class="loading-spinner-lg"></div>
                </div>
                <div class="progress progress-lg mb-4">
                    <div class="progress-bar" style="width: 65%"></div>
                </div>
                <div class="progress mb-4">
                    <div class="progress-bar progress-bar-striped" style="width: 45%"></div>
                </div>
                <div class="skeleton skeleton-title"></div>
                <div class="skeleton skeleton-text"></div>
                <div class="skeleton skeleton-text"></div>
            </div>
        </section>

        <!-- Empty State -->
        <section class="demo-section">
            <h2 class="demo-section-title">Empty State</h2>
            <div class="card">
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <h3 class="empty-state-title">No Games Found</h3>
                    <p class="empty-state-message">
                        No ROM files were found in the /boot/roms directory. Add some games to get started.
                    </p>
                    <button class="btn btn-primary">Add Games</button>
                </div>
            </div>
        </section>

        <!-- Responsive Grid -->
        <section class="demo-section">
            <h2 class="demo-section-title">Responsive Grid</h2>
            <p class="text-secondary mb-4">Resize your browser to see the grid adapt (4 cols → 2 cols → 1 col)</p>
            <div class="grid grid-cols-4">
                <?php for($i = 1; $i <= 8; $i++): ?>
                <div class="card card-compact text-center">
                    <div style="padding: var(--space-6);">Grid Item <?php echo $i; ?></div>
                </div>
                <?php endfor; ?>
            </div>
        </section>

        <!-- Spacing Scale -->
        <section class="demo-section">
            <h2 class="demo-section-title">Spacing Scale</h2>
            <div class="card">
                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <div style="display: flex; align-items: center; gap: var(--space-4);">
                        <span class="text-sm text-secondary" style="width: 100px;">space-2 (8px)</span>
                        <div style="height: 24px; width: var(--space-2); background-color: var(--color-primary);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-4);">
                        <span class="text-sm text-secondary" style="width: 100px;">space-4 (16px)</span>
                        <div style="height: 24px; width: var(--space-4); background-color: var(--color-primary);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-4);">
                        <span class="text-sm text-secondary" style="width: 100px;">space-6 (24px)</span>
                        <div style="height: 24px; width: var(--space-6); background-color: var(--color-primary);"></div>
                    </div>
                    <div style="display: flex; align-items: center; gap: var(--space-4);">
                        <span class="text-sm text-secondary" style="width: 100px;">space-8 (32px)</span>
                        <div style="height: 24px; width: var(--space-8); background-color: var(--color-primary);"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Demo -->
        <section class="demo-section">
            <h2 class="demo-section-title">Interactive Components</h2>
            <div class="card">
                <button class="btn btn-primary" onclick="showToast()">Show Toast Notification</button>
                <button class="btn btn-secondary" onclick="showModal()">Show Modal</button>
            </div>
        </section>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Modal Demo -->
    <div id="demoModal" style="display: none;">
        <div class="modal-backdrop" onclick="hideModal()"></div>
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Launch Game</h3>
                <button class="modal-close" onclick="hideModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to launch <strong>Marvel vs Capcom 2</strong>?</p>
                <div class="form-group">
                    <label class="form-label">NetDIMM</label>
                    <select class="form-select">
                        <option>192.168.1.100 (Player 1)</option>
                        <option>192.168.1.101 (Player 2)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-ghost" onclick="hideModal()">Cancel</button>
                <button class="btn btn-primary">Launch</button>
            </div>
        </div>
    </div>

    <script>
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const currentTheme = html.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
        }
        
        // Toggle Favorite
        function toggleFavorite(button) {
            button.classList.toggle('is-favorite');
            if (button.classList.contains('is-favorite')) {
                button.textContent = '★';
            } else {
                button.textContent = '☆';
            }
        }

        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }

        // Toast Notification
        let toastCounter = 0;
        function showToast() {
            const types = ['success', 'error', 'warning', 'info'];
            const messages = [
                {title: 'Success!', message: 'Game launched successfully'},
                {title: 'Error', message: 'Failed to connect to NetDIMM'},
                {title: 'Warning', message: 'Low disk space detected'},
                {title: 'Info', message: 'New games available'}
            ];
            
            const type = types[toastCounter % types.length];
            const msg = messages[toastCounter % messages.length];
            toastCounter++;

            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <div class="toast-icon">${type === 'success' ? '✓' : type === 'error' ? '✗' : type === 'warning' ? '⚠' : 'ℹ'}</div>
                <div class="toast-content">
                    <div class="toast-title">${msg.title}</div>
                    <div class="toast-message">${msg.message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 5000);
        }

        // Modal
        function showModal() {
            document.getElementById('demoModal').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('demoModal').style.display = 'none';
        }
    </script>
</body>
</html>
