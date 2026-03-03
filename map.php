<?php
// map.php - Included by dashboard.php

// Note: No HTML head/body here, this is injected into main content wrapper.
?>
<style>
    .map-container-inner {
        width: 100%;
        height: calc(100vh - 160px); /* Fill remaining space below topbar (approx) */
        background: #eef2f6;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
        cursor: grab;
    }

    .map-container-inner:active {
        cursor: grabbing;
    }

    #campus-map {
        width: 100%;
        height: 100%;
        transform-origin: 0 0;
    }

    .map-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: var(--white);
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }

    .map-btn {
        width: 40px;
        height: 40px;
        border: none;
        background: var(--bg);
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-main);
        transition: all 0.2s;
    }

    .map-btn:hover {
        background: var(--accent);
        color: var(--white);
    }

    /* SVG Styling */
    .building {
        fill: #94a3b8;
        stroke: #334155;
        stroke-width: 2;
        transition: fill 0.3s, filter 0.3s;
        cursor: pointer;
    }

    .building:hover {
        fill: var(--accent);
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.2));
    }

    .building.active {
        fill: #f59e0b; /* Orange for active/selected */
        stroke: #b45309;
        stroke-width: 3;
    }

    .path-line {
        stroke: rgba(148, 163, 184, 0.4);
        stroke-width: 4;
        fill: none;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Active route */
    .animated-route {
        stroke: #10b981; /* Green */
        stroke-width: 8;
        fill: none;
        stroke-dasharray: 1000;
        stroke-dashoffset: 1000;
        animation: drawPath 3s linear forwards;
    }

    @keyframes drawPath {
        to { stroke-dashoffset: 0; }
    }

    /* Tooltip */
    .map-tooltip {
        position: absolute;
        background: var(--primary);
        color: var(--white);
        padding: 10px 15px;
        border-radius: 8px;
        font-size: 0.9rem;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
        z-index: 100;
        transform: translate(-50%, -100%);
        margin-top: -15px;
    }
    
    .map-tooltip::after {
        content: '';
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -6px;
        border-width: 6px;
        border-style: solid;
        border-color: var(--primary) transparent transparent transparent;
    }

    .tooltip-title {
        font-weight: 600;
        margin-bottom: 4px;
        color: var(--accent);
    }
</style>

<div class="dashboard-card" style="margin-bottom: 0; padding: 1rem;">
    <div class="map-container-inner" id="mapWrapper">
        <div class="map-tooltip" id="mapTooltip">
            <div class="tooltip-title" id="tooltipTitle">Building Name</div>
            <div id="tooltipDesc">Description</div>
        </div>
        
        <!-- SVG Canvas representing the Campus -->
        <!-- viewBox 0 0 800 600 -->
        <svg id="campus-map" viewBox="0 0 800 600" preserveAspectRatio="xMidYMid meet">
            <!-- Background Layer -->
            <rect width="800" height="600" fill="#eef2f6" />
            
            <!-- Roads/Paths (Visual only, not logical routing edges) -->
            <path d="M 100,100 L 200,150 L 300,150" class="path-line" />
            <path d="M 200,150 L 250,250 L 200,300" class="path-line" />
            <path d="M 250,250 L 400,250" class="path-line" />
            
            <!-- Buildings (IDs match our database 'svg_id') -->
            <!-- Main Administration (bldg-admin) around 100,100 -->
            <rect id="bldg-admin" class="building" x="60" y="60" width="80" height="80" rx="8" 
                  data-name="Main Administration" data-desc="Administrative offices and Registrar" />
            
            <!-- CS Dept (bldg-cs) around 300,150 -->
            <polygon id="bldg-cs" class="building" points="260,110 340,110 340,190 300,210 260,190" 
                     data-name="Computer Science Dept" data-desc="CS Dept, Labs and Faculty offices" />
            
            <!-- Library (bldg-library) around 200,300 -->
            <circle id="bldg-library" class="building" cx="200" cy="300" r="50" 
                    data-name="Central Library" data-desc="Main campus library spanning 3 floors" />
            
            <!-- Student Center (bldg-student-center) around 400,250 -->
            <rect id="bldg-student-center" class="building" x="350" y="200" width="100" height="100" rx="15" 
                  data-name="Student Center" data-desc="Cafeteria, recreation, and student union" />
             
            <!-- A dynamic group for drawing routes -->
            <g id="route-layer"></g>
        </svg>

        <div class="map-controls">
            <button class="map-btn" id="zoomIn" title="Zoom In"><i class="fas fa-plus"></i></button>
            <button class="map-btn" id="zoomOut" title="Zoom Out"><i class="fas fa-minus"></i></button>
            <button class="map-btn" id="resetMap" title="Reset View"><i class="fas fa-sync-alt"></i></button>
        </div>
    </div>
</div>

<script src="assets/js/map.js"></script>
