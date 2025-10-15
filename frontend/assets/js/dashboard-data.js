// === Sample dashboard podaci ===
const dashboardMetrics = {
  totalRecipes: 124,
  totalUsers: 842,
  avgRating: 4.5,
  todaysViews: 732
};

const recentRecipes = [
  { id: 1, title: "Chocolate Cake", category: "Dessert" },
  { id: 2, title: "Grilled Salmon", category: "Dinner" },
  { id: 3, title: "Avocado Toast", category: "Breakfast" },
  { id: 4, title: "Chicken Tikka Masala", category: "Dinner" }
];

function getDashboardData() {
  return new Promise(resolve => 
    setTimeout(() => resolve({ metrics: dashboardMetrics, recent: recentRecipes }), 150)
  );
}

// === Render funkcija ===
function renderDashboard() {
  getDashboardData().then(data => {
    // Popuni metrike
    $("#metricRecipes").text(data.metrics.totalRecipes);
    $("#metricUsers").text(data.metrics.totalUsers);
    $("#metricRating").text(data.metrics.avgRating.toFixed(1));
    $("#metricViews").text(data.metrics.todaysViews);

    // Popuni listu recepata
    const $list = $("#recentList");
    $list.empty();
    data.recent.forEach(r => {
      $list.append(`
        <li class="mb-2">
          <i class="bi bi-bookmark text-primary me-2"></i>
          <strong>${r.title}</strong>
          <small class="text-muted">(${r.category})</small>
        </li>
      `);
    });

    // Poboljšani grafikon
    Highcharts.chart("chartContainer", {
      chart: {
        type: "area",
        backgroundColor: "#ffffff",
        spacing: [20, 20, 20, 20]
      },
      title: {
        text: "Website Views (Past 7 Days)",
        align: "left",
        style: {
          fontSize: "16px",
          fontWeight: "600"
        }
      },
      subtitle: {
        text: "Today: " + data.metrics.todaysViews + " views",
        align: "left",
        style: {
          color: "#666666"
        }
      },
      xAxis: {
        categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
        tickmarkPlacement: "on",
        title: {
          text: null
        }
      },
      yAxis: {
        title: {
          text: "Views"
        },
        labels: {
          formatter: function () {
            return this.value;
          }
        }
      },
      tooltip: {
        split: true,
        valueSuffix: " views"
      },
      plotOptions: {
        area: {
          stacking: "normal",
          lineColor: "#0d6efd",
          lineWidth: 2,
          marker: {
            enabled: false
          }
        }
      },
      series: [{
        name: "Views",
        data: [650, 720, 690, 810, 790, 870, data.metrics.todaysViews],
        color: "#0d6efd",
        fillOpacity: 0.3
      }],
      credits: { enabled: false }
    });

  });
}
