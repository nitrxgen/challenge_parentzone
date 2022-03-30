
// i was going to use a OOP approach to this but i decided against it
// because the data shown is reloaded after every search/filtration
// and there's no real manipulation of data going on, so i'm keeping it
// simple and classic, just load the data, put data into table, no OOP
// plus OOP requires some time to design, procedural approach works at
// the moment as i'm making all the steps and functionality up

let mainusers;
let search_timeout;

function data_reset() {
  let inputs = document.querySelectorAll('div#mainusers input');
  for(var i=0;i<inputs.length;i++)
    inputs[i].value = '';

  clear_table();
  document.querySelector('div#filters input:nth-child(1)').disabled = true;
  document.querySelector('div#filters input:nth-child(2)').disabled = true;
  document.querySelector('div#status').innerText = '';
}

function data_load(query) {
  // yes yes, i know putting things like this into a GET request is bad and
  // POST should be used but i'm running out of time and just need to finish
  fetch('userlist.php?output=json&filters='+query).then(response=> {
    if(!response.ok)
      throw new Error('HTTP Error: ${response.status}');
    return response.json();
  }).then(json=>{
    clear_table();
    for(var i=0;i<json.results.length;i++)
      table_insert(json.results[i]);
    document.querySelector('div#status').innerText = "SQL Query:\n"+json.metadata.rawquery;
  });
}

// made this function to dedicate the responsibility of inserting rows to a table here
function table_insert(data) {
  let table_row = document.createElement('DIV');
  let table_cell;
  table_row.className = 'table_row';
  for(var i = 0;i<data.length;i++) {
    table_cell = document.createElement('DIV');
    table_cell.className = 'table_cell';
    table_cell.appendChild(document.createTextNode(data[i]));
    table_row.appendChild(table_cell);
  }
  mainusers.appendChild(table_row);
}

function clear_table() {
  // loop through all the child elements in the main table, minus 2
  // because of the header and filter rows
  for(var i=mainusers.childNodes.length-1;i>=2;i--)
    mainusers.removeChild(mainusers.childNodes[i]);
}

function data_export() {
  let inputs = document.querySelectorAll('div#mainusers input[name^=input_]');
  let finalquery = '';
  for(var i=0;i<inputs.length;i++) {
    let paramvalue = encodeURIComponent(inputs[i].value.toString());
    if(paramvalue!=='')
      finalquery+= inputs[i].name.substr(6) + '=' + paramvalue + '&';
  }

  // encode all of the parameters ready to be used in the api call
  finalquery = encodeURIComponent(finalquery);

  // redirect the browser to download the CSV contents produced in PHP
  window.location.href = 'userlist.php?output=csv&filters='+finalquery;
}

function begin_filtration() {
  // just being fancy, check if all filters are empty, if so then
  // disable the "reset filters" button, but also collect their values
  // for when we execute the search
  let inputs = document.querySelectorAll('div#mainusers input[name^=input_]');
  let inputlen = 0;
  let finalquery = '';

  document.querySelector('div#status').innerText = '';
  for(var i=0;i<inputs.length;i++) {
    inputlen+= inputs[i].value.toString().length;
    let paramvalue = encodeURIComponent(inputs[i].value.toString());
    if(paramvalue!=='')
      finalquery+= inputs[i].name.substr(6) + '=' + paramvalue + '&';
  }

  // encode all of the parameters ready to be used in the api call
  finalquery = encodeURIComponent(finalquery);

  // provide the choice to use buttons if there's text somewhere
  document.querySelector('div#filters input:nth-child(1)').disabled = inputlen==0;
  document.querySelector('div#filters input:nth-child(2)').disabled = inputlen==0;

  // create a 500ms timeout after every key press to load the new data
  clearTimeout(search_timeout);
  search_timeout = setTimeout(()=>{
    data_load(finalquery);
  }, 500);
}

function ready() {
  mainusers = document.querySelector('div#mainusers');
  let table_row;
  let table_cell;
  let table_input;

  // generate the initial table showing column names and filter inputs
  fetch('userlist.php?output=json').then(response=> {
    if(!response.ok)
      throw new Error('HTTP Error: ${response.status}');
    return response.json();
  }).then(json=>{

    // empty the div of loading text and any other contaminant
    while(mainusers.firstChild)
      mainusers.removeChild(mainusers.firstChild);

    // create the titled columns of the table
    table_row = document.createElement('DIV');
    table_row.className = 'table_row';
    for(var i=0;i<json.metadata.columns.length;i++) {
      table_cell = document.createElement('DIV');
      table_cell.className = 'table_cell';

      // create the text for the table columns
      table_cell.appendChild(document.createTextNode(json.metadata.columns[i]));
      table_row.appendChild(table_cell);
    }
    mainusers.appendChild(table_row);

    // create the filter inputs
    table_row = document.createElement('DIV');
    table_row.className = 'table_row';
    for(var i = 0;i<json.metadata.columns.length;i++) {
      table_cell = document.createElement('DIV');
      table_cell.className = 'table_cell';

      // create the input field in this cell
      table_input = document.createElement('INPUT');
      table_input.name = 'input_'+json.metadata.columns[i]; // used for filtering
      table_input.addEventListener('input', begin_filtration);
      table_input.placeholder = "\u{1F50E} ...";
      table_cell.appendChild(table_input);
      table_row.appendChild(table_cell);
    }
    mainusers.appendChild(table_row);
    document.querySelector('div#filters').style.display = 'block';
  });

  // first button is reset
  document.querySelector('div#filters input:nth-child(1)').addEventListener('click', data_reset);

  // second button is export
  document.querySelector('div#filters input:nth-child(2)').addEventListener('click', data_export);
}

document.addEventListener('DOMContentLoaded', ready);
