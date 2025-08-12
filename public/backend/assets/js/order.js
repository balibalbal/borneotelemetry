/**
 * Charts ChartsJS
 */
"use strict";

(function () {
    // Color Variables
    const purpleColor = "#836AF9",
        yellowColor = "#ffe800",
        cyanColor = "#28dac6",
        orangeColor = "#FF8132",
        orangeLightColor = "#ffcf5c",
        oceanBlueColor = "#299AFF",
        greyColor = "#4F5D70",
        greyLightColor = "#EDF1F4",
        blueColor = "#2B9AFF",
        blueLightColor = "#84D0FF";

    let cardColor, headingColor, labelColor, borderColor, legendColor;

    if (isDarkStyle) {
        cardColor = config.colors_dark.cardColor;
        headingColor = config.colors_dark.headingColor;
        labelColor = config.colors_dark.textMuted;
        legendColor = config.colors_dark.bodyColor;
        borderColor = config.colors_dark.borderColor;
    } else {
        cardColor = config.colors.cardColor;
        headingColor = config.colors.headingColor;
        labelColor = config.colors.textMuted;
        legendColor = config.colors.bodyColor;
        borderColor = config.colors.borderColor;
    }

    // Set height according to their data-height
    // --------------------------------------------------------------------
    const chartList = document.querySelectorAll(".chartjs");
    chartList.forEach(function (chartListItem) {
        chartListItem.height = chartListItem.dataset.height;
    });

    // Line Chart
    // --------------------------------------------------------------------

    const lineChart = document.getElementById("lineChart");
    if (lineChart) {
        fetch("/chart-data")
            .then((response) => response.json())
            .then((data) => {
                const maxTotal = Math.max(...data.totals) + 2;
                const lineChartVar = new Chart(lineChart, {
                    type: "line",
                    data: {
                        labels: data.dates,
                        datasets: [
                            {
                                data: data.totals,
                                label: "Order Harian",
                                borderColor: config.colors.primary,
                                tension: 0.5,
                                backgroundColor: config.colors.primary,
                                fill: false,
                                pointRadius: 0, // Ubah radius titik menjadi 0 untuk menghilangkan titik standar
                                pointHoverRadius: 5,
                                pointHoverBorderWidth: 5,
                                pointBorderColor: "transparent",
                                pointHoverBorderColor: cardColor,
                                pointHoverBackgroundColor:
                                    config.colors.primary,
                                // Fungsi untuk menyesuaikan titik-titik yang akan ditampilkan sebagai lingkaran
                                pointStyle: function (context) {
                                    return "circle";
                                },
                                // Mengatur ukuran titik berdasarkan kondisi
                                pointRadius: function (context) {
                                    return 8;
                                },
                                // Mengatur warna titik berdasarkan kondisi
                                pointBackgroundColor: function (context) {
                                    return "red"; // Titik non-puncak tidak ditampilkan
                                },
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor,
                                },
                                ticks: {
                                    color: labelColor,
                                },
                                title: {
                                    display: true,
                                    text: "Tanggal Order", // Label sumbu X
                                    color: headingColor,
                                    font: {
                                        size: 14,
                                        weight: "bold",
                                    },
                                },
                            },
                            y: {
                                scaleLabel: {
                                    display: true,
                                },
                                min: 0,
                                max: maxTotal,
                                ticks: {
                                    color: labelColor,
                                    stepSize: 30,
                                },
                                title: {
                                    display: true,
                                    text: "Total Order", // Label sumbu Y
                                    color: headingColor,
                                    font: {
                                        size: 14,
                                        weight: "bold",
                                    },
                                },
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor,
                                },
                            },
                        },
                        plugins: {
                            tooltip: {
                                // Updated default tooltip UI
                                rtl: isRtl,
                                backgroundColor: cardColor,
                                titleColor: headingColor,
                                bodyColor: legendColor,
                                borderWidth: 1,
                                borderColor: borderColor,
                            },
                            legend: {
                                position: "top",
                                align: "start",
                                rtl: isRtl,
                                labels: {
                                    font: {
                                        family: "Inter",
                                    },
                                    usePointStyle: true,
                                    padding: 35,
                                    boxWidth: 6,
                                    boxHeight: 6,
                                    color: legendColor,
                                },
                            },
                        },
                    },
                });
            })
            .catch((error) =>
                console.error("Error fetching chart data:", error)
            );
    }

    // Bar Chart
    // --------------------------------------------------------------------
    const barChart = document.getElementById("barChart");
    if (barChart) {
        fetch("/chart-data")
            .then((response) => response.json())
            .then((data) => {
                const maxTotal = Math.max(...data.totals) + 20; // Ambil nilai tertinggi dan tambahkan 20

                const barChartVar = new Chart(barChart, {
                    type: "bar", // Tipe chart utama
                    data: {
                        labels: data.dates,
                        datasets: [
                            {
                                type: "bar", // Dataset pertama untuk bar chart
                                data: data.totals,
                                backgroundColor: orangeLightColor,
                                borderColor: "transparent",
                                maxBarThickness: 15,
                                borderRadius: {
                                    topRight: 15,
                                    topLeft: 15,
                                },
                            },
                            {
                                type: "line", // Dataset kedua untuk line chart
                                data: data.totals,
                                borderColor: "rgba(75, 192, 192, 1)",
                                borderWidth: 2,
                                fill: false,
                                pointRadius: 0, // Menghilangkan titik pada garis
                                cubicInterpolationMode: "monotone", // Menyeting kurva menjadi lebih lembut
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 500,
                        },
                        plugins: {
                            tooltip: {
                                rtl: isRtl,
                                backgroundColor: cardColor,
                                titleColor: headingColor,
                                bodyColor: legendColor,
                                borderWidth: 1,
                                borderColor: borderColor,
                            },
                            legend: {
                                display: false,
                            },
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor,
                                },
                                ticks: {
                                    color: labelColor,
                                },
                                title: {
                                    display: true,
                                    text: "Tanggal", // Label sumbu X
                                    color: headingColor,
                                    font: {
                                        size: 14,
                                        weight: "bold",
                                    },
                                },
                            },
                            y: {
                                min: 0,
                                max: maxTotal, // Set nilai maksimum berdasarkan nilai tertinggi + 20
                                grid: {
                                    color: borderColor,
                                    drawBorder: false,
                                    borderColor: borderColor,
                                },
                                ticks: {
                                    stepSize: 30,
                                    color: labelColor,
                                },
                                title: {
                                    display: true,
                                    text: "Jumlah Order", // Label sumbu Y
                                    color: headingColor,
                                    font: {
                                        size: 14,
                                        weight: "bold",
                                    },
                                },
                            },
                        },
                    },
                });
            })
            .catch((error) =>
                console.error("Error fetching bar chart data:", error)
            );
    }

    // Polar Chart
    // --------------------------------------------------------------------

    const polarChart = document.getElementById("polarChart");
    if (polarChart) {
        fetch("/chart-bon-hutang")
            .then((response) => response.json())
            .then((data) => {
                const monthNames = [
                    "Januari",
                    "Februari",
                    "Maret",
                    "April",
                    "Mei",
                    "Juni",
                    "Juli",
                    "Agustus",
                    "September",
                    "Oktober",
                    "November",
                    "Desember",
                ];

                // Inisialisasi data dengan 0 untuk setiap bulan
                const chartData = new Array(12).fill(0);

                // Isi data berdasarkan respons dari server
                data.months.forEach((month, index) => {
                    chartData[month - 1] = data.totals[index];
                });

                const polarChartVar = new Chart(polarChart, {
                    type: "polarArea",
                    data: {
                        labels: monthNames,
                        datasets: [
                            {
                                label: "Total Hutang",
                                backgroundColor: [
                                    "#4bc0c0", // Januari
                                    "#ffce56", // Februari
                                    "#ff6384", // Maret
                                    "#36a2eb", // April
                                    "#9966ff", // Mei
                                    "#ff9f40", // Juni
                                    "#63ff84", // Juli
                                    "#a2eb36", // Agustus
                                    "#ce56ff", // September
                                    "#050C9C", // Oktober
                                    "#c0c04b", // November
                                    "#1A5319", // Desember
                                ],
                                data: chartData,
                                borderWidth: 0,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 500,
                        },
                        scales: {
                            r: {
                                ticks: {
                                    display: false,
                                    color: labelColor,
                                },
                                grid: {
                                    display: false,
                                },
                            },
                        },
                        plugins: {
                            tooltip: {
                                // Updated default tooltip UI
                                rtl: isRtl,
                                backgroundColor: cardColor,
                                titleColor: headingColor,
                                bodyColor: legendColor,
                                borderWidth: 1,
                                borderColor: borderColor,
                            },
                            legend: {
                                rtl: isRtl,
                                position: "right",
                                labels: {
                                    usePointStyle: true,
                                    padding: 25,
                                    boxWidth: 8,
                                    boxHeight: 8,
                                    color: legendColor,
                                    font: {
                                        family: "Inter",
                                    },
                                },
                            },
                        },
                    },
                });
            })
            .catch((error) =>
                console.error("Error fetching bon hutang chart data:", error)
            );
    }
})();
