document.getElementById("Visbut").onclick = function() {
    // Textos y datos numéricos
    document.getElementById("titlecur").value = document.getElementById("nombrecur").value;
    document.getElementById("curdesc").value = document.getElementById("Desc").value;
    document.getElementById("hours").value = document.getElementById("curnum").value;
    document.getElementById("precio").value = document.getElementById("curplata").value;

    // Cargar la imagen seleccionada en tiempo real
    const inputFile = document.getElementById("Dataso");
    const imagenPrevia = document.getElementById("curso");

    if (inputFile.files && inputFile.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            imagenPrevia.src = e.target.result;
        }
        reader.readAsDataURL(inputFile.files[0]);
    }

    // Visualización del precio y símbolos
    const btnclick = document.getElementById("Visbut");
    const precieishon = document.getElementById("Prec");
    const simbolito = document.getElementById("peso");

    if (btnclick && precieishon) {
        if (precieishon.style.display == "none" || precieishon.style.display == "" && simbolito.style.display == "none" || simbolito.style.display == "") {
            precieishon.style.display = "block";
            simbolito.style.display = "block";
        }
    }
};