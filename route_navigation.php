<?php
// route_navigation.php - Included by dashboard.php

// Note: No HTML head/body here, this is injected into main content wrapper.

// Fetch buildings/nodes to populate dropdowns
$pdo = getDBConnection();
$nodes = $pdo->query("SELECT id, name FROM nodes ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    .routing-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        height: calc(100vh - 160px);
    }
    
    .routing-panel {
        background: var(--white);
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: var(--shadow);
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-group-route {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .form-group-route label {
        font-weight: 500;
        color: var(--text-main);
        font-size: 0.95rem;
    }

    .form-control-route {
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        font-family: inherit;
        color: var(--text-main);
        transition: border-color 0.3s;
        background-color: var(--bg);
    }

    .form-control-route:focus {
        outline: none;
        border-color: var(--accent);
    }

    .btn-route {
        background: var(--accent);
        color: var(--white);
        border: none;
        padding: 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-route:hover {
        background: var(--accent-hover);
        transform: translateY(-2px);
    }

    .btn-route:disabled {
        background: #94a3b8;
        cursor: not-allowed;
        transform: none;
    }

    .route-result {
        margin-top: auto;
        padding: 1rem;
        background: #f1f5f9;
        border-radius: 8px;
        display: none;
    }
    
    .route-result.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    .route-result h4 {
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    @media (max-width: 992px) {
        .routing-layout {
            grid-template-columns: 1fr;
            height: auto;
        }
        
        .map-container-inner {
            height: 400px;
        }
    }

    .loader {
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top: 3px solid var(--white);
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
        display: none;
    }
    
    .btn-route.loading .loader { display: block; }
    .btn-route.loading .btn-text { display: none; }
    
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
</style>

<div class="routing-layout">
    <!-- Panel -->
    <div class="routing-panel">
        <div>
            <h3 style="color: var(--primary); margin-bottom: 0.5rem;"><i class="fas fa-route" style="color: var(--accent);"></i> Route Planner</h3>
            <p style="color: var(--text-light); font-size: 0.9rem;">Select your current location and destination to find the shortest path.</p>
        </div>

        <form id="routeForm">
            <div class="form-group-route">
                <label for="startNode">Current Location</label>
                <select id="startNode" class="form-control-route" required>
                    <option value="" disabled selected>Select starting point...</option>
                    <?php foreach($nodes as $node): ?>
                        <option value="<?php echo htmlspecialchars($node['id']); ?>">
                            <?php echo htmlspecialchars($node['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group-route" style="text-align: center; color: var(--text-light);">
                <i class="fas fa-ellipsis-v"></i>
            </div>

            <div class="form-group-route">
                <label for="endNode">Destination</label>
                <select id="endNode" class="form-control-route" required>
                    <option value="" disabled selected>Select destination...</option>
                    <?php foreach($nodes as $node): ?>
                        <option value="<?php echo htmlspecialchars($node['id']); ?>">
                            <?php echo htmlspecialchars($node['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-route" id="findRouteBtn">
                <span class="btn-text">Find Shortest Route</span>
                <div class="loader"></div>
            </button>
        </form>

        <div class="route-result" id="routeResult">
            <h4>Route Information</h4>
            <div id="routeDetails" style="font-size: 0.9rem; color: var(--text-main);"></div>
        </div>
    </div>

    <!-- Map Reuse -->
    <!-- We inline the same map structure here for routing visualization. 
         Normally we might componentize this more, but we just want the SVG -->
    <div class="map-container-inner" id="mapWrapper" style="box-shadow: var(--shadow);">
        <svg id="campus-map" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid meet">
            <rect width="800" height="600" fill="#eef2f6" />
            <path d="M 100,100 L 200,150 L 300,150" class="path-line" />
            <path d="M 200,150 L 250,250 L 200,300" class="path-line" />
            <path d="M 250,250 L 400,250" class="path-line" />
            
            <rect id="bldg-admin" class="building" x="60" y="60" width="80" height="80" rx="8" />
            <polygon id="bldg-cs" class="building" points="260,110 340,110 340,190 300,210 260,190" />
            <circle id="bldg-library" class="building" cx="200" cy="300" r="50" />
            <rect id="bldg-student-center" class="building" x="350" y="200" width="100" height="100" rx="15" />
            
            <!-- Nodes logic for rendering routes -->
            <g id="route-layer"></g>

            <!-- Destination Marker -->
            <g id="dest-marker" style="display:none; transform-origin: center; animation: pulse 1.5s infinite;">
                <circle cx="0" cy="0" r="10" fill="var(--accent)" stroke="#fff" stroke-width="3" />
            </g>
        </svg>

        <div class="map-controls">
            <button class="map-btn" id="zoomIn" title="Zoom In"><i class="fas fa-plus"></i></button>
            <button class="map-btn" id="zoomOut" title="Zoom Out"><i class="fas fa-minus"></i></button>
            <button class="map-btn" id="resetMap" title="Reset View"><i class="fas fa-sync-alt"></i></button>
        </div>
    </div>
</div>

<script src="assets/js/map.js"></script>
<script src="assets/js/routing.js"></script>
<style>
    @keyframes pulse {
        0% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(6, 182, 212, 0.7)); }
        50% { transform: scale(1.2); filter: drop-shadow(0 0 10px rgba(6, 182, 212, 0)); }
        100% { transform: scale(1); filter: drop-shadow(0 0 0 rgba(6, 182, 212, 0)); }
    }
</style>
