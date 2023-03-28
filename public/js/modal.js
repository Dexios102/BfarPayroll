
var modal = document.getElementById("addModal");
var btn = document.getElementById("addBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal
btn.onclick = function () {
    modal.style.display = "block";
};

// When the user clicks on <span> (x), close the modal
span.onclick = function () {
    modal.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
};


// CheckModal
var Cmodal = document.getElementById("checkModal");
var Cbtn = document.getElementById("checkBtn");

// Get the <span> element that closes the modal
var Cspan = document.getElementsByClassName("close2")[0];

// When the user clicks the button, open the modal
Cbtn.onclick = function () {
    Cmodal.style.display = "block";
};

// When the user clicks on <span> (x), close the modal
Cspan.onclick = function () {
    Cmodal.style.display = "none";
};

// When the user clicks anywhere outside of the modal, close it
window.onclick = function (event) {
    if (event.target == Cmodal) {
        Cmodal.style.display = "none";
    }
};







