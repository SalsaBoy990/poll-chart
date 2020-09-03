jQuery(document).ready(function ($) {
  // search for the current form, btn, id field
  var currentVoteBtnId;
  var currentVoteFormId;
  var currentVoteErrorId;
  var currentId;

  // regex select if multiple forms are present in one page
  var getVoteBtns = $('[id^="voteBtnShortcode"]');

  console.log(getVoteBtns);
  // if multiple polls are present, register click events to all of them
  if (getVoteBtns.length > 1) {
    for(var i = 0; i < getVoteBtns.length; i++) {
      var element = getVoteBtns[i].attributes[1].value;
      

      currentVoteBtnId = "#voteBtnShortcode" + element;
      currentVoteFormId = "#pollChart" + element;
      currentVoteErrorId = "#ag-poll-form-error" + element;
      currentId = parseInt(element, 10);

      console.log(currentVoteBtnId);
      console.log(currentVoteFormId);


      // send ajax request
      agSendInPollData(currentId, currentVoteBtnId, currentVoteFormId, currentVoteErrorId);
    }
  } else {
    // if only one poll is present
    currentVoteBtnId = "#voteBtnShortcode" + getVoteBtns.attr("data-vote");
    currentVoteFormId = "#pollChart" + getVoteBtns.attr("data-vote");
    currentVoteErrorId = "#ag-poll-form-error" + getVoteBtns.attr("data-vote");
    currentId = parseInt(getVoteBtns.attr("data-vote"), 10);

    // send ajax request
    agSendInPollData(currentId, currentVoteBtnId, currentVoteFormId, currentVoteErrorId);
  }

  
  function agSendInPollData(currentId, currentVoteBtnId, currentVoteFormId, currentVoteErrorId) {
    // ajax request on the current vote btn
    $(currentVoteBtnId).on("click", function () {
      var myVote = $('input[name="choices"]:checked', currentVoteFormId).val();
      // console.log(myVote)

      // if vote is undefined, do not send the request
      if (!myVote) {
        return;
      }

      var data = {
        action: "ag_poll_chart_ajax_action",
        vote: myVote,
        id: currentId,
        security: AGPollChartAjax.security,
      };

      // AJAX POST
      $.ajax({
        type: "POST",
        url: AGPollChartAjax.ajax_url,
        data: data,
        dataType: "json",
      })
        .done(function (response) {
          console.log(response);

          var question = response.data.question;
          var voteResult = response.data.chartData;
          console.log(response.data);

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

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.BarChart(
              document.getElementById("pollChart" + currentId)
            );
            chart.draw(chartData, options);
          }
        })
        .fail(function () {
          $(currentVoteErrorId).html("Error");
        });
    });
  }
});
