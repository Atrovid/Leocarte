let dataCurse;
let donneeHoraireDebut;
let donneeHoraireFin;

window.addEventListener('load', () => {
    document.getElementById("envoyer").addEventListener("click", recuperer);
})

function recuperer() {
    dataCurse = document.getElementById("matiere_cours").value;
    donneeHoraireDebut = document.getElementById("horaire_debut").value;
    donneeHoraireFin = document.getElementById("horaire_fin").value;
    var form = document.getElementById("Formulaire");
    form.remove();
    var invisible = document.getElementById("invisible");
    invisible.style.visibility = "visible";
    createNewPage();
}

function createNewPage() {
    infomationOnTheCurse();
}

function infomationOnTheCurse() {
    var info = document.getElementById("informations");
    var dl = document.createElement("dl");
    info.appendChild(dl);
    var dt = document.createElement("dt");
    dl.appendChild(dt);
    dt.appendChild(document.createTextNode("Matière de cours : "));
    var dd = document.createElement("dd");
    dl.appendChild(dd);
    dd.appendChild(document.createTextNode(dataCurse));

    dt = document.createElement("dt");
    dl.appendChild(dt);
    dt.appendChild(document.createTextNode("Horaire de début : "));
    dd = document.createElement("dd");
    dl.appendChild(dd);
    dd.appendChild(document.createTextNode(donneeHoraireDebut));

    dt = document.createElement("dt");
    dl.appendChild(dt);
    dt.appendChild(document.createTextNode("Horaire de fin : "));
    dd = document.createElement("dd");
    dl.appendChild(dd);
    dd.appendChild(document.createTextNode(donneeHoraireFin));
}