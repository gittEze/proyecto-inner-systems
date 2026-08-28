
document.getElementById("Visbut").onclick = function() {

    document.getElementById("titlecur").value = document.getElementById("nombrecur").value;

    document.getElementById("curdesc").value = document.getElementById("Desc").value;

    document.getElementById("hours").value= document.getElementById("curnum").value
    
    document.getElementById("precio").value= document.getElementById("curplata").value

    const btnclick  = document.getElementById("Visbut");

    const precieishon =document.getElementById("Prec");

    const simbolito = document.getElementById("peso")

    
if( btnclick && precieishon){

        if(precieishon.style.display == "none" || precieishon.style.display == "" && simbolito.style.display == "none" || simbolito.style.display == "")

        precieishon.style.display = "block"
        simbolito.style.display = "block"
};



}

