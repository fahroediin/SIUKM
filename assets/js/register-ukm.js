
// Function to show snackbar
function showSnackbar() {
    var snackbar = document.getElementById("snackbar");
    snackbar.className = "show";
    setTimeout(function() {
        snackbar.className = snackbar.className.replace("show", "");
    }, 3000);
}


// Mendefinisikan fungsi JavaScript untuk memperbarui field nama_ukm
function updateNamaUKM(select) {
var id_ukm = select.value;
var nama_ukmField = document.getElementById("nama_ukm");
// Get the selected option element
var selectedOption = select.options[select.selectedIndex];
  // Mengirim permintaan AJAX ke server
  var xhttp = new XMLHttpRequest();
  xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      // Mengambil respons dari server
      var nama_ukm = this.responseText;

      // Mengatur nilai field nama_ukm dengan respons dari server
      nama_ukmField.value = selectedOption.text;
    }
  };
  xhttp.open("GET", "get_nama_ukm.php?id_ukm=" + id_ukm, true);
  xhttp.send();
}
  function showSnackbar() {
    var snackbar = document.getElementById("snackbar");
    snackbar.className = "show";
    setTimeout(function() {
      snackbar.className = snackbar.className.replace("show", "");
    }, 3000);
  }
function showPreview(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.style.display = "block";
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = "none";
    }
}
  // Mendapatkan elemen modal, tombol, dan span penutup modal
  var modal = document.getElementById("myModal");
  var btn = document.getElementById("modalButton");
  var span = document.getElementsByClassName("close")[0];

  btn.onclick = function() {
    modal.style.display = "block";
  }

  span.onclick = function() {
    modal.style.display = "none";
  }
  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
    function logout() {
        window.location.href = "?logout=true";
    }
const idUkmSelect = document.getElementById("id_ukm_dropdown");
const namaUkmField = document.getElementById("nama_ukm");

idUkmSelect.addEventListener("change", function() {
    const selectedOption = idUkmSelect.options[idUkmSelect.selectedIndex];
    const namaUkm = selectedOption.text; // Get the text of the selected option
    namaUkmField.value = namaUkm; // Set the value of the hidden input field
});
