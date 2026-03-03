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

    .map-switcher {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 10;
        background: var(--white);
        padding: 5px;
        border-radius: 8px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        display: flex;
        gap: 5px;
    }

    .map-switcher select {
        padding: 8px 12px;
        border: 1px solid var(--border);
        border-radius: 4px;
        font-family: inherit;
        font-weight: 500;
        color: var(--text-main);
        outline: none;
        cursor: pointer;
    }

    /* SVG Styling */
    .building {
        fill: rgba(148, 163, 184, 0.4); /* Make them semi-transparent over the image */
        stroke: #334155;
        stroke-width: 2;
        transition: fill 0.3s, filter 0.3s;
        cursor: pointer;
    }

    .building:hover {
        fill: rgba(6, 182, 212, 0.6); /* Cyan hover */
        filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.2));
    }

    .building.active {
        fill: rgba(245, 158, 11, 0.7); /* Orange */
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
        
        <div class="map-switcher">
            <select id="floorSelect">
                <option value="campus.jpg" data-w="1200" data-h="800">Campus Layout</option>
                <option value="ground_floor.jpg" data-w="800" data-h="1200">Building B - Ground Floor</option>
                <option value="first_floor.jpg" data-w="800" data-h="1200">Building B - First Floor</option>
                <option value="second_floor.jpg" data-w="800" data-h="1200">Building B - Second Floor</option>
                <option value="third_floor.jpg" data-w="800" data-h="1200">Building B - Third Floor</option>
            </select>
        </div>

        <div class="map-tooltip" id="mapTooltip">
            <div class="tooltip-title" id="tooltipTitle">Building Name</div>
            <div id="tooltipDesc">Description</div>
        </div>
        
        <!-- SVG Canvas representing the Campus -->
        <!-- viewBox matches the intrinsic resolution of the background image -->
        <svg id="campus-map" viewBox="0 0 1200 800" preserveAspectRatio="xMidYMid meet">
            <!-- Background Image Layer -->
            <!-- Note: The user MUST place images in assets/images/ with these names -->
            <image id="map-bg-image" href="assets/images/campus.jpg" width="1200" height="800" x="0" y="0" />
            
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
