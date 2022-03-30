
// i was only trying to up the count of input fields as requested
// i allow users to choose their gender of male and female, or they
// can input their own user-specified gender, so this function simply
// hides or shows the user-specified text field depending if they
// choose the 'other' option
function genderupdate() {
  const selectboi = document.querySelector('select[name=gender]');
  document.querySelector('div#usergender').style.display =
    selectboi.options[selectboi.selectedIndex].value=='other'?'block':'none';
}

// this function executes as the password field updates, and also
// is used when the form is validating
function checkpassword() {
  const password = document.querySelector('form#userreg input[name=password]').value;

  // a bit gimmicky, but the password hints on the form can change
  // style depending on what conditions it meets (currently underlines)
  document.querySelector('form#userreg span#pw_len').className = password.length>=8?'pw_fine':'';
  document.querySelector('form#userreg span#pw_uc').className = /[A-Z]/.test(password)?'pw_fine':'';
  document.querySelector('form#userreg span#pw_lc').className = /[a-z]/.test(password)?'pw_fine':'';
  document.querySelector('form#userreg span#pw_dig').className = /\d/.test(password)?'pw_fine':'';

  return /[A-Z]/.test(password) && /[a-z]/.test(password) && /\d/.test(password);
}

// this function handles actions prior to form submission such as
// validation and checking if the user agrees to the t&cs, we can
// prevent form POST submissions until everything is seemingly valid
function processform(e = false, onload = false) {

  // collect all the elements
  const el_firstname = document.querySelector('form#userreg input[name=firstname]');
  const el_lastname  = document.querySelector('form#userreg input[name=lastname]');
  const el_email     = document.querySelector('form#userreg input[name=email]');
  const el_password  = document.querySelector('form#userreg input[name=password]');
  const el_password2 = document.querySelector('form#userreg input[name=password2]');
  const el_tac       = document.querySelector('form#userreg input[name=tac]');
  let el_focus, valid_firstname, valid_lastname, valid_email, valid_password, valid_password2, valid_tac;

  // if this function is called by document onload, don't highlight the input errors
  // give the user a chance to put in data before we validate it, not on page load
  if(!onload) {

    // check if the user's first name has 3 letters in there at least
    document.querySelector('#hint_firstname').style.visibility = 
      (valid_firstname = /[A-Za-z]{3}/.test(el_firstname.value))?'hidden':'visible';

    // now last name
    document.querySelector('#hint_lastname').style.visibility = 
      (valid_lastname = /[A-Za-z]{3}/.test(el_lastname.value))?'hidden':'visible';

    // validate the email address
    document.querySelector('#hint_email').style.visibility = 
      (valid_email = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(el_email.value))?'hidden':'visible';

    // we can check the password, most of this is done with checkpassword()
    valid_password = checkpassword();

    // check both password fields are equal
    document.querySelector('#hint_password2').style.visibility = 
    (valid_password2 = (el_password.value===el_password2.value))?'hidden':'visible';

    // check they agreed to terms and conditions
    document.querySelector('#hint_tac').style.visibility = 
      (valid_tac = el_tac.checked)?'hidden':'visible';

    // check the validity backwards so we set focus to the top-most element
    if(!valid_tac)
      el_focus = el_tac;

    if(!valid_password2)
      el_focus = el_password2;

    if(!valid_password)
      el_focus = el_password;

    if(!valid_email)
      el_focus = el_email;

    if(!valid_lastname)
      el_focus = el_lastname;

    if(!valid_firstname)
      el_focus = el_firstname;

    // if el_focus has been set, then that basically says the form needs attention
    // if it's not set, we can proceed with the POST request
    if(el_focus) {
      el_focus.focus();
      if(e)
        e.preventDefault();
    }
  }
}

function ready() {
  document.querySelector('input[name=password]').addEventListener('keyup', checkpassword);
  document.querySelector('select[name=gender]').addEventListener('change', genderupdate);
  document.querySelector('form#userreg').addEventListener('submit', (e)=> processform(e));

  // if the form was submitted somehow bypassing javascript validation, we can
  // run the processform function to check input fields, highlight errors, it
  // will not submit the form
  processform(false, true);

  // similarly, we handle the dynamic visibility of the custom gender field on load
  genderupdate();
}
document.addEventListener('DOMContentLoaded', ready);
