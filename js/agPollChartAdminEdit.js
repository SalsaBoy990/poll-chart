jQuery(document).ready(function ($) {
  $("#ag-poll-add-choice-edit-btn").on("click", function () {
    var myChoices = $("#ag-poll-choices-edit");
    // console.log(myChoices)

    // get last input field's position number
    var lastInput = $("#ag-poll-choices-edit input:last-of-type");

    var position = parseInt(lastInput.attr("data-number"), 10);
    console.log(position);

    position++;

    // generate new input field with incremented position number
    var newInput =
      '<label for="choices[' +
      position +
      ']">Choice ' +
      position +
      ":</label><br />";
    newInput +=
      '<input type="text" class="form-control large-text" name="choices[' +
      position +
      ']" value="" data-number="' +
      position +
      '" /><br />';

    myChoices.append(newInput);
  });

  if ($("#ag-poll-results-edit-page").length > 0) {
    // get current id
    var agPollId = $('input[name="pollid"]').val();
    agPollId = parseInt(agPollId, 10);

    var data = {
      action: "ag_poll_chart_admin_ajax_action",
      pollId: agPollId,
      security: AGPollChartAdminAjax.security,
    };

    $.ajax({
      type: "POST",
      url: AGPollChartAdminAjax.ajax_url,
      data: data,
      dataType: "json",
    })
      .done(function (response) {
        var voteResult = response.data.chartData;
        // console.log(response.data);

        if (response.success === false) {
          $("#ag-poll-results-edit-page").html(response.data);
          return;
        }

        // GOOGLE CHART
        // Load the Visualization API and the corechart package.
        google.charts.load("current", { packages: ["corechart"] });

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the bar chart, passes in the data and
        // draws it.
        function drawChart() {
          // Create the data table.
          var chartData = new google.visualization.DataTable();
          chartData.addColumn("string", "Categories");
          chartData.addColumn("number", "Number of votes");

          for (var index in voteResult) {
            chartData.addRow([index, voteResult[index]]);
          }

          // Set chart options
          var options = {
            title: "Poll results",
            width: 500,
            height: 500,
            legend: "bottom",
          };

          console.log("ag-poll-res-" + agPollId);

          // Instantiate and draw our chart, passing in some options.
          var chart = new google.visualization.BarChart(
            document.getElementById("ag-poll-results-edit-page")
          );
          chart.draw(chartData, options);
        }
      })
      .fail(function (response) {
        if (response.success === false) {
          $("#ag-poll-results-edit-page").html(response.data);
        }
      });
  }
});
