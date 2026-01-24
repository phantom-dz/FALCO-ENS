(function () {
  function drawBarChart(svg, data) {
    if (!svg || !data.length) {
      return;
    }
    const width = svg.clientWidth || 300;
    const height = svg.clientHeight || 180;
    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    const max = Math.max(...data.map((d) => d.value), 1);
    const barWidth = width / data.length - 12;
    svg.innerHTML = '';
    data.forEach((d, index) => {
      const barHeight = (d.value / max) * (height - 30);
      const x = index * (barWidth + 12) + 6;
      const y = height - barHeight - 20;
      const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
      rect.setAttribute('x', x);
      rect.setAttribute('y', y);
      rect.setAttribute('width', barWidth);
      rect.setAttribute('height', barHeight);
      rect.setAttribute('fill', '#3a6ff8');
      svg.appendChild(rect);
    });
  }

  function drawSparkline(svg, data) {
    if (!svg || !data.length) {
      return;
    }
    const width = svg.clientWidth || 300;
    const height = svg.clientHeight || 180;
    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
    const max = Math.max(...data, 1);
    const step = width / (data.length - 1);
    const points = data
      .map((value, index) => {
        const x = index * step;
        const y = height - (value / max) * (height - 20) - 10;
        return `${x},${y}`;
      })
      .join(' ');
    svg.innerHTML = '';
    const polyline = document.createElementNS('http://www.w3.org/2000/svg', 'polyline');
    polyline.setAttribute('points', points);
    polyline.setAttribute('fill', 'none');
    polyline.setAttribute('stroke', '#16a085');
    polyline.setAttribute('stroke-width', '3');
    svg.appendChild(polyline);
  }

  document.addEventListener('cem-chart-data', (event) => {
    const { yearProgress, activity } = event.detail;
    drawBarChart(document.querySelector('[data-chart="year-progress"]'), yearProgress || []);
    drawSparkline(document.querySelector('[data-chart="activity"]'), activity || []);
  });
})();
