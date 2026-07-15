"use strict";

(function () {
    document.addEventListener("DOMContentLoaded", function () {
        const weeklyOverviewChartEl = document.querySelector(
            "#weeklyOverviewChart",
        );
        if (!weeklyOverviewChartEl || !window.weeklyOverviewData) return;

        const semesterData = Array.isArray(window.weeklyOverviewData.semester)
            ? window.weeklyOverviewData.semester
            : [];
        const ipkData = Array.isArray(window.weeklyOverviewData.ipk)
            ? window.weeklyOverviewData.ipk.map((value) => Number(value))
            : [];

        if (!semesterData.length || !ipkData.length) return;

        const minIpk = Math.min(...ipkData);
        const maxIpk = Math.max(...ipkData);
        const range = maxIpk - minIpk;
        const padding = range > 0 ? Math.max(0.05, range * 0.4) : 0.15;

        let yMin = Math.max(0, Number((minIpk - padding).toFixed(2)));
        let yMax = Math.min(4, Number((maxIpk + padding).toFixed(2)));

        // Keep a minimum chart window so tiny differences remain visible.
        if (yMax - yMin < 0.3) {
            const mid = (minIpk + maxIpk) / 2;
            yMin = Math.max(0, Number((mid - 0.15).toFixed(2)));
            yMax = Math.min(4, Number((mid + 0.15).toFixed(2)));
        }

        const options = {
            chart: {
                type: "area",
                height: 300,
                toolbar: { show: false },
                zoom: { enabled: false },
                animations: {
                    enabled: true,
                    easing: "easeinout",
                    speed: 500,
                },
            },
            colors: ["#1064d1"],
            series: [
                {
                    name: "IPK",
                    data: ipkData,
                },
            ],
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 1,
                    opacityTo: 0.04,
                    stops: [0, 90, 100],
                },
            },
            grid: {
                borderColor: "#e7e7e7",
                strokeDashArray: 4,
                padding: {
                    top: 8,
                    right: 8,
                    bottom: 0,
                    left: 8,
                },
            },
            xaxis: {
                categories: semesterData,
                labels: {
                    rotate: -25,
                },
            },
            yaxis: {
                min: yMin,
                max: yMax,
                tickAmount: 6,
                labels: {
                    formatter: (val) => val.toFixed(2),
                },
            },
            stroke: {
                curve: "smooth",
                width: 4,
            },
            markers: {
                size: 6,
                hover: {
                    size: 9,
                },
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => val.toFixed(2),
                background: {
                    enabled: true,
                    borderRadius: 4,
                    borderWidth: 0,
                    foreColor: "#fff",
                },
            },
            tooltip: {
                y: {
                    formatter: (val) =>
                        `${val.toFixed(2)} (${(val * 25).toFixed(1)}%)`,
                },
            },
        };

        new ApexCharts(weeklyOverviewChartEl, options).render();
    });
})();
