// assets/js/routing.js

document.addEventListener('DOMContentLoaded', () => {
    const routeForm = document.getElementById('routeForm');
    if (!routeForm) return;

    const startNode = document.getElementById('startNode');
    const endNode = document.getElementById('endNode');
    const btnSubmit = document.getElementById('findRouteBtn');
    const resultPanel = document.getElementById('routeResult');
    const routeDetails = document.getElementById('routeDetails');
    const routeLayer = document.getElementById('route-layer');
    const destMarker = document.getElementById('dest-marker');

    routeForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (startNode.value === endNode.value) {
            alert("Start and destination cannot be the same.");
            return;
        }

        btnSubmit.classList.add('loading');
        btnSubmit.disabled = true;
        resultPanel.classList.remove('active');
        routeLayer.innerHTML = '';
        destMarker.style.display = 'none';

        try {
            const res = await fetch(`api/get_route.php?start=${startNode.value}&end=${endNode.value}`);
            const data = await res.json();

            if (data.success) {
                // Show Result Panel
                resultPanel.classList.add('active');

                let stepsHtml = `<div style="margin-bottom: 10px;"><strong>Total Distance:</strong> ${data.distance} units</div>`;
                stepsHtml += `<div style="font-weight: 600; margin-bottom: 5px;">Route Steps:</div>`;
                stepsHtml += `<ul style="list-style: decimal; padding-left: 20px;">`;
                data.steps.forEach(step => {
                    stepsHtml += `<li>${step}</li>`;
                });
                stepsHtml += `</ul>`;

                routeDetails.innerHTML = stepsHtml;

                // Draw SVG Path
                const pathElement = document.createElementNS("http://www.w3.org/2000/svg", "path");
                pathElement.setAttribute("d", data.svg_path);
                pathElement.setAttribute("class", "animated-route");

                // Calculate dash array dynamically based on path length if possible,
                // but we use CSS animation which assumes 1000 length. For a real app,
                // we'd measure pathElement.getTotalLength() after appending and set dash array via JS.
                routeLayer.appendChild(pathElement);

                // Quick hack to animate the stroke correctly
                setTimeout(() => {
                    const length = pathElement.getTotalLength();
                    pathElement.style.strokeDasharray = length;
                    pathElement.style.strokeDashoffset = length;

                    // Trigger animation
                    pathElement.animate([
                        { strokeDashoffset: length },
                        { strokeDashoffset: 0 }
                    ], {
                        duration: 1500,
                        fill: 'forwards',
                        easing: 'ease-out'
                    });
                }, 50);

                // Show Destination Marker
                destMarker.setAttribute("transform", `translate(${data.dest_x}, ${data.dest_y})`);
                destMarker.style.display = 'block';

            } else {
                alert(data.error || "Could not calculate route.");
            }
        } catch (error) {
            console.error('Routing Error:', error);
            alert("A server error occurred while fetching the route.");
        } finally {
            btnSubmit.classList.remove('loading');
            btnSubmit.disabled = false;
        }
    });
});
