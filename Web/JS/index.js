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
}
