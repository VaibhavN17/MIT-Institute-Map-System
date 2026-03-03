// assets/js/map.js

document.addEventListener('DOMContentLoaded', () => {
    const svgMap = document.getElementById('campus-map');
    const mapWrapper = document.getElementById('mapWrapper');

    if (!svgMap || !mapWrapper) return;

    // --- State variables for Pan/Zoom ---
    let isPanning = false;
    let startPoint = { x: 0, y: 0 };
    let endPoint = { x: 0, y: 0 };
    let scale = 1;
    let viewBox = { x: 0, y: 0, w: 800, h: 600 };
    const originalViewBox = { x: 0, y: 0, w: 800, h: 600 };

    // Set initial viewBox
    updateViewBox();

    function updateViewBox() {
        svgMap.setAttribute('viewBox', `${viewBox.x} ${viewBox.y} ${viewBox.w} ${viewBox.h}`);
    }

    // --- Mouse Wheel Zoom ---
    mapWrapper.addEventListener('wheel', (e) => {
        e.preventDefault();

        // Calculate point under mouse before zoom
        const pt = getPointFromEvent(e);
        const mapPtX = viewBox.x + (pt.x / mapWrapper.clientWidth) * viewBox.w;
        const mapPtY = viewBox.y + (pt.y / mapWrapper.clientHeight) * viewBox.h;

        const zoomFactor = -e.deltaY > 0 ? 0.9 : 1.1; // Zoom in / Out

        // Don't zoom out too much or zoom in too much
        if ((viewBox.w * zoomFactor > originalViewBox.w * 2) || (viewBox.w * zoomFactor < 200)) return;

        viewBox.w *= zoomFactor;
        viewBox.h *= zoomFactor;

        // Adjust x and y so the point under mouse stays in same screen pos
        viewBox.x = mapPtX - (pt.x / mapWrapper.clientWidth) * viewBox.w;
        viewBox.y = mapPtY - (pt.y / mapWrapper.clientHeight) * viewBox.h;

        updateViewBox();
    });

    // --- Panning ---
    mapWrapper.addEventListener('mousedown', (e) => {
        // Prevent panning if clicking a button inside wrapper
        if (e.target.closest('.map-controls')) return;

        isPanning = true;
        startPoint = { x: e.clientX, y: e.clientY };
    });

    mapWrapper.addEventListener('mousemove', (e) => {
        if (!isPanning) return;

        endPoint = { x: e.clientX, y: e.clientY };
        const dx = (startPoint.x - endPoint.x) / mapWrapper.clientWidth * viewBox.w;
        const dy = (startPoint.y - endPoint.y) / mapWrapper.clientHeight * viewBox.h;

        viewBox.x += dx;
        viewBox.y += dy;

        startPoint = { x: e.clientX, y: e.clientY };
        updateViewBox();
    });

    const stopPanning = () => { isPanning = false; };
    mapWrapper.addEventListener('mouseup', stopPanning);
    mapWrapper.addEventListener('mouseleave', stopPanning);

    // --- Button Controls ---
    document.getElementById('zoomIn').addEventListener('click', () => {
        viewBox.w *= 0.8; viewBox.h *= 0.8;
        // Keep centered
        viewBox.x += (viewBox.w / 0.8 - viewBox.w) / 2;
        viewBox.y += (viewBox.h / 0.8 - viewBox.h) / 2;
        updateViewBox();
    });

    document.getElementById('zoomOut').addEventListener('click', () => {
        viewBox.w *= 1.25; viewBox.h *= 1.25;
        viewBox.x -= (viewBox.w - viewBox.w / 1.25) / 2;
        viewBox.y -= (viewBox.h - viewBox.h / 1.25) / 2;
        updateViewBox();
    });

    document.getElementById('resetMap').addEventListener('click', () => {
        viewBox = { ...originalViewBox };
        updateViewBox();
        // Clear highlights and routes
        document.querySelectorAll('.building.active').forEach(b => b.classList.remove('active'));
        document.getElementById('route-layer').innerHTML = '';
        tooltip.style.opacity = '0';
    });

    function getPointFromEvent(e) {
        const rect = mapWrapper.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        }
    }

    // --- Map Tooltips & Click Logic ---
    const tooltip = document.getElementById('mapTooltip');
    const tooltipTitle = document.getElementById('tooltipTitle');
    const tooltipDesc = document.getElementById('tooltipDesc');
    const buildings = document.querySelectorAll('.building');

    buildings.forEach(building => {
        building.addEventListener('mousemove', (e) => {
            // Since we're hovering over a scaled/panned SVG, get viewport position
            const rect = mapWrapper.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            tooltipTitle.textContent = building.getAttribute('data-name');
            tooltipDesc.textContent = building.getAttribute('data-desc');

            tooltip.style.left = `${x}px`;
            tooltip.style.top = `${y}px`;
            tooltip.style.opacity = '1';
        });

        building.addEventListener('mouseleave', () => {
            tooltip.style.opacity = '0';
        });

        building.addEventListener('click', (e) => {
            // Remove active from others
            buildings.forEach(b => b.classList.remove('active'));
            building.classList.add('active');

            // Note: If routing is integrated, clicking might set start or end point.
            // Right now we just animate/highlight.
        });
    });
});
