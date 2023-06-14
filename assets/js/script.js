// Mendapatkan halaman yang sedang dibuka
var currentPath = window.location.pathname;

// Menambahkan kelas 'active' pada elemen nav-link yang sesuai
$('.nav-link').each(function() {
  var href = $(this).attr('href');
  if (href === currentPath) {
    $(this).addClass('active');
  }
});

function resetForm() {
  document.getElementById("username").value = "";
  document.getElementById("password").value = "";
}

function validateForm() {
  var firstname = document.forms["myForm"]["firstname"].value;
  var lastname = document.forms["myForm"]["lastname"].value;
  var nim = document.forms["myForm"]["nim"].value;
  var semester = document.forms["myForm"]["semester"].value;
  var programstudi = document.forms["myForm"]["programstudi"].value;
  var ukm = document.forms["myForm"]["ukm"].value;
  var email = document.forms["myForm"]["email"].value;

  if (firstname == "") {
    alert("Nama depan tidak boleh kosong");
    return false;
  }
  if (lastname == "") {
    alert("Nama belakang tidak boleh kosong");
    return false;
  }
  if (nim == "") {
    alert("NIM tidak boleh kosong");
    return false;
  }
  if (semester == "") {
    alert("Semester tidak boleh kosong");
    return false;
  }
  if (programstudi == "") {
    alert("Program studi tidak boleh kosong");
    return false;
  }
  if (ukm == "") {
    alert("UKM tidak boleh kosong");
    return false;
  }
  if (email == "") {
    alert("Email tidak boleh kosong");
    return false;
  }
  if (email.indexOf("@") == -1) {
    alert("Email tidak valid");
    return false;
  }
  return true;
}
function validateForm() {
  var firstname = document.forms["myForm"]["firstname"].value;
  var lastname = document.forms["myForm"]["lastname"].value;
  var nim = document.forms["myForm"]["nim"].value;
  var semester = document.forms["myForm"]["semester"].value;
  var programstudi = document.forms["myForm"]["programstudi"].value;
  var ukm = document.forms["myForm"]["ukm"].value;
  var email = document.forms["myForm"]["email"].value;

  if (firstname == "") {
    alert("Nama depan tidak boleh kosong");
    return false;
  }
  if (lastname == "") {
    alert("Nama belakang tidak boleh kosong");
    return false;
  }
  if (nim == "") {
    alert("NIM tidak boleh kosong");
    return false;
  }
  if (semester == "") {
    alert("Semester tidak boleh kosong");
    return false;
  }
  if (programstudi == "") {
    alert("Program studi tidak boleh kosong");
    return false;
  }
  if (ukm == "") {
    alert("UKM tidak boleh kosong");
    return false;
  }
  if (email == "") {
    alert("Email tidak boleh kosong");
    return false;
  }
  if (email.indexOf("@") == -1) {
    alert("Email tidak valid");
    return false;
  }
  return true;
}
