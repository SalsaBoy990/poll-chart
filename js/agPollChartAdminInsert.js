jQuery(document).ready(function ($) {
  $("#ag-poll-add-choice-insert-btn").on("click", function () {
    var myChoices = $("#ag-poll-choices-insert");
    // console.log(myChoices)

    // get last input field's position number
    var lastInput = $("#ag-poll-choices-insert input:last-of-type");
    
    var position = parseInt( lastInput.attr('data-number'), 10 );
    console.log(position);

    position++;
    
    // generate new input field with incremented position number
    var newInput = '<label for="choices[' + position + ']">Choice ' + position + ':</label><br />';
    newInput += '<input type="text" class="form-control large-text" name="choices[' + position + ']" value="" data-number="' + position + '" /><br />';
    
    myChoices.append(newInput);
  });
});
